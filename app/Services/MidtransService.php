<?php

namespace App\Services;

use App\Models\Santri;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected $serverKey;
    protected $isProduction;
    protected $isSanitized;
    protected $is3ds;
    protected $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->isProduction = config('services.midtrans.is_production');
        $this->isSanitized = config('services.midtrans.is_sanitized', true);
        $this->is3ds = config('services.midtrans.is_3ds', true);

        $this->baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create a Fixed Virtual Account for a Santri (BCA/BNI/BRI supported by Core API usually)
     * Note: "Fixed VA" in Midtrans Core API is often treated as "Charge" with long expiry
     * OR using specific "Permata VA" / "BNI VA" endpoints if activated.
     * 
     * For "Closed Amount" Fixed VA that can be paid repeatedly, 
     * usually we register it via "Payment Link" or specific "Account Regulatory".
     * 
     * However, simpler approach for "Fixed VA" in many aggregators is:
     * Assign a specific VA number to the user (e.g. BNI + Mobile Number).
     * 
     * Since Midtrans "Core API" creates a transaction-based VA (one time),
     * To get a TRULY FIXED VA for Closed Amount that stays active:
     * We typically use "BNI RO" (Receiving Object) or similar persistent VA features 
     * which require Business plan.
     * 
     * AS A SIMPLIFICATION for "Zero to Hero":
     * We will use a "Trick": Create a Charge with 5-year expiry. 
     * BUT Midtrans VA is usually single-use. 
     * 
     * REAL FIXED VA requires "B2B" integration or "Subscription".
     * 
     * ADJUSTMENT FOR USER: 
     * We will implement the "Create Transaction" logic. 
     * When Santri clicks "Generate VA", we create a NEW Transaction (Order ID = Unique).
     * This acts like a "Ticket" to pay.
     * 
     * IF user wants ONE VA FOREVER: Users must activate "BNI PERMANENT VA" in Midtrans Dashboard.
     * Assuming they have that, we update Santri's VA Data.
     */
    public function createTransaction($santri, $nominal = 505000)
    {
        // Unique Order ID per transaction attempt
        $orderId = 'SPP-' . $santri->nis . '-' . time();

        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $nominal,
            ],
            'customer_details' => [
                'first_name' => $santri->nama_santri,
                'email' => 'santri@riyadlulhuda.com', // Dummy if not exists
                'phone' => $santri->no_hp_ortu_wali,
            ],
            'bank_transfer' => [
                'bank' => 'bri', // Defaulting to BRI as requested/common
                'va_number' => '123' . $santri->nis, // Try to request specific suffix if allowed
            ]
        ];

        // DUMMY MODE SEMENTARA (REQUEST USER)
        // Jika Server Key tidak ada atau kosong, generate fake response
        if (empty($this->serverKey)) {
            Log::warning("Midtrans Server Key is NULL. Returning DUMMY VA for Santri: {$santri->nama_santri}");
            
            // Mencoba membuat VA "Cantik" (8800 + NIS)
            $mockVa = '8800' . preg_replace('/\D/', '', $santri->nis); 
            if (strlen($mockVa) > 13) {
                 $mockVa = substr($mockVa, 0, 13);
            }

            return [
                'status_code' => '201',
                'transaction_status' => 'pending',
                'order_id' => $orderId,
                'gross_amount' => $nominal,
                'va_numbers' => [
                    [
                        'bank' => 'bri',
                        'va_number' => $mockVa
                    ]
                ]
            ];
        }

        // Send Request
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withBasicAuth($this->serverKey, '')
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->baseUrl . '/charge', $params);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Midtrans Charge Failed: ' . $response->body());
        return null;
    }

    /**
     * Verify Webhook Signature matches
     */
    public function isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)
    {
        $input = $orderId . $statusCode . $grossAmount . $this->serverKey;
        $calculated = hash('sha512', $input);
        return $calculated === $signatureKey;
    }
}
