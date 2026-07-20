<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\CustomerProspect;
use App\Models\CsrDistribution;
use App\Models\CsrDistributionMonth;
use App\Models\Device;
use App\Models\MarketingReferral;
use App\Models\MonthlyBill;
use App\Models\NetwatchMonitoring;
use App\Models\Odp;
use App\Models\OntInventory;
use App\Models\OperationalExpense;
use App\Models\InternetPackage;
use App\Models\ProrataRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import data master ARMEDIA (DATA_PELANGGAN_AAM_GUMELAR.xlsx) ke database.
 *
 * Aman dijalankan berkali-kali: semua insert pakai updateOrCreate berdasarkan
 * kunci unik masing-masing (id_baru, kode_odp, sn, dst), jadi re-run = sync ulang.
 *
 * Usage:
 *   php artisan app:import-master-data /path/to/DATA_PELANGGAN_AAM_GUMELAR.xlsx
 */
class ImportMasterDataCommand extends Command
{
    protected $signature = 'app:import-master-data {path : Path ke file .xlsx}';

    protected $description = 'Import 2_Data_Pelanggan, 3_Perangkat, 8_Master_ODP, 4_Produk, dll dari file master Excel ARMEDIA ke database';

    /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet */
    protected $book;

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $this->info('Membaca workbook (bisa beberapa detik untuk file besar)...');
        $this->book = IOFactory::load($path);

        DB::transaction(function () {
            $this->importProducts();
            $this->importOdps();
            $this->importCustomers();
            $this->importProspects();
            $this->importDevices();
            $this->importOntInventories();
            $this->importNetwatch();
            $this->importMonthlyBills();
            $this->importCsr();
            $this->importMarketing();
            $this->importProrata();
            $this->importOperationalExpenses();
        });

        $this->info('Selesai. Ringkasan:');
        $this->table(['Tabel', 'Jumlah baris'], [
            ['internet_packages', InternetPackage::count()],
            ['odps', Odp::count()],
            ['customers', Customer::count()],
            ['customer_prospects', CustomerProspect::count()],
            ['devices', Device::count()],
            ['ont_inventories', OntInventory::count()],
            ['netwatch_monitorings', NetwatchMonitoring::count()],
            ['monthly_bills', MonthlyBill::count()],
            ['csr_distributions', CsrDistribution::count()],
            ['csr_distribution_months', CsrDistributionMonth::count()],
            ['marketing_referrals', MarketingReferral::count()],
            ['prorata_rates', ProrataRate::count()],
            ['operational_expenses', OperationalExpense::count()],
        ]);

        return self::SUCCESS;
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    protected function sheet(string $name)
    {
        $sheet = $this->book->getSheetByName($name);

        if (! $sheet) {
            $this->warn("Sheet '{$name}' tidak ditemukan, dilewati.");
        }

        return $sheet;
    }

    /** Ambil nilai sel mentah (string kosong jadi null). */
    protected function v($row, int $col)
    {
        $val = $row[$col] ?? null;

        if ($val === null) {
            return null;
        }

        if (is_string($val)) {
            $val = trim($val);

            return $val === '' ? null : $val;
        }

        return $val;
    }

    /** Nilai teks "-" pada tagihan berarti 0 / belum bayar. */
    protected function toAmount($val): int
    {
        if ($val === null || $val === '-' || $val === '') {
            return 0;
        }

        return (int) round((float) $val);
    }

    protected function toDate($val): ?string
    {
        if ($val === null || $val === '' || $val === '-') {
            return null;
        }

        if ($val instanceof \DateTimeInterface) {
            return $val->format('Y-m-d');
        }

        if (is_numeric($val)) {
            try {
                return ExcelDate::excelToDateTimeObject($val)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        try {
            return \Carbon\Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Iterasi baris sebagai array ter-index-1 mulai dari $startRow, berhenti saat kolom A & B (index 1 & 2) kosong. */
    protected function rows($sheet, int $startRow, int $stopIfEmptyCols = 2): \Generator
    {
        $highestRow = $sheet->getHighestDataRow();

        for ($r = $startRow; $r <= $highestRow; $r++) {
            $row = [];
            $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

            $empty = true;
            for ($c = 1; $c <= $highestCol; $c++) {
                $cell = $sheet->getCellByColumnAndRow($c, $r);
                $row[$c] = $cell->getValue();
                if ($c <= $stopIfEmptyCols && $row[$c] !== null && $row[$c] !== '') {
                    $empty = false;
                }
            }

            if ($empty) {
                continue;
            }

            yield $r => $row;
        }
    }

    // ---------------------------------------------------------------
    // 4_Produk -> products   (header row 2, data mulai row 3)
    // ---------------------------------------------------------------
    protected function importProducts(): void
    {
        $sheet = $this->sheet('4_Produk');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 3) as $row) {
            $kode = $this->v($row, 3);
            if (! $kode) continue;

            InternetPackage::updateOrCreate(
                ['kode' => $kode],
                [
                    'nama_paket' => $this->v($row, 2),
                    'speed_mbps' => (int) ($this->v($row, 4) ?? 0),
                    'harga' => $this->toAmount($this->v($row, 5)),
                    'alokasi_ip' => $this->v($row, 6),
                    'is_active' => true,
                ]
            );
            $n++;
        }
        $this->info("products: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 8_Master_ODP -> odps   (header row 4, data mulai row 5)
    // ---------------------------------------------------------------
    protected function importOdps(): void
    {
        $sheet = $this->sheet('8_Master_ODP');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 5) as $row) {
            $kode = $this->v($row, 1);
            if (! $kode) continue;

            Odp::updateOrCreate(
                ['code' => $kode],
                [
                    'port_terpakai' => (int) ($this->v($row, 2) ?? 0),
                    'kapasitas_maks' => $this->v($row, 3) !== null ? (int) $this->v($row, 3) : null,
                    'sisa_slot' => $this->v($row, 4) !== null ? (int) $this->v($row, 4) : null,
                    'status' => $this->v($row, 5),
                    'desa_lokasi' => $this->v($row, 6),
                ]
            );
            $n++;
        }
        $this->info("odps: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 2_Data_Pelanggan -> customers   (header row 3, data mulai row 5)
    // ---------------------------------------------------------------
    protected function importCustomers(): void
    {
        $sheet = $this->sheet('2_Data_Pelanggan');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 5) as $row) {
            $idBaru = $this->v($row, 2);
            if (! $idBaru) continue;

            $product = ($kode = $this->v($row, 12)) ? InternetPackage::firstWhere('kode', $kode) : null;
            $odp = ($kodeOdp = $this->v($row, 18)) ? Odp::firstWhere('code', $kodeOdp) : null;

            Customer::updateOrCreate(
                ['id_arm' => $idBaru],
                [
                    'id_lama' => $this->v($row, 3),
                    'name' => $this->v($row, 4) ?? '(tanpa nama)',
                    'nik' => $this->v($row, 5),
                    'whatsapp' => $this->v($row, 6),
                    'password' => bcrypt($this->v($row, 6) ?? 'armedia123'),
                    'kec' => $this->v($row, 7),
                    'desa' => $this->v($row, 8),
                    'rw' => $this->v($row, 9),
                    'rt' => $this->v($row, 10),
                    'kota_kab' => $this->v($row, 11),
                    'internet_package_id' => $product?->id,
                    'paket_mbps' => $this->v($row, 13) !== null ? (int) $this->v($row, 13) : null,
                    'harga' => $this->toAmount($this->v($row, 14)),
                    'ip_address' => $this->v($row, 15),
                    'perangkat_kode' => $this->v($row, 16),
                    'sn' => $this->v($row, 17),
                    'odp_id' => $odp?->id,
                    'activated_at' => $this->toDate($this->v($row, 19)),
                    'cable_length_m' => $this->v($row, 20),
                    'pon_olt' => $this->v($row, 21),
                    'notes' => $this->v($row, 22),
                    'subscription_status' => strtolower($this->v($row, 23) ?? 'aktif'),
                    'link_foto' => $this->v($row, 24),
                    'link_maps' => $this->v($row, 25),
                    'wa_konfirmasi_rtrw' => $this->v($row, 26),
                    'wa_umum' => $this->v($row, 27),
                    'wa_invoice_tagihan' => $this->v($row, 28),
                    'wa_foto_lokasi' => $this->v($row, 29),
                    'ssid' => $this->v($row, 30),
                    'password_wifi' => $this->v($row, 31),
                    'vlan' => $this->v($row, 32),
                    'tipe_onu' => $this->v($row, 33),
                    'jatuh_tempo_bulan_ini' => $this->toDate($this->v($row, 34)),
                    'wa_ingatkan_h3' => $this->v($row, 35),
                    'wa_jatuh_tempo_hari_ini' => $this->v($row, 36),
                    'wa_lewat_tempo' => $this->v($row, 37),
                    'tagihan_bln1_prorata' => $this->toAmount($this->v($row, 38)),
                    'wa_invoice_pelanggan_baru' => $this->v($row, 39),
                    'port_olt' => $this->v($row, 40),
                    'index_olt' => $this->v($row, 41),
                    'profile' => $this->v($row, 42),
                    'vlan_olt' => $this->v($row, 43),
                    'redaman_dbm' => $this->v($row, 44),
                    'sn_lama' => $this->v($row, 45),
                    'teknisi_pasang' => $this->v($row, 46),
                ]
            );
            $n++;
        }
        $this->info("customers: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 1_Calon_Pelanggan -> customer_prospects   (header row 3, data mulai row 4)
    // ---------------------------------------------------------------
    protected function importProspects(): void
    {
        $sheet = $this->sheet('1_Calon_Pelanggan');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 4) as $row) {
            $nama = $this->v($row, 2);
            if (! $nama) continue;

            $noLaporan = $this->v($row, 4);

            CustomerProspect::updateOrCreate(
                $noLaporan ? ['no_laporan' => $noLaporan] : ['nama_pelanggan' => $nama, 'nomor_telepon' => $this->v($row, 3)],
                [
                    'nama_pelanggan' => $nama,
                    'nomor_telepon' => $this->v($row, 3),
                    'alamat_pemasangan' => $this->v($row, 5),
                    'jadwal_pasang' => $this->toDate($this->v($row, 6)),
                    'jenis_layanan' => $this->v($row, 7),
                    'status' => $this->v($row, 8) ?? 'Belum',
                    'marketing' => $this->v($row, 9),
                ]
            );
            $n++;
        }
        $this->info("customer_prospects: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 3_Perangkat -> devices   (header row 8, data mulai row 9)
    // ---------------------------------------------------------------
    protected function importDevices(): void
    {
        $sheet = $this->sheet('3_Perangkat');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 9) as $row) {
            $kodeId = $this->v($row, 4);
            $sn = $this->v($row, 5);
            if (! $kodeId && ! $sn) continue;

            $customer = ($idPel = $this->v($row, 8)) ? Customer::firstWhere('id_arm', $idPel) : null;

            Device::updateOrCreate(
                $kodeId ? ['kode_id' => $kodeId] : ['sn' => $sn],
                [
                    'nama' => $this->v($row, 2) ?? 'ONT',
                    'model' => $this->v($row, 3),
                    'sn' => $sn,
                    'tgl_ambil_dari_stok' => $this->toDate($this->v($row, 6)),
                    'status' => $this->v($row, 7),
                    'customer_id' => $customer?->id,
                    'id_lama_referensi' => $this->v($row, 10),
                    'kondisi' => $this->v($row, 11),
                    'catatan' => $this->v($row, 12),
                ]
            );
            $n++;
        }
        $this->info("devices: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 12_ONT_NEW + 13_ONT_EROR -> ont_inventories
    // ---------------------------------------------------------------
    protected function importOntInventories(): void
    {
        $n = 0;

        if ($sheet = $this->sheet('12_ONT_NEW')) {
            foreach ($this->rows($sheet, 3) as $row) {
                $sn = $this->v($row, 4);
                if (! $sn) continue;

                $customer = ($idPel = $this->v($row, 15)) ? Customer::firstWhere('id_arm', $idPel) : null;

                OntInventory::updateOrCreate(
                    ['sn' => $sn, 'tipe' => 'new'],
                    [
                        'nama_barang' => $this->v($row, 2) ?? 'ONT',
                        'merek' => $this->v($row, 3),
                        'mac_address' => $this->v($row, 5),
                        'ip_address' => $this->v($row, 6),
                        'ssid_2g' => $this->v($row, 7),
                        'ssid_5g' => $this->v($row, 8),
                        'jumlah' => (int) ($this->v($row, 9) ?? 1),
                        'keterangan' => $this->v($row, 10),
                        'status' => $this->v($row, 11),
                        'tgl_keluar' => $this->toDate($this->v($row, 12)),
                        'teknisi' => $this->v($row, 13),
                        'customer_id' => $customer?->id,
                    ]
                );
                $n++;
            }
        }

        if ($sheet = $this->sheet('13_ONT_EROR')) {
            foreach ($this->rows($sheet, 3) as $row) {
                $sn = $this->v($row, 4);
                if (! $sn) continue;

                OntInventory::updateOrCreate(
                    ['sn' => $sn, 'tipe' => 'error'],
                    [
                        'nama_barang' => $this->v($row, 2) ?? 'ONT',
                        'merek' => $this->v($row, 3),
                        'mac_address' => $this->v($row, 5),
                        'ip_address' => $this->v($row, 6),
                        'ssid_2g' => $this->v($row, 7),
                        'ssid_5g' => $this->v($row, 8),
                        'jumlah' => (int) ($this->v($row, 9) ?? 1),
                        'keterangan' => $this->v($row, 10),
                    ]
                );
                $n++;
            }
        }

        $this->info("ont_inventories: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 9_Monitoring_Netwatch -> netwatch_monitorings  (header row 3, data mulai row 4)
    // ---------------------------------------------------------------
    protected function importNetwatch(): void
    {
        $sheet = $this->sheet('9_Monitoring_Netwatch');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 4) as $row) {
            $ip = $this->v($row, 2);
            if (! $ip) continue;

            $customer = ($idPel = $this->v($row, 4)) ? Customer::firstWhere('id_arm', $idPel) : null;

            NetwatchMonitoring::updateOrCreate(
                ['ip_address' => $ip],
                [
                    'status_koneksi' => $this->v($row, 3),
                    'customer_id' => $customer?->id,
                    'desa' => $this->v($row, 6),
                    'rw_rt' => $this->v($row, 7),
                    'paket_mbps' => $this->v($row, 8) !== null ? (int) $this->v($row, 8) : null,
                    'status_berlangganan' => $this->v($row, 9),
                    'chat_wa' => $this->v($row, 10),
                    'wa_follow_up_gangguan' => $this->v($row, 11),
                ]
            );
            $n++;
        }
        $this->info("netwatch_monitorings: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 10_Tagihan_Bulanan -> monthly_bills  (header row 4, data mulai row 5)
    // kolom F..Q = Jan..Des (tahun berjalan diambil dari judul sheet, default tahun ini)
    // ---------------------------------------------------------------
    protected function importMonthlyBills(): void
    {
        $sheet = $this->sheet('10_Tagihan_Bulanan');
        if (! $sheet) return;

        $tahun = (int) date('Y'); // sheet berjudul "... (2026)" - sesuaikan manual jika perlu
        $bulanCols = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]; // Jan(F) .. Des(Q)

        $n = 0;
        foreach ($this->rows($sheet, 5) as $row) {
            $idArm = $this->v($row, 2);
            if (! $idArm) continue;

            $customer = Customer::firstWhere('id_arm', $idArm);
            if (! $customer) continue;

            $hargaAcuan = $this->toAmount($this->v($row, 5));

            foreach ($bulanCols as $i => $col) {
                $bulan = $i + 1;
                MonthlyBill::updateOrCreate(
                    ['customer_id' => $customer->id, 'tahun' => $tahun, 'bulan' => $bulan],
                    [
                        'jumlah' => $this->toAmount($this->v($row, $col)),
                        'harga_acuan_snapshot' => $hargaAcuan,
                    ]
                );
                $n++;
            }
        }
        $this->info("monthly_bills: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 5_Rekap_CSR -> csr_distributions + csr_distribution_months
    // (header 2 baris, data mulai row 3)
    // pelanggan bulanan: kolom I..O (Jun..Des) = 9..15
    // csr bulanan:       kolom P..V (Jun..Des) = 16..22
    // ---------------------------------------------------------------
    protected function importCsr(): void
    {
        $sheet = $this->sheet('5_Rekap_CSR');
        if (! $sheet) return;

        $bulanMap = [6, 7, 8, 9, 10, 11, 12]; // Jun..Des
        $pelangganCols = [9, 10, 11, 12, 13, 14, 15];
        $csrCols = [16, 17, 18, 19, 20, 21, 22];

        $n = 0;
        foreach ($this->rows($sheet, 3) as $row) {
            $no = $this->v($row, 1);
            if ($no === null) continue;

            $distribution = CsrDistribution::updateOrCreate(
                ['no' => (int) $no],
                [
                    'nama' => $this->v($row, 2),
                    'provinsi' => $this->v($row, 3),
                    'kabupaten' => $this->v($row, 4),
                    'kecamatan' => $this->v($row, 5),
                    'desa' => $this->v($row, 6),
                    'rw' => $this->v($row, 7),
                    'rt' => $this->v($row, 8),
                    'total' => $this->toAmount($this->v($row, 23)),
                    'dana_desa' => $this->toAmount($this->v($row, 24)),
                    'dana_rt' => $this->toAmount($this->v($row, 25)),
                    'status_pencairan' => $this->v($row, 26) ?? 'Belum Dibayar',
                    'tgl_bayar' => $this->toDate($this->v($row, 27)),
                ]
            );

            foreach ($bulanMap as $i => $bulan) {
                CsrDistributionMonth::updateOrCreate(
                    ['csr_distribution_id' => $distribution->id, 'bulan' => $bulan],
                    [
                        'jumlah_pelanggan' => (int) ($this->v($row, $pelangganCols[$i]) ?? 0),
                        'jumlah_csr' => $this->toAmount($this->v($row, $csrCols[$i])),
                    ]
                );
            }
            $n++;
        }
        $this->info("csr_distributions: {$n} baris (x7 bulan)");
    }

    // ---------------------------------------------------------------
    // 6_Marketing -> marketing_referrals  (header row 3, data mulai row 4)
    // ---------------------------------------------------------------
    protected function importMarketing(): void
    {
        $sheet = $this->sheet('6_Marketing');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 4) as $row) {
            $client = $this->v($row, 4);
            if (! $client) continue;

            MarketingReferral::create([
                'nama_marketing' => $this->v($row, 2),
                'lokasi' => $this->v($row, 3),
                'nama_client' => $client,
                'jumlah_fee' => $this->toAmount($this->v($row, 5)),
                'tgl_daftar' => $this->toDate($this->v($row, 6)),
                'sumber_data' => $this->v($row, 7),
            ]);
            $n++;
        }
        $this->info("marketing_referrals: {$n} baris");
    }

    // ---------------------------------------------------------------
    // Tabel_ProRata -> prorata_rates  (header row 4, data mulai row 5)
    // kolom B..I = ARMED 10, 20, 30, 50, 75, 100, 150, 200
    // ---------------------------------------------------------------
    protected function importProrata(): void
    {
        $sheet = $this->sheet('Tabel_ProRata');
        if (! $sheet) return;

        $productCols = [
            2 => 'ARMED 10', 3 => 'ARMED 20', 4 => 'ARMED 30', 5 => 'ARMED 50',
            6 => 'ARMED 75', 7 => 'ARMED 100', 8 => 'ARMED 150', 9 => 'ARMED 200',
        ];

        $n = 0;
        foreach ($this->rows($sheet, 5) as $row) {
            $tgl = $this->v($row, 1);
            if ($tgl === null) continue;

            foreach ($productCols as $col => $namaProduk) {
                $product = InternetPackage::firstWhere('nama_paket', $namaProduk);
                if (! $product) continue; // produk ini belum ada di 4_Produk, lewati

                ProrataRate::updateOrCreate(
                    ['tanggal_pasang' => (int) $tgl, 'product_id' => $product->id],
                    ['jumlah' => $this->toAmount($this->v($row, $col))]
                );
                $n++;
            }
        }
        $this->info("prorata_rates: {$n} baris");
    }

    // ---------------------------------------------------------------
    // 14_OPR -> operational_expenses  (header row 1, data mulai row 2)
    // ---------------------------------------------------------------
    protected function importOperationalExpenses(): void
    {
        $sheet = $this->sheet('14_OPR');
        if (! $sheet) return;

        $n = 0;
        foreach ($this->rows($sheet, 2, 3) as $row) {
            $operasional = $this->v($row, 3);
            if (! $operasional) continue;

            OperationalExpense::create([
                'nota' => $this->v($row, 2),
                'operasional' => $operasional,
                'qty' => $this->v($row, 4) !== null ? (string) $this->v($row, 4) : null,
                'harga_satuan' => $this->toAmount($this->v($row, 5)),
                'total_harga' => $this->toAmount($this->v($row, 6)),
            ]);
            $n++;
        }
        $this->info("operational_expenses: {$n} baris");
    }
}
