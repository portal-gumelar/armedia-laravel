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
        Log::info('Midtrans Webhook Received:', $payload);

        $orderId           = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;

        // custom_field1 = invoice id yang kita sisipkan saat generate Snap Token
        $invoiceId = $payload['custom_field1'] ?? null;

        if (!$orderId || !$transactionStatus) {
            return response()->json(['message' => 'Invalid Payload'], 400);
        }

        // Cari Invoice
        $invoice = null;
        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
        }

        if (!$invoice) {
            // Fallback: cari via invoice_no (format: ARM-0001-260711.070000.ID)
            // order_id midtrans kita set dari invoice_no di MidtransPaymentService
            $invoice = Invoice::where('invoice_no', 'like', '%' . explode('-', $orderId)[0] . '%')->first();
        }

        if (!$invoice) {
            Log::warning("Midtrans Webhook: Invoice not found for order_id {$orderId}");
            return response()->json(['message' => 'Invoice Not Found'], 404);
        }

        // Mapping status Midtrans → InvoiceStatus Enum kita
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                // Ditandai perlu review manual — tetap belum
                Log::warning("Midtrans: payment challenged for invoice {$invoice->invoice_no}");
            } elseif ($fraudStatus === 'accept') {
                $invoice->update([
                    'status'         => InvoiceStatus::LUNAS->value,
                    'paid_at'        => now(),
                    'payment_method' => $paymentType ?? 'midtrans',
                ]);
            }
        } elseif ($transactionStatus === 'settlement') {
            $invoice->update([
                'status'         => InvoiceStatus::LUNAS->value,
                'paid_at'        => now(),
                'payment_method' => $paymentType ?? 'midtrans',
            ]);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            // Bersihkan token lama agar bisa generate ulang
            $invoice->update([
                'payment_token' => null,
                'payment_url'   => null,
            ]);
        } elseif ($transactionStatus === 'pending') {
            // Tidak ubah status, biarkan tetap 'belum' sambil menunggu konfirmasi
            Log::info("Midtrans: payment pending for invoice {$invoice->invoice_no}");
        }

        Log::info("Midtrans Webhook: Invoice {$invoice->invoice_no} → transaction_status: {$transactionStatus}");
        return response()->json(['message' => 'OK']);
    }
}
