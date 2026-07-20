<?php

namespace App\Imports;

use App\Models\Odp;
use App\Models\Village;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OdpImport implements ToCollection, WithHeadingRow
{
    private int $rowCount = 0;
    private array $villageCache = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $code = trim($row['kode_odp'] ?? $row['code'] ?? '');
            if (empty($code)) continue;

            $desaName  = trim($row['desa'] ?? '');
            $villageId = $this->resolveVillage($desaName);

            Odp::updateOrCreate(
                ['code' => $code],
                [
                    'max_capacity' => (int) ($row['kapasitas'] ?? $row['max_capacity'] ?? 8),
                    'village_id'   => $villageId,
                    'status'       => 'aktif',
                    'notes'        => trim($row['catatan'] ?? ''),
                ]
            );
            $this->rowCount++;
        }
    }

    private function resolveVillage(string $name): ?int
    {
        if (empty($name)) return null;
        $key = strtoupper(trim($name));
        if (!isset($this->villageCache[$key])) {
            $village = Village::whereRaw('UPPER(TRIM(name)) = ?', [$key])->first();
            $this->villageCache[$key] = $village?->id;
        }
        return $this->villageCache[$key];
    }

    public function getRowCount(): int { return $this->rowCount; }
}
