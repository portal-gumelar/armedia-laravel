<?php

namespace App\Imports;

use App\Models\InternetPackage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PackageImport implements ToCollection, WithHeadingRow
{
    private int $rowCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $code = trim($row['kode'] ?? $row['code'] ?? '');
            if (empty($code)) continue;

            InternetPackage::updateOrCreate(
                ['code' => $code],
                [
                    'nama_paket'   => trim($row['nama_paket'] ?? $row['nama'] ?? ''),
                    'brand'        => strtoupper(trim($row['brand'] ?? '')),
                    'kecepatan'    => trim($row['kecepatan'] ?? ''),
                    'speed_mbps'   => (int) ($row['speed_mbps'] ?? $row['mbps'] ?? 0),
                    'harga'        => (int) ($row['harga'] ?? $row['price'] ?? 0),
                    'ip_allocation' => trim($row['ip_allocation'] ?? ''),
                    'is_active'    => true,
                ]
            );
            $this->rowCount++;
        }
    }

    public function getRowCount(): int { return $this->rowCount; }
}
