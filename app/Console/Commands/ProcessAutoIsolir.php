<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Jobs\ProcessMikrotikIsolation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
            if ($customer && $customer->mikrotik_server_id) {
                // Dispatch job isolir
                ProcessMikrotikIsolation::dispatch($customer, 'isolate');
                $count++;
                $this->info("Mengisolir pelanggan: {$customer->name}");
            }
        }

        $this->info("Proses selesai. {$count} pelanggan masuk antrean isolir.");
        Log::info("Auto Isolir: {$count} pelanggan diisolir.");
    }
}
