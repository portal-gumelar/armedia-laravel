<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Device;
use App\Models\InternetPackage;
use App\Models\Odp;
use App\Models\Village;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    private int   $rowCount = 0;
    private array $villageCache  = [];
    private array $packageCache  = [];
    private array $odpCache      = [];
    private array $deviceCache   = [];
    public  array $duplicates    = [];

    public function collection(Collection $rows): void
    {
        // Pre-load caches
        Village::all()->each(fn ($v) => $this->villageCache[strtoupper(trim($v->name))] = $v->id);
        InternetPackage::all()->each(fn ($p) => $this->packageCache[trim($p->code ?? '')] = $p->id);
        Odp::all()->each(fn ($o) => $this->odpCache[trim($o->code)] = $o->id);
        Device::all()->each(fn ($d) => $this->deviceCache[trim($d->device_code)] = $d->id);

        $seen = []; // untuk deteksi duplikat

        foreach ($rows as $row) {
            $idArm = trim($row['id_arm'] ?? $row['id'] ?? '');
            $name  = trim($row['nama'] ?? '');
            $wa    = $this->normalizePhone(trim($row['whatsapp'] ?? $row['hp'] ?? ''));

            if (empty($name)) continue;

            // Deteksi duplikat nama + whatsapp
            $dupKey = strtolower($name . '|' . $wa);
            if (isset($seen[$dupKey])) {
                $this->duplicates[] = "Duplikat: {$name} ({$wa})";
                Log::warning("CustomerImport: Duplikat ditemukan: {$name} / {$wa}");
            }
            $seen[$dupKey] = true;

            $desaName  = $this->normalizeDesa($row['desa'] ?? '');
            $villageId = $this->villageCache[strtoupper($desaName)] ?? null;
            $packageId = $this->packageCache[trim($row['produk_id'] ?? $row['kode_paket'] ?? '')] ?? null;
            $odpId     = $this->odpCache[trim($row['odp'] ?? '')] ?? null;
            $deviceId  = $this->deviceCache[trim($row['perangkat_id'] ?? $row['device_code'] ?? '')] ?? null;

            $customer = Customer::updateOrCreate(
                ['id_arm' => $idArm ?: null],
                [
                    'id_lama'            => trim($row['id_lama'] ?? ''),
                    'name'               => $name,
                    'whatsapp'           => $wa,
                    'nik'                => trim($row['nik'] ?? ''),
                    'alamat'             => trim($row['alamat'] ?? ''),
                    'kecamatan'          => 'GUMELAR',
                    'rw'                 => trim($row['rw'] ?? ''),
                    'rt'                 => trim($row['rt'] ?? ''),
                    'village_id'         => $villageId,
                    'internet_package_id' => $packageId,
                    'odp_id'             => $odpId,
                    'device_id'          => $deviceId,
                    'ip_address'         => trim($row['ip_address'] ?? $row['ip'] ?? ''),
                    'pon_olt'            => trim($row['pon_olt'] ?? ''),
                    'cable_length_m'     => (int) ($row['kabel_m'] ?? 0) ?: null,
                    'activated_at'       => $this->parseDate($row['tanggal_aktif'] ?? ''),
                    'subscription_status' => strtolower(trim($row['status'] ?? 'aktif')),
                ]
            );

            // Sync device.customer_id
            if ($deviceId) {
                Device::where('id', $deviceId)->update(['customer_id' => $customer->id, 'status' => 'terpasang']);
            }

            $this->rowCount++;
        }

        if (!empty($this->duplicates)) {
            Log::warning('CustomerImport: ' . count($this->duplicates) . ' duplikat ditemukan', $this->duplicates);
        }
    }

    private function normalizePhone(string $phone): ?string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        return strlen($clean) >= 8 ? $clean : null;
    }

    private function normalizeDesa(string $desa): string
    {
        return strtoupper(trim($desa));
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value)) return null;
        try {
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    public function getRowCount(): int { return $this->rowCount; }
}
