<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceGeneratorService;
use App\Services\WhatsAppService;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoGenerateInvoicesCommand extends Command
{
    protected $signature = 'armedia:generate-invoices {period? : Periode tagihan (Y-m) contoh 2026-07}';
    protected $description = 'Generate tagihan (Invoice) untuk semua pelanggan aktif bulan ini dan kirim WA';

    public function handle(InvoiceGeneratorService $generatorService)
    {
        $periodArg = $this->argument('period');
        if (!$periodArg) {
            $periodArg = date('Y-m'); // Default bulan ini
        }

        $periodDate = Carbon::parse($periodArg . '-01');
        $this->info("Memulai pembuatan tagihan massal untuk periode: " . $periodDate->translatedFormat('F Y'));

        try {
            // Generate in DB
            $stats = $generatorService->generateForMonth($periodDate->toDateString());
            $this->info("Selesai generate DB. Created: {$stats['created']}, Updated: {$stats['updated']}, Skipped: {$stats['skipped']}");

            // Mengambil semua tagihan belum lunas untuk bulan ini yang baru saja dibuat/diupdate
            $invoices = Invoice::with('customer')
                ->where('period', $periodDate->toDateString())
                ->where('status', \App\Enums\InvoiceStatus::BELUM->value)
                ->get();

            $this->info("Menyiapkan pengiriman pesan WA untuk " . $invoices->count() . " pelanggan...");

            $sentCount = 0;
            foreach ($invoices as $invoice) {
                $customer = $invoice->customer;
                if (!$customer || empty($customer->whatsapp)) {
                    continue;
                }

                $monthName = $periodDate->translatedFormat('F Y');
                $amount = number_format($invoice->amount, 0, ',', '.');
                $dueDate = Carbon::parse($invoice->due_date)->translatedFormat('d F Y');
                $portalLink = url('/member');

                $waMsg = "Halo Bapak/Ibu *{$customer->name}*,\n\n"
                       . "Berikut adalah rincian tagihan internet ARMEDIA Anda untuk bulan *{$monthName}*:\n\n"
                       . "💳 *Total Tagihan:* Rp {$amount}\n"
                       . "🗓 *Jatuh Tempo:* {$dueDate}\n\n"
                       . "Untuk mempermudah pembayaran, silakan login ke portal member kami:\n"
                       . "👉 {$portalLink}\n\n"
                       . "Mohon lakukan pembayaran sebelum jatuh tempo agar layanan internet Anda tidak terganggu.\n"
                       . "Jika Anda sudah melakukan pembayaran, silakan abaikan pesan ini.\n\n"
                       . "Terima kasih,\n*ARMEDIA*";

                $success = WhatsAppService::sendMessage($customer->whatsapp, $waMsg);
                if ($success) {
                    $sentCount++;
                }

                // Jeda 2 detik antar pesan agar tidak diblokir WhatsApp (Rate Limiting)
                sleep(2);
            }

            $this->info("✅ Proses selesai! Berhasil mengirim {$sentCount} pesan WhatsApp.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: " . $e->getMessage());
            Log::error("AutoGenerateInvoicesCommand Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
