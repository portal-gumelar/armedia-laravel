<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class NetworkMap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Networking';
    protected static ?string $title = 'Peta Jaringan (GIS)';

    protected static string $view = 'filament.pages.network-map';

    protected function getViewData(): array
    {
        $odps = \App\Models\Odp::whereNotNull('latitude')->whereNotNull('longitude')->get()->map(function ($odp) {
            return [
                'type' => 'odp',
                'id' => $odp->id,
                'name' => $odp->name,
                'code' => $odp->code,
                'lat' => $odp->latitude,
                'lng' => $odp->longitude,
            ];
        });

        $customers = \App\Models\Customer::whereNotNull('link_maps')->orWhereNotNull('maps_url')->get()->map(function ($customer) {
            // Attempt to parse lat/lng from URL
            $url = $customer->link_maps ?: $customer->maps_url;
            $lat = null;
            $lng = null;
            
            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            } elseif (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            }

            if (!$lat || !$lng) return null;

            return [
                'type' => 'customer',
                'id' => $customer->id,
                'name' => $customer->name,
                'status' => $customer->subscription_status,
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'odp' => $customer->odp?->name ?? 'Belum ada ODP',
            ];
        })->filter()->values();

        return [
            'locations' => $odps->merge($customers)->values()->toJson(),
        ];
    }
}
