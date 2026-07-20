<?php

namespace App\Http\Controllers\Api;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;

class TypebotBridgeController extends Controller
{
    /**
     * Cari pelanggan berdasarkan Nomor WhatsApp
     * FIX: kolom `phone` diubah ke `whatsapp` sesuai skema DB aktual
     */
    public function getCustomerByPhone(Request $request)
    {
        $phone = $request->query('phone');
        if (!$phone) return response()->json(['error' => 'Phone required'], 400);

        // Normalisasi: hapus semua non-digit, strip leading 0/62
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = substr($cleanPhone, 2); // 628xxx → 8xxx
        } elseif (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1); // 08xxx → 8xxx
        }

        $customer = Customer::where('whatsapp', 'LIKE', "%{$cleanPhone}%")
            ->with(['internetPackage', 'village'])
            ->first();

        if (!$customer) {
            return response()->json(['found' => false, 'message' => 'Customer not found']);
        }

        return response()->json([
            'found'    => true,
            'customer' => [
                'id'         => $customer->id,
                'id_arm'     => $customer->id_arm,
                'name'       => $customer->name,
                'whatsapp'   => $customer->whatsapp,
                'package'    => $customer->internetPackage?->nama_paket,
                'village'    => $customer->village?->name,
                'status'     => $customer->subscription_status?->value ?? $customer->subscription_status,
            ]
        ]);
    }

    /**
     * Cek Tagihan Belum Lunas
     * FIX: status value diubah ke 'belum' sesuai InvoiceStatus Enum
     */
    public function getUnpaidInvoices($customerId)
    {
        $invoices = Invoice::where('customer_id', $customerId)
            ->where('status', InvoiceStatus::BELUM->value) // 'belum' bukan 'UNPAID'
            ->orderByDesc('period')
            ->get()
            ->map(fn($inv) => [
                'id'         => $inv->id,
                'invoice_no' => $inv->invoice_no,
                'period'     => $inv->period?->format('F Y'),
                'amount'     => $inv->amount,
                'due_date'   => $inv->due_date?->format('d M Y') ?? '10 bulan ini',
                'status'     => $inv->status?->value ?? $inv->status,
            ]);

        return response()->json([
            'count'    => $invoices->count(),
            'invoices' => $invoices,
        ]);
    }

    /**
     * Generate Link Pembayaran Midtrans via Typebot (AI Karyawan Kasir)
     */
    public function generatePaymentLink(Request $request, $invoiceId, MidtransPaymentService $midtransService)
    {
        $invoice = Invoice::with('customer')->find($invoiceId);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $statusVal = $invoice->status instanceof \App\Enums\InvoiceStatus
            ? $invoice->status->value
            : $invoice->status;

        if ($statusVal === InvoiceStatus::LUNAS->value) {
            return response()->json(['message' => 'Invoice sudah lunas', 'paid' => true]);
        }

        try {
            $paymentData = $midtransService->generatePaymentToken($invoice);
            return response()->json([
                'success'     => true,
                'payment_url' => $paymentData['url'],
                'token'       => $paymentData['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal generate link: ' . $e->getMessage()], 500);
        }
    }
}
