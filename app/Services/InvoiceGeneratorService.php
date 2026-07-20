<?php

namespace App\Services;

use App\Enums\CustomerSubscriptionStatus;
use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceGeneratorService
{
    /**
     * Generate tagihan untuk semua pelanggan aktif pada bulan tertentu.
     * Menggunakan updateOrCreate sehingga aman dijalankan berulang kali.
     *
     * @param  string $period  Format: 'Y-m' atau 'Y-m-d'
     * @return array  ['created' => int, 'updated' => int, 'skipped' => int]
     */
    public function generateForMonth(string $period): array
    {
        $periodDate = Carbon::parse($period)->startOfMonth()->toDateString();

        $customers = Customer::with('internetPackage')
            ->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)
            ->get();

        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        DB::transaction(function () use ($customers, $periodDate, &$stats) {
            foreach ($customers as $customer) {
                $amount = $customer->internetPackage?->harga ?? 0;

                if ($amount <= 0) {
                    $stats['skipped']++;
                    continue;
                }

                $invoice = Invoice::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'period'      => $periodDate,
                    ],
                    [
                        // Hanya update amount jika belum dibayar
                        'amount' => $amount,
                        'status' => InvoiceStatus::BELUM->value,
                    ]
                );

                $invoice->wasRecentlyCreated ? $stats['created']++ : $stats['updated']++;
            }
        });

        return $stats;
    }
}
