# Import Data Master ARMEDIA ke Laravel

Paket ini berisi migration, model, 1 Artisan import command, dan Filament Resource
untuk memasukkan `DATA_PELANGGAN_AAM_GUMELAR.xlsx` ke database `armedia-laravel`.

## Pemetaan sheet -> tabel

| Sheet Excel | Tabel | Catatan |
|---|---|---|
| `4_Produk` | `products` | paket ARMED 10-100 |
| `8_Master_ODP` | `odps` | kapasitas & sisa slot ODP |
| `2_Data_Pelanggan` | `customers` | tabel utama, 46 kolom asli dipertahankan semua |
| `1_Calon_Pelanggan` | `customer_prospects` | belum terpasang |
| `3_Perangkat` | `devices` | stok + histori pemasangan ONT |
| `12_ONT_NEW` + `13_ONT_EROR` | `ont_inventories` | digabung, dibedakan kolom `tipe` (new/error) |
| `9_Monitoring_Netwatch` | `netwatch_monitorings` | status UP/DOWN per IP |
| `10_Tagihan_Bulanan` | `monthly_bills` | di-*unpivot*: 1 baris/bulan/pelanggan (bukan 1 baris = 12 kolom bulan) |
| `5_Rekap_CSR` | `csr_distributions` + `csr_distribution_months` | breakdown bulanan dipisah ke tabel anak |
| `6_Marketing` | `marketing_referrals` | fee mitra |
| `Tabel_ProRata` | `prorata_rates` | referensi tarif hari pertama, di-*unpivot* per produk |
| `14_OPR` | `operational_expenses` | nota operasional |

**Sengaja tidak dibuatkan tabel** (bukan data, tapi tool/dokumentasi di dalam Excel-nya sendiri):
- `DASHBOARD` -> gantinya widget ringkasan di Filament (hitung langsung dari DB)
- `0_Panduan` -> dokumentasi, tidak perlu masuk DB
- `Registrasi_OLT` -> generator command berbasis formula, cukup dibuat ulang sebagai fitur "copy perintah OLT" di Filament kalau dibutuhkan
- `ARSIP_Data_Teknis_Lama` -> sheet ini sendiri sudah ditandai "berpotensi duplikat, jangan input data baru di sini", jadi diabaikan
- `11_Slip_Gaji` -> ini template cetak (formula manual per periode), lebih cocok jadi fitur "Payroll" tersendiri nanti, bukan hasil import langsung

## Cara pakai

1. Copy semua isi folder ini ke root project `armedia-laravel` kamu (folder `database/`, `app/` akan menimpa/menambah, **additive** — tidak menyentuh tabel/route lain).
2. Pastikan PhpSpreadsheet ada (biasanya sudah ikut lewat `maatwebsite/excel`, kalau belum):
   ```bash
   composer require phpoffice/phpspreadsheet
   ```
3. Jalankan migration:
   ```bash
   php artisan migrate
   ```
4. Upload file Excel ke server/VPS (atau ke storage lokal saat dev), lalu jalankan:
   ```bash
   php artisan app:import-master-data /path/ke/DATA_PELANGGAN_AAM_GUMELAR_-_Rapi.xlsx
   ```
   Command ini pakai `updateOrCreate` di semua tabel → **aman dijalankan berkali-kali** tiap kali file Excel-nya di-update (misal jalan mingguan via cron/scheduler kalau mau tetap sinkron dari Excel, sebelum benar-benar pindah 100% ke Filament sebagai sumber utama).
5. Daftarkan resource baru ke Filament panel provider kamu (biasanya otomatis ke-detect kalau sudah ada di `app/Filament/Resources`, tinggal `php artisan optimize:clear` kalau di-cache).

## Yang sudah lengkap sebagai Filament Resource

`CustomerResource`, `ProductResource`, `OdpResource`, `DeviceResource`, `NetwatchMonitoringResource` — lengkap dengan Pages (List/Create/Edit), form, tabel, filter, dan grouping navigasi sesuai PRD (**Operasional ISP** / **Jaringan & Monitoring**).

Resource untuk `MonthlyBillResource`, `OntInventoryResource`, `CsrDistributionResource`,
`MarketingReferralResource`, `CustomerProspectResource`, `OperationalExpenseResource`,
`ProrataRateResource` belum dibuatkan filenya — polanya identik dengan 5 resource di atas
(form + table + 3 halaman Pages). Tinggal bilang mana yang mau diprioritaskan dulu dan saya buatkan.

## Catatan implementasi

- `customers.id_baru` (format `ARM-0001`) jadi kunci relasi ke `devices`, `ont_inventories`,
  `netwatch_monitorings`, `monthly_bills` — semua di-lookup via kolom "ID PELANGGAN" yang ada di
  sheet masing-masing.
- `monthly_bills` diasumsikan tahun berjalan (`date('Y')`) karena judul sheet-nya
  "TAGIHAN BULANAN (2026)" — kalau nanti sheetnya diganti ke tahun baru, tinggal ubah baris
  `$tahun` di `ImportMasterDataCommand::importMonthlyBills()`.
- Nilai `"-"` di kolom tagihan diartikan `0` (belum bayar / belum aktif bulan itu), bukan `null`,
  supaya gampang di-`SUM()` untuk laporan.
- Produk `ARMED 150` dan `ARMED 200` ada di `Tabel_ProRata` tapi belum ada di `4_Produk` (belum
  ada pelanggannya) — baris pro-rata untuk produk itu otomatis dilewati saat import sampai
  produknya didaftarkan dulu di menu Paket/Produk.
