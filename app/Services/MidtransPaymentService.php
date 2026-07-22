<?php

namespace App\Services;

use App\Models\Invoice;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransPaymentService
{
    public function __construct()
    {
        $settings = app(\App\Settings\PaymentSettings::class);
        Config::$serverKey    = $settings->midtrans_server_key;
        Config::$clientKey    = $settings->midtrans_client_key;
        Config::$isProduction = $settings->midtrans_is_production;
        
        Config::$isSanitized  = true; // Always sanitized
        Config::$is3ds        = true; // Always 3ds
    }

    /**
     * Generate Snap Token & Payment Link untuk Invoice.
     * FIX: 
     *  - order_id diubah ke invoice_no (bukan invoice_number)
     *  - kolom customer: phone → whatsapp, tambahkan fallback email
     *  - custom_field1 = invoice->id untuk digunakan saat Webhook masuk
     */
    public function generatePaymentToken(Invoice $invoice): array
    {
        // Jika sudah ada token & URL yang valid, return cache
        if ($invoice->payment_token && $invoice->payment_url) {
            return [
                'token' => $invoice->payment_token,
                'url'   => $invoice->payment_url,
            ];
        }

        $customer = $invoice->customer;

        // Gunakan invoice_no sebagai order_id (lebih readable di dashboard Midtrans)
        // Tambahkan timestamp agar unik jika di-retry
        $orderId = ($invoice->invoice_no ?? 'INV-' . $invoice->id) . '-' . time();
        // Pastikan order_id tidak lebih dari 50 karakter (batasan Midtrans)
        if (strlen($orderId) > 50) {
            $orderId = substr($orderId, 0, 50);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $invoice->amount,
            ],
            'customer_details' => [
                'first_name' => $customer?->name ?? 'Pelanggan ARMEDIA',
                'email'      => 'noreply@armedia.id', // Pelanggan tidak punya email field
                'phone'      => $customer?->whatsapp ?? '000', // FIX: whatsapp, bukan phone
            ],
            'item_details' => [
                [
                    'id'       => 'paket-' . ($customer?->internet_package_id ?? 0),
                    'price'    => (int) $invoice->amount,
                    'quantity' => 1,
                    'name'     => $customer?->internetPackage?->nama_paket ?? 'Tagihan Internet Bulanan',
                ]
            ],
            // Sisipkan invoice_id agar webhook dapat menemukan tagihan yg tepat
            'custom_field1' => (string) $invoice->id,
            'custom_field2' => (string) ($customer?->id ?? ''),
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $baseUrl = Config::$isProduction
                ? 'https://app.midtrans.com/snap/v2/vtweb/'
                : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

            $paymentUrl = $baseUrl . $snapToken;

            // Simpan token & URL ke DB
            $invoice->update([
                'payment_token' => $snapToken,
                'payment_url'   => $paymentUrl,
            ]);

            return [
                'token' => $snapToken,
                'url'   => $paymentUrl,
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
