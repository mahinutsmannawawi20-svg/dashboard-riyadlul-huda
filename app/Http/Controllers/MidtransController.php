<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MidtransService;
use App\Services\TelegramService;
use App\Services\FonnteService;
use App\Models\Santri;
use App\Models\Syahriah;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MidtransController extends Controller
{
    protected $midtransService;
    protected $telegramService;
    protected $fonnteService;

    public function __construct(MidtransService $midtrans, TelegramService $telegram, FonnteService $fonnte)
    {
        $this->midtransService = $midtrans;
        $this->telegramService = $telegram;
        $this->fonnteService = $fonnte;
    }

    /**
     * Generate VA for a Santri
     */
    public function generateVa(Request $request, Santri $santri)
    {
        // Check if already has VA
        if ($santri->virtual_account_number) {
            return back()->with('error', 'Santri already has a Virtual Account.');
        }

        // Call Service
        $response = $this->midtransService->createTransaction($santri);

        if ($response && isset($response['va_numbers'][0]['va_number'])) {
            $vaNumber = $response['va_numbers'][0]['va_number'];
            
            // Update Santri
            $santri->update(['virtual_account_number' => $vaNumber]);
            
            return back()->with('success', 'Virtual Account berhasil di-generate: ' . $vaNumber);
        } else if ($response && isset($response['permata_va_number'])) {
            // Fallback for Permata
            $vaNumber = $response['permata_va_number'];
            $santri->update(['virtual_account_number' => $vaNumber]);
            return back()->with('success', 'Virtual Account berhasil di-generate: ' . $vaNumber);
        }

        return back()->with('error', 'Gagal generate VA dari Midtrans. Cek log atau pulsa/kuota API.');
    }

    /**
     * Reset VA (Clear VA Number) for a Santri
     */
    public function resetVa(Request $request, Santri $santri)
    {
        $santri->update(['virtual_account_number' => null]);
        return back()->with('success', 'Virtual Account berhasil di-reset. Silakan generate ulang untuk transaksi baru.');
    }

    /**
     * Bulk Generate VA for all Santri without VA
     */
    public function generateVaBulk(Request $request)
    {
        // Increase time limit for bulk process
        set_time_limit(300); // 5 minutes

        $santris = Santri::whereNull('virtual_account_number')
                         ->orWhere('virtual_account_number', '')
                         ->get();

        if ($santris->isEmpty()) {
            return back()->with('info', 'Semua santri sudah memiliki Virtual Account.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($santris as $santri) {
            try {
                // Determine nominal (using default)
                $response = $this->midtransService->createTransaction($santri);

                if ($response && isset($response['va_numbers'][0]['va_number'])) {
                    $santri->update(['virtual_account_number' => $response['va_numbers'][0]['va_number']]);
                    $successCount++;
                } else if ($response && isset($response['permata_va_number'])) {
                     $santri->update(['virtual_account_number' => $response['permata_va_number']]);
                     $successCount++;
                } else {
                    $failCount++;
                }
                
                // Optional: Sleep to prevent rate rate limit if needed
                // sleep(1); 
            } catch (\Exception $e) {
                Log::error("Bulk VA Error for ID {$santri->id}: " . $e->getMessage());
                $failCount++;
            }
        }

        return back()->with('success', "Proses Selesai. Sukses: $successCount, Gagal: $failCount");
    }

    /**
     * Bulk Reset VA (Clear All VA Numbers)
     */
    public function resetVaBulk(Request $request)
    {
        // Update all santri set va to null
        Santri::query()->update(['virtual_account_number' => null]);
        
        return back()->with('success', 'Semua Virtual Account berhasil di-reset (dihapus). Silakan generate ulang massal jika diperlukan.');
    }

    /**
     * Handle incoming Webhook from Midtrans
     */
    public function webhook(Request $request)
    {
        Log::info('Midtrans Webhook Received:', $request->all());

        $notification = $request->all();
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $signatureKey = $notification['signature_key'];
        $transactionStatus = $notification['transaction_status'];
        $type = $notification['payment_type'];
        $fraudStatus = $notification['fraud_status'] ?? null;

        // 1. Verify Signature
        if (!$this->midtransService->isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning("Midtrans Invalid Signature: $orderId");
            return response()->json(['message' => 'Invalid Signature'], 400);
        }

        // 2. Log Transaction to payment_gateway table
        // Extract NIS from Order ID (Format: SPP-NIS-TIMESTAMP)
        $parts = explode('-', $orderId);
        $nis = isset($parts[1]) ? $parts[1] : null;

        // Store log
        DB::table('payment_gateway')->updateOrInsert(
            ['order_id' => $orderId],
            [
                'payment_type' => $type,
                'transaction_status' => $transactionStatus,
                'gross_amount' => $grossAmount,
                'json_response' => json_encode($notification),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Process Status
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                // TODO: Set pending
            } else if ($fraudStatus == 'accept') {
                $this->handleSuccess($nis, $grossAmount);
            }
        } else if ($transactionStatus == 'settlement') {
            $this->handleSuccess($nis, $grossAmount);
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            // TODO: Handle Failed
        } else if ($transactionStatus == 'pending') {
            // TODO: Handle Pending
        }

        return response()->json(['message' => 'OK']);
    }

    private function handleSuccess($nis, $amount)
    {
        if (!$nis) return;

        $santri = Santri::where('nis', $nis)->first();
        if (!$santri) {
            Log::error("Midtrans Success for Unknown Santri NIS: $nis");
            return;
        }

        // SMART PAYMENT LOGIC: Find oldest unpaid month
        // We assume 1 transaction = 1 month payment logic for now
        $unpaidMonth = Syahriah::where('santri_id', $santri->id)
            ->where('is_lunas', false)
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->first();

        if ($unpaidMonth) {
            // Mark as Paid
            $unpaidMonth->update([
                'is_lunas' => true,
                'tanggal_bayar' => now(),
                'keterangan' => 'Lunas via Midtrans (Auto)',
                'nominal' => $amount // Record amount paid
            ]);

            // Format Month Name
            $monthName = \Carbon\Carbon::create()->month($unpaidMonth->bulan)->translatedFormat('F');
            $year = $unpaidMonth->tahun;


    
            // SMART NOTIFICATION (TAGIHAN)
            $message = "✅ **PEMBAYARAN DITERIMA**\n\n";
            $message .= "Terima kasih, pembayaran Syahriah untuk:\n";
            $message .= "Santri: **{$santri->nama_santri}**\n";
            $message .= "Bulan: **{$monthName} {$year}**\n";
            $message .= "Nominal: Rp " . number_format($amount, 0, ',', '.') . "\n";
            $message .= "Status: **LUNAS**\n\n";
            $message .= "_Pesan otomatis Dashboard Riyadlul Huda_";

            $this->telegramService->sendMessage($message);
            
            // WA NOTIFICATION via Fonnte
            if ($santri->no_hp_ortu_wali) {
                $this->fonnteService->notifyPaymentSuccess(
                    $santri->no_hp_ortu_wali, 
                    $santri->nama_santri, 
                    $amount, 
                    $monthName, 
                    $year
                );
            }
            
            Log::info("Payment Processed for Santri $nis - Month $monthName $year");
        } else {
            // NOTIFICATION (DEPOSIT / LEBIH BAYAR)
            $message = "💰 **PEMBAYARAN DITERIMA (DEPOSIT)**\n\n";
            $message .= "Pembayaran diterima tetapi tidak ada tagihan tertunggak:\n";
            $message .= "Santri: **{$santri->nama_santri}**\n";
            $message .= "Nominal: Rp " . number_format($amount, 0, ',', '.') . "\n";
            $message .= "Keterangan: Disimpan sebagai saldo/deposit.\n\n";
            $message .= "_Pesan otomatis Dashboard Riyadlul Huda_";

            $this->telegramService->sendMessage($message);

            // WA NOTIFICATION via Fonnte
            if ($santri->no_hp_ortu_wali) {
                $this->fonnteService->notifyDepositSuccess(
                    $santri->no_hp_ortu_wali, 
                    $santri->nama_santri, 
                    $amount
                );
            }

            Log::info("Payment Received for Santri $nis but NO Arrears found. Notification sent.");
        }
    }
}
