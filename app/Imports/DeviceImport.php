<?php

namespace App\Imports;

use App\Models\Device;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DeviceImport implements ToCollection, WithHeadingRow
{
    private int $rowCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $code = trim($row['kode_perangkat'] ?? $row['device_code'] ?? '');
            if (empty($code)) continue;

            Device::updateOrCreate(
                ['device_code' => $code],
                [
                    'name'             => trim($row['nama'] ?? $row['name'] ?? 'XPON ONT'),
                    'model'            => trim($row['model'] ?? ''),
                    'serial_number'    => trim($row['serial_number'] ?? $row['sn'] ?? '') ?: null,
                    'batch_month_year' => trim($row['batch'] ?? ''),
                    'status'           => strtolower(trim($row['status'] ?? 'stok')),
                ]
            );
            $this->rowCount++;
        }
    }

    public function getRowCount(): int { return $this->rowCount; }
}
