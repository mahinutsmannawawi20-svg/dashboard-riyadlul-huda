<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;
    protected $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send WhatsApp message to a specific number or comma-separated numbers (group/multiple)
     * 
     * @param string $target Phone number (e.g., '08123456789' or '628123456789')
     * @param string $message The message content
     * @return bool
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (!$this->token) {
            Log::warning('Fonnte token not set. Message not sent.');
            return false;
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->post($this->apiUrl, [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62', // Default to Indonesia
                ]);

            if ($response->successful()) {
                Log::info("Fonnte message sent to {$target}");
                return true;
            }

            Log::error('Fonnte API Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment notification to parent
     */
    public function notifyPaymentSuccess($targetPhone, $santriName, $amount, $monthName, $year)
    {
        $formattedAmount = number_format($amount, 0, ',', '.');
        
        $message = "*PEMBAYARAN DITERIMA* ✅\n\n";
        $message .= "Terima kasih, pembayaran syahriah telah kami terima dengan detail berikut:\n\n";
        $message .= "👤 Nama: $santriName\n";
        $message .= "📅 Bulan: $monthName $year\n";
        $message .= "💰 Nominal: Rp $formattedAmount\n";
        $message .= "✓ Status: LUNAS\n\n";
        $message .= "_Pesan otomatis dari Sistem Informasi Riyadlul Huda_";

        return $this->sendMessage($targetPhone, $message);
    }

    /**
     * Send deposit notification to parent
     */
    public function notifyDepositSuccess($targetPhone, $santriName, $amount)
    {
        $formattedAmount = number_format($amount, 0, ',', '.');
        
        $message = "*PEMBAYARAN DITERIMA (DEPOSIT)* 💰\n\n";
        $message .= "Pembayaran diterima kami simpan sebagai saldo/deposit (karena tidak ada tagihan tertunggak):\n\n";
        $message .= "👤 Nama: $santriName\n";
        $message .= "💰 Nominal: Rp $formattedAmount\n";
        $message .= "✓ Status: TERSIMPAN\n\n";
        $message .= "_Pesan otomatis dari Sistem Informasi Riyadlul Huda_";

        return $this->sendMessage($targetPhone, $message);
    }
    
    /**
     * Send generic notification
     */
    public function notify($target, $title, $body)
    {
        $message = "*$title*\n\n$body";
        return $this->sendMessage($target, $message);
    }
}
