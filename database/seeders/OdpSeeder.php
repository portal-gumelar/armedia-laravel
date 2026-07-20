<?php

namespace Database\Seeders;

use App\Models\Odp;
use App\Models\Village;
use Illuminate\Database\Seeder;

class OdpSeeder extends Seeder
{
    /**
     * Seed 16 ODP dari data Excel master.
     * updateOrCreate by 'code' — aman dijalankan berulang.
     * Harga kapasitas default 8 port per ODP.
     */
    public function run(): void
    {
        // Ambil desa GUMELAR sebagai default jika village belum diketahui
        $defaultVillage = Village::where('name', 'GUMELAR')->first();

        $odps = [
            ['code' => '1/1/1', 'max_capacity' => 8],
            ['code' => '1/1/2', 'max_capacity' => 8],
            ['code' => '1/1/3', 'max_capacity' => 8],
            ['code' => '1/1/4', 'max_capacity' => 8],
            ['code' => '1/1/5', 'max_capacity' => 8],
            ['code' => '1/1/6', 'max_capacity' => 8],
            ['code' => '1/1/7', 'max_capacity' => 8],
            ['code' => '1/1/8', 'max_capacity' => 8],
            ['code' => '1/2/1', 'max_capacity' => 8],
            ['code' => '1/2/2', 'max_capacity' => 8],
            ['code' => '1/2/3', 'max_capacity' => 8],
            ['code' => '1/2/4', 'max_capacity' => 8],
            ['code' => '1/2/5', 'max_capacity' => 8],
            ['code' => '1/2/6', 'max_capacity' => 8],
            ['code' => '1/2/7', 'max_capacity' => 8],
            ['code' => '1/2/8', 'max_capacity' => 8],
        ];

        foreach ($odps as $odp) {
            Odp::updateOrCreate(
                ['code' => $odp['code']],
                [
                    'max_capacity' => $odp['max_capacity'],
                    'village_id'   => $defaultVillage?->id,
                    'status'       => 'aktif',
                ]
            );
        }

        $this->command->info('OdpSeeder: ' . count($odps) . ' ODP seeded.');
        $this->command->warn('Catatan: Update village_id tiap ODP sesuai data aktual via panel admin atau import Excel.');
    }
}
