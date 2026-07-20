<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Odp;
use App\Models\Customer;

class PetaCoverage extends Page
{
    protected static string $view = 'filament.pages.peta-coverage';

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Peta Coverage ODP';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Peta Coverage Jaringan ODP';

    public function getOdps(): \Illuminate\Support\Collection
    {
        return Odp::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount('customers')
            ->get()
            ->map(fn ($odp) => [
                'id'           => $odp->id,
                'code'         => $odp->code,
                'alamat'       => $odp->alamat ?? $odp->desa_lokasi ?? '-',
                'latitude'     => (float) $odp->latitude,
                'longitude'    => (float) $odp->longitude,
                'kapasitas'    => $odp->kapasitas_maks ?? $odp->max_capacity ?? 0,
                'terpakai'     => $odp->customers_count,
                'sisa'         => max(0, ($odp->kapasitas_maks ?? $odp->max_capacity ?? 0) - $odp->customers_count),
                'status'       => $odp->status_odp ?? $odp->status ?? 'Aktif',
            ]);
    }

    public function getCustomers(): \Illuminate\Support\Collection
    {
        // Hanya customer yang memiliki ODP dengan koordinat
        return Customer::whereHas('odp', fn ($q) => $q->whereNotNull('latitude'))
            ->with('odp')
            ->where('subscription_status', 'aktif')
            ->get()
            ->map(fn ($c) => [
                'id'     => $c->id,
                'name'   => $c->name,
                'id_arm' => $c->id_arm,
                'odp_id' => $c->odp_id,
            ]);
    }

    public function getTotalOdp(): int
    {
        return Odp::count();
    }

    public function getOdpWithCoords(): int
    {
        return Odp::whereNotNull('latitude')->count();
    }

    public function getTotalCustomer(): int
    {
        return Customer::where('subscription_status', 'aktif')->count();
    }
}
