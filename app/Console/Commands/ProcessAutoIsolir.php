<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Jobs\ProcessMikrotikIsolation;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Enums\CustomerSubscriptionStatus;

class ProcessAutoIsolir extends Command
{
    protected $signature = 'billing:auto-isolir';
    protected $description = 'Proses isolir otomatis ke Mikrotik untuk tagihan yang sudah lewat jatuh tempo';

    public function handle()
    {
        $this->info('Memulai pengecekan tagihan jatuh tempo untuk auto-isolir...');

        $today = Carbon::today();

        // Cari tagihan UNPAID yang due_date-nya kurang dari hari ini (sudah lewat jatuh tempo)
        $overdueInvoices = Invoice::where('status', \App\Enums\InvoiceStatus::BELUM->value)
            ->whereDate('due_date', '<', $today)
            ->with('customer')
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $customer = $invoice->customer;
            if ($customer && $customer->subscription_status !== CustomerSubscriptionStatus::ISOLIR) {
                // Update status di database
                $customer->update([
                    'subscription_status' => CustomerSubscriptionStatus::ISOLIR
                ]);

                // Dispatch job isolir jika ada server mikrotik
                if ($customer->mikrotik_server_id) {
                    ProcessMikrotikIsolation::dispatch($customer, 'isolate');
                }

                // Kirim notifikasi WA
                if ($customer->whatsapp) {
                    $paymentUrl = $invoice->payment_url ?? url('/pelanggan/bayar/'.$invoice->id);
                    $message = <<<MSG
⚠️ *PEMBERITAHUAN ISOLIR LAYANAN* ⚠️

Yth. Bapak/Ibu *{$customer->name}*,

Mohon maaf, layanan internet Anda saat ini *DIHENTIKAN SEMENTARA (Isolir)* karena sistem mendeteksi adanya tagihan yang telah melewati batas waktu pembayaran (Jatuh Tempo).

📄 *Nomor Invoice*: {$invoice->invoice_no}
💵 *Total Tagihan*: Rp {$invoice->amount}

Silakan lakukan pembayaran agar layanan internet Anda dapat otomatis aktif kembali.
Link Pembayaran & Portal:
🌐 {$paymentUrl}

Abaikan pesan ini jika Anda baru saja melakukan pembayaran. Terima kasih.
MSG;
                    WhatsAppService::sendMessage($customer->whatsapp, $message);
                }

                $count++;
                $this->info("Mengisolir pelanggan: {$customer->name}");
            }
        }

        $this->info("Proses selesai. {$count} pelanggan masuk antrean isolir.");
        Log::info("Auto Isolir: {$count} pelanggan diisolir.");
    }
}
