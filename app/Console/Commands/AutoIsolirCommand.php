<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\MikrotikService;
use App\Services\WhatsAppService;
use App\Enums\CustomerSubscriptionStatus;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoIsolirCommand extends Command
{
    protected $signature = 'armedia:auto-isolir {--dry-run : Jalankan tanpa aksi ke Mikrotik (hanya log)}';
    protected $description = 'Otomatis isolir pelanggan yang belum bayar tagihan melewati jatuh tempo';

    public function handle(MikrotikService $mikrotikService)
    {
        $this->info('Memulai pengecekan tagihan telat...');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('Menjalankan dalam mode DRY RUN. Tidak ada aksi isolir nyata ke Mikrotik.');
        }

        // Cari invoice berstatus BELUM BAYAR yang jatuh temponya sudah lewat
        // Asumsi due_date ada di invoice, atau jatuh tempo adalah akhir bulan dari period.
        $today = Carbon::today();
        
        // Ambil invoice bulan ini atau bulan sebelumnya yang belum lunas
        $overdueInvoices = Invoice::with('customer')
            ->where('status', InvoiceStatus::BELUM->value)
            ->where('due_date', '<', $today)
            ->whereHas('customer', function ($query) {
                $query->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value);
            })
            ->get();

        $isolatedCount = 0;

        foreach ($overdueInvoices as $invoice) {
            $customer = $invoice->customer;
            
            $this->info("Mengisolir Pelanggan: {$customer->name} (Tagihan: Rp" . number_format($invoice->amount, 0, ',', '.') . ")");
            
            if (!$isDryRun) {
                // Eksekusi Isolir Mikrotik
                $success = $mikrotikService->isolateCustomer($customer);
                
                if ($success) {
                    // Update status pelanggan
                    $customer->update(['subscription_status' => CustomerSubscriptionStatus::ISOLIR->value]);
                    
                    // Kirim Notifikasi WA
                    $waMsg = "⚠️ *PEMBERITAHUAN ISOLIR SEMENTARA*\n\n"
                           . "Yth. Bapak/Ibu {$customer->name},\n"
                           . "Layanan internet Anda telah dihentikan sementara (isolir) karena terdapat tagihan yang belum diselesaikan melewati tanggal jatuh tempo.\n\n"
                           . "Tagihan: *Rp " . number_format($invoice->amount, 0, ',', '.') . "*\n"
                           . "Periode: " . Carbon::parse($invoice->period)->translatedFormat('F Y') . "\n\n"
                           . "Silakan lakukan pembayaran untuk mengaktifkan kembali layanan internet Anda. Buka portal di: " . route('portal.login') . "\n\n"
                           . "Abaikan pesan ini jika Anda sudah melakukan pembayaran.\n"
                           . "Terima kasih.";
                           
                    if ($customer->whatsapp) {
                        WhatsAppService::sendMessage($customer->whatsapp, $waMsg);
                    }
                    
                    $isolatedCount++;
                } else {
                    $this->error("Gagal mengisolir {$customer->name} di Mikrotik.");
                    Log::error("AutoIsolir: Gagal memproses Mikrotik untuk pelanggan ID: {$customer->id}");
                }
            } else {
                $isolatedCount++;
            }
        }

        $this->info("✅ Proses selesai. Total pelanggan diisolir: {$isolatedCount}");
        return Command::SUCCESS;
    }
}
