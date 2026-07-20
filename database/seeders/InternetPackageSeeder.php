<?php

namespace Database\Seeders;

use App\Models\InternetPackage;
use Illuminate\Database\Seeder;

class InternetPackageSeeder extends Seeder
{
    /**
     * Seed paket ARMED dan HEROIK dari data Excel master.
     * updateOrCreate by 'code' — aman dijalankan berulang.
     */
    public function run(): void
    {
        $packages = [
            // ARMED
            ['code' => 'AR-10',  'brand' => 'ARMED',  'nama_paket' => 'ARMED 10 Mbps',   'kecepatan' => '10 Mbps',   'speed_mbps' => 10,  'harga' => 150000],
            ['code' => 'AR-15',  'brand' => 'ARMED',  'nama_paket' => 'ARMED 15 Mbps',   'kecepatan' => '15 Mbps',   'speed_mbps' => 15,  'harga' => 175000],
            ['code' => 'AR-20',  'brand' => 'ARMED',  'nama_paket' => 'ARMED 20 Mbps',   'kecepatan' => '20 Mbps',   'speed_mbps' => 20,  'harga' => 200000],
            ['code' => 'AR-25',  'brand' => 'ARMED',  'nama_paket' => 'ARMED 25 Mbps',   'kecepatan' => '25 Mbps',   'speed_mbps' => 25,  'harga' => 225000],
            ['code' => 'AR-30',  'brand' => 'ARMED',  'nama_paket' => 'ARMED 30 Mbps',   'kecepatan' => '30 Mbps',   'speed_mbps' => 30,  'harga' => 250000],
            ['code' => 'AR-50',  'brand' => 'ARMED',  'nama_paket' => 'ARMED 50 Mbps',   'kecepatan' => '50 Mbps',   'speed_mbps' => 50,  'harga' => 300000],
            ['code' => 'AR-100', 'brand' => 'ARMED',  'nama_paket' => 'ARMED 100 Mbps',  'kecepatan' => '100 Mbps',  'speed_mbps' => 100, 'harga' => 400000],
            ['code' => 'AR-200', 'brand' => 'ARMED',  'nama_paket' => 'ARMED 200 Mbps',  'kecepatan' => '200 Mbps',  'speed_mbps' => 200, 'harga' => 550000],
            // HEROIK
            ['code' => 'HR-10',  'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 10 Mbps',  'kecepatan' => '10 Mbps',   'speed_mbps' => 10,  'harga' => 130000],
            ['code' => 'HR-15',  'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 15 Mbps',  'kecepatan' => '15 Mbps',   'speed_mbps' => 15,  'harga' => 155000],
            ['code' => 'HR-20',  'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 20 Mbps',  'kecepatan' => '20 Mbps',   'speed_mbps' => 20,  'harga' => 180000],
            ['code' => 'HR-25',  'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 25 Mbps',  'kecepatan' => '25 Mbps',   'speed_mbps' => 25,  'harga' => 200000],
            ['code' => 'HR-30',  'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 30 Mbps',  'kecepatan' => '30 Mbps',   'speed_mbps' => 30,  'harga' => 225000],
            ['code' => 'HR-50',  'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 50 Mbps',  'kecepatan' => '50 Mbps',   'speed_mbps' => 50,  'harga' => 275000],
            ['code' => 'HR-150', 'brand' => 'HEROIK', 'nama_paket' => 'HEROIK 150 Mbps', 'kecepatan' => '150 Mbps',  'speed_mbps' => 150, 'harga' => 450000],
        ];

        foreach ($packages as $pkg) {
            InternetPackage::updateOrCreate(
                ['code' => $pkg['code']],
                array_merge($pkg, ['is_active' => true])
            );
        }

        $this->command->info('InternetPackageSeeder: ' . count($packages) . ' paket seeded.');
    }
}
