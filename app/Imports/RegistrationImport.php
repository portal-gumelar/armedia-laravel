<?php

namespace App\Imports;

use App\Models\Registration;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegistrationImport implements ToCollection, WithHeadingRow
{
    private int $rowCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nama = trim($row['nama'] ?? '');
            if (empty($nama)) continue;

            $wa = preg_replace('/[^0-9]/', '', trim($row['whatsapp'] ?? $row['hp'] ?? ''));
            if (strlen($wa) < 8) $wa = null;

            Registration::updateOrCreate(
                [
                    'nama'     => $nama,
                    'whatsapp' => $wa,
                ],
                [
                    'paket'             => trim($row['paket'] ?? $row['produk'] ?? ''),
                    'kecamatan'         => 'GUMELAR',
                    'desa'              => strtoupper(trim($row['desa'] ?? '')),
                    'alamat'            => trim($row['alamat'] ?? ''),
                    'rw'                => trim($row['rw'] ?? ''),
                    'rt'                => trim($row['rt'] ?? ''),
                    'report_no'         => trim($row['report_no'] ?? $row['no_laporan'] ?? ''),
                    'marketing'         => trim($row['marketing'] ?? ''),
                    'pipeline_status'   => strtolower(trim($row['status_pipeline'] ?? 'belum')),
                    'status'            => 'baru',
                ]
            );
            $this->rowCount++;
        }
    }

    public function getRowCount(): int { return $this->rowCount; }
}
