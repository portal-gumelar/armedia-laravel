<?php

namespace Database\Seeders;

use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Seed desa-desa Kecamatan Gumelar — normalized tanpa trailing space.
     * Menggunakan updateOrCreate sehingga aman dijalankan berulang kali.
     */
    public function run(): void
    {
        $desas = [
            'GUMELAR',
            'CIHONJE',
            'PANINGKABAN',
            'KARANGKEMIRI',
            'SAMUDRA',
            'SUNYALANGU',
            'TLAGA',
            'GANCANG',
            'CITATAH',
            'SAMEGA',
            'CIKARET',
            'KARANGTALUN',
            'KARANGSALAM',
            'CIRAHAB',
            'PLANGKARAN',
            'WINDUJAYA',
        ];

        foreach ($desas as $desa) {
            Village::updateOrCreate(
                [
                    'name'      => trim($desa),
                    'kecamatan' => 'GUMELAR',
                ]
            );
        }

        $this->command->info('VillageSeeder: ' . count($desas) . ' desa seeded.');
    }
}
