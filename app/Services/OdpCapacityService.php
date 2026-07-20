<?php

namespace App\Services;

use App\Enums\CustomerSubscriptionStatus;
use App\Models\Odp;

class OdpCapacityService
{
    /**
     * Hitung statistik kapasitas port ODP berdasarkan jumlah pelanggan aktif.
     *
     * @return array{used: int, max: int, available: int, percent: float, color: string}
     */
    public function getStats(Odp $odp): array
    {
        $used = $odp->customers()
            ->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)
            ->count();

        $max       = $odp->max_capacity ?? 8; // default 8 port jika tidak diisi
        $available = max(0, $max - $used);
        $percent   = $max > 0 ? round(($used / $max) * 100) : 0;

        $color = match (true) {
            $percent >= 90 => 'danger',
            $percent >= 70 => 'warning',
            default        => 'success',
        };

        return compact('used', 'max', 'available', 'percent', 'color');
    }

    /**
     * Hitung stats untuk semua ODP sekaligus (eager-friendly).
     *
     * @return array<int, array> Keyed by odp->id
     */
    public function getStatsForAll(): array
    {
        $odps   = Odp::withCount([
            'customers as active_count' => fn ($q) => $q->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value),
        ])->get();

        $result = [];
        foreach ($odps as $odp) {
            $used      = $odp->active_count;
            $max       = $odp->max_capacity ?? 8;
            $available = max(0, $max - $used);
            $percent   = $max > 0 ? round(($used / $max) * 100) : 0;
            $color     = match (true) {
                $percent >= 90 => 'danger',
                $percent >= 70 => 'warning',
                default        => 'success',
            };

            $result[$odp->id] = compact('used', 'max', 'available', 'percent', 'color');
        }

        return $result;
    }
}
