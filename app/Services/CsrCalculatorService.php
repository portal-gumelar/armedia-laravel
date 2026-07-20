<?php

namespace App\Services;

use App\Enums\CustomerSubscriptionStatus;
use App\Models\CsrContribution;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CsrCalculatorService
{
    // Tarif CSR per pelanggan aktif per bulan (dalam rupiah)
    public const FEE_PER_CUSTOMER = 3000;
    public const DESA_SHARE       = 1000;
    public const RT_SHARE         = 2000;

    /**
     * Hitung dan simpan snapshot CSR untuk bulan tertentu.
     *
     * @param  string $period  Format: 'Y-m' atau 'Y-m-d' (akan dinormalisasi ke hari pertama bulan)
     * @return int Jumlah baris unik yang dibuat/diupdate
     */
    public function calculate(string $period): int
    {
        $periodDate = Carbon::parse($period)->startOfMonth()->toDateString();

        $groups = Customer::query()
            ->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)
            ->whereNotNull('village_id')
            ->select('village_id', 'rw', 'rt', DB::raw('COUNT(*) as customer_count'))
            ->groupBy('village_id', 'rw', 'rt')
            ->get();

        $count = 0;

        DB::transaction(function () use ($groups, $periodDate, &$count) {
            foreach ($groups as $group) {
                $n = $group->customer_count;

                CsrContribution::updateOrCreate(
                    [
                        'village_id' => $group->village_id,
                        'rw'         => $group->rw,
                        'rt'         => $group->rt,
                        'period'     => $periodDate,
                    ],
                    [
                        'customer_count' => $n,
                        'csr_total'      => $n * self::FEE_PER_CUSTOMER,
                        'desa_share'     => $n * self::DESA_SHARE,
                        'rt_share'       => $n * self::RT_SHARE,
                    ]
                );
                $count++;
            }
        });

        return $count;
    }

    /**
     * Hitung CSR secara real-time langsung dari tabel pelanggan (tanpa menyimpan).
     * Digunakan untuk preview/live di halaman laporan.
     */
    public function getLiveData(): \Illuminate\Support\Collection
    {
        return Customer::query()
            ->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)
            ->with('village')
            ->select('village_id', 'rw', 'rt', DB::raw('COUNT(*) as customer_count'))
            ->groupBy('village_id', 'rw', 'rt')
            ->orderBy('village_id')
            ->orderBy('rw')
            ->orderBy('rt')
            ->get()
            ->map(function ($row) {
                $n = $row->customer_count;
                return (object) [
                    'village'        => $row->village,
                    'rw'             => $row->rw,
                    'rt'             => $row->rt,
                    'customer_count' => $n,
                    'csr_total'      => $n * self::FEE_PER_CUSTOMER,
                    'desa_share'     => $n * self::DESA_SHARE,
                    'rt_share'       => $n * self::RT_SHARE,
                ];
            });
    }

    /**
     * Rekap per Desa dari live data (dikelompokkan).
     */
    public function getLiveDataByVillage(): \Illuminate\Support\Collection
    {
        return $this->getLiveData()
            ->groupBy(fn ($row) => $row->village?->name ?? 'Tidak Diketahui')
            ->map(function ($rows, $villageName) {
                return (object) [
                    'village_name'   => $villageName,
                    'customer_count' => $rows->sum('customer_count'),
                    'csr_total'      => $rows->sum('csr_total'),
                    'desa_share'     => $rows->sum('desa_share'),
                    'rt_share'       => $rows->sum('rt_share'),
                    'rt_count'       => $rows->count(),
                    'rows'           => $rows,
                ];
            })
            ->sortBy('village_name')
            ->values();
    }
}
