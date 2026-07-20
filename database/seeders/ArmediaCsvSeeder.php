<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Village;
use Carbon\Carbon;

class ArmediaCsvSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PAKET -> internet_packages
        foreach ($this->readCsv('products.csv') as $r) {
            if (empty($r['ID'])) continue;
            DB::table('internet_packages')->updateOrInsert(
                ['code' => $r['ID']],
                [
                    'nama_paket'    => $r['Nama'],
                    'brand'         => explode(' ', $r['Nama'])[0],
                    'kecepatan'     => (string) $r['Kapasitas'],
                    'harga'         => (int) $r['Harga'],
                    'ip_allocation' => $r['Alokasi IP'] ?: null,
                    'is_active'     => true,
                    'updated_at'    => now(), 'created_at' => now(),
                ]
            );
        }

        // 2. ODP -> odps
        foreach ($this->readCsv('odp.csv') as $r) {
            if (empty($r['KODE ODP'])) continue;
            
            // Cari atau buat village
            $villageId = null;
            if (!empty($r['DESA / LOKASI'])) {
                $v = Village::firstOrCreate(['name' => strtoupper(trim($r['DESA / LOKASI']))]);
                $villageId = $v->id;
            }

            DB::table('odps')->updateOrInsert(
                ['code' => $r['KODE ODP']],
                [
                    'max_capacity' => is_numeric($r['KAPASITAS MAKS']) ? (int) $r['KAPASITAS MAKS'] : null,
                    'village_id'   => $villageId,
                    'status'       => $r['STATUS'] ?: null, 
                    'updated_at'   => now(), 'created_at' => now()
                ]
            );
        }

        // 3. PERANGKAT -> devices
        foreach ($this->readCsv('devices.csv') as $r) {
            if (empty($r['ID'])) continue;
            DB::table('devices')->updateOrInsert(
                ['device_code' => $r['ID']],
                [
                    'name'             => $r['NAMA'] ?: null,
                    'model'            => $r['MODEL'] ?: null,
                    'serial_number'    => $r['SN'] ?: null,
                    'batch_month_year' => $r['TGL AMBIL DARI STOK'] ?: null,
                    'status'           => str_contains(strtolower($r['STATUS']), 'terpasang') ? 'terpasang' : 'stok',
                    'updated_at'       => now(), 'created_at' => now(),
                ]
            );
        }

        // 4. PELANGGAN -> customers
        foreach ($this->readCsv('customers.csv') as $r) {
            if (empty($r['ID BARU'])) continue;

            $packageId = DB::table('internet_packages')->where('code', $r['Produk ID'])->value('id');
            $deviceId  = DB::table('devices')->where('device_code', $r['Perangkat ID'])->value('id');
            $odpId     = DB::table('odps')->where('code', $r['ODP'])->value('id');
            
            $villageId = null;
            if (!empty($r['Desa'])) {
                $v = Village::firstOrCreate(['name' => strtoupper(trim($r['Desa']))]);
                $villageId = $v->id;
            }

            $customerId = DB::table('customers')->updateOrInsert(
                ['id_arm' => $r['ID BARU']],
                [
                    'id_lama'             => $r['ID LAMA'] ?: null,
                    'name'                => $r['NAMA'],
                    'nik'                 => $r['NIK KTP'] ?: null,
                    'whatsapp'            => $r['No.HP'] ?: null,
                    'kecamatan'           => $r['Kec'] ?: null,
                    'village_id'          => $villageId,
                    'rw'                  => $r['RW'] ?: null,
                    'rt'                  => $r['RT'] ?: null,
                    'kota_kab'            => $r['Kota/Kab'] ?: null,
                    'internet_package_id' => $packageId,
                    'device_id'           => $deviceId,
                    'odp_id'              => $odpId,
                    'ip_address'          => $r['IP'] ?: null,
                    'pon_olt'             => $r['PON OLT'] ?: null,
                    'cable_length_m'      => is_numeric($r['Panjang Kabel']) ? (int) $r['Panjang Kabel'] : null,
                    'activated_at'        => $this->date($r['Tgl Aktif']),
                    'subscription_status' => str_contains(strtolower($r['Status']), 'berhenti') ? 'berhenti' : 'aktif',
                    'notes'               => $r['Keterangan'] ?: null,
                    'photo_url'           => $r['Link Foto'] ?: null,
                    'maps_url'            => $r['Link Maps'] ?: null,
                    'drive_folder_url'    => $r['Link Folder Drive'] ?: null,
                    'updated_at'          => now(), 'created_at' => now(),
                ]
            );

            // Update customer_id di tabel devices jika device terpasang
            if ($deviceId) {
                // Get customer auto-increment ID
                $c = DB::table('customers')->where('id_arm', $r['ID BARU'])->first();
                if ($c) {
                    DB::table('devices')->where('id', $deviceId)->update(['customer_id' => $c->id]);
                }
            }
        }

        // 5. PROSPEK -> registrations
        foreach ($this->readCsv('prospects.csv') as $r) {
            if (empty($r['Nama Pelanggan'])) continue;
            DB::table('registrations')->updateOrInsert(
                ['report_no' => $r['NO LAPORAN'] ?: null, 'nama' => $r['Nama Pelanggan']],
                [
                    'paket'               => $r['Jenis Layanan'] ?: '-',
                    'whatsapp'            => $r['Nomor Telepon'] ?: '-',
                    'alamat'              => $r['Alamat Pemasangan'] ?: '-',
                    'desa'                => '-', // Fallback
                    'tanggal_pemasangan'  => $r['Jadwal Pasang'] ?: 'Secepatnya',
                    'status'              => str_contains(strtolower($r['Status']), 'terpasang') ? 'selesai' : 'baru',
                    'marketing'           => $r['marketing'] ?: null,
                    'updated_at'          => now(), 'created_at' => now(),
                ]
            );
        }

        // 6. TAGIHAN -> invoices
        $bulanMap = ['Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'Mei'=>5,'Jun'=>6,'Jul'=>7,'Agu'=>8,'Sep'=>9,'Okt'=>10,'Nov'=>11,'Des'=>12];
        foreach ($this->readCsv('billing.csv') as $r) {
            $customerId = DB::table('customers')->where('id_arm', $r['id_arm'])->value('id');
            if (!$customerId) continue;
            
            $bulan = $bulanMap[$r['bulan']] ?? null;
            if (!$bulan) continue;
            
            $period = sprintf('2026-%02d-01', $bulan);

            DB::table('invoices')->updateOrInsert(
                ['customer_id' => $customerId, 'period' => $period],
                [
                    'amount'     => is_numeric($r['harga_acuan']) ? (int) $r['harga_acuan'] : 0,
                    'status'     => $this->invoiceStatus($r['status']),
                    'updated_at' => now(), 'created_at' => now(),
                ]
            );
        }
    }

    private function readCsv(string $file): array
    {
        $path = database_path("data/{$file}");
        $rows = [];
        if (($h = fopen($path, 'r')) !== false) {
            $header = fgetcsv($h);
            while (($data = fgetcsv($h)) !== false) {
                // Ensure the row has the same number of columns as the header
                $rows[] = array_combine($header, array_pad($data, count($header), ''));
            }
            fclose($h);
        }
        return $rows;
    }

    private function date(?string $v): ?string
    {
        $v = trim((string) $v);
        if (!$v) return null;
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function invoiceStatus(string $s): string
    {
        $s = strtolower(trim($s));
        if (str_contains($s, 'lunas')) return 'lunas';
        if (str_contains($s, 'belum')) return 'belum';
        return 'tidak_tertagih';
    }
}
