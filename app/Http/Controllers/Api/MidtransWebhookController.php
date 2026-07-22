<?php

namespace App\Http\Controllers\Api;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Menerima notifikasi webhook dari Midtrans.
     * FIX:
     *  - status diubah ke InvoiceStatus enum ('lunas', 'belum')
     *  - kolom invoice_number diubah ke invoice_no sesuai migrasi aktual
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        $orderId           = $payload['order_id'] ?? null;
        $statusCode        = $payload['status_code'] ?? null;
        $grossAmount       = $payload['gross_amount'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;
        $signatureKey      = $payload['signature_key'] ?? null;
        $invoiceId         = $payload['custom_field1'] ?? null;

        // 1. Validasi Keberadaan Data Minimal
        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid Payload'], 400);
        }

        // 2. Validasi Signature Key (SHA512)
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($expectedSignature !== $signatureKey) {
            Log::warning("Midtrans Webhook: Invalid Signature for Order ID {$orderId}");
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // 3. Simpan Raw Payload ke Log (Tabel baru)
        \App\Models\MidtransWebhookLog::create([
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payload' => $payload,
        ]);
        
        Log::info('Midtrans Webhook Valid Signature Received:', ['order_id' => $orderId]);

        // 4. Cari Invoice
        $invoice = null;
        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
        }

        if (!$invoice) {
            $invoice = Invoice::where('invoice_no', 'like', '%' . explode('-', $orderId)[0] . '%')->first();
        }

        if (!$invoice) {
            Log::warning("Midtrans Webhook: Invoice not found for order_id {$orderId}");
            return response()->json(['message' => 'Invoice Not Found'], 404);
        }

        // 5. Idempotency Check (Apakah sudah lunas?)
        if ($invoice->status === InvoiceStatus::LUNAS->value) {
            Log::info("Midtrans Webhook: Invoice {$invoice->invoice_no} is already LUNAS. Skipping.");
            return response()->json(['message' => 'Already Processed']);
        }

        // 6. Validasi Nominal Pembayaran (Gross Amount vs Invoice Total)
        // Midtrans mengirimkan gross_amount dengan format desimal e.g. "150000.00"
        if ((float) $grossAmount !== (float) $invoice->total_amount) {
            Log::warning("Midtrans Webhook: Amount mismatch for {$invoice->invoice_no}. Expected {$invoice->total_amount}, got {$grossAmount}.");
            // Bisa return error atau simpan log fraud
            return response()->json(['message' => 'Amount Mismatch'], 400);
        }

        // 7. Proses Status Pembayaran
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'challenge') {
                // Jangan lunas, masih butuh review manual Midtrans
                Log::warning("Midtrans: payment challenged for invoice {$invoice->invoice_no}");
            } else if ($fraudStatus == 'accept' || $transactionStatus == 'settlement') {
                $invoice->update([
                    'status'         => InvoiceStatus::LUNAS->value,
                    'paid_at'        => now(),
                    'payment_method' => $paymentType ?? 'midtrans',
                ]);
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $invoice->update([
                'payment_token' => null,
                'payment_url'   => null,
            ]);
        } elseif ($transactionStatus == 'pending') {
            Log::info("Midtrans: payment pending for invoice {$invoice->invoice_no}");
        }

        return response()->json(['message' => 'OK']);
    }
}
