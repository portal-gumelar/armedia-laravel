<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportArmediaMaster extends Command
{
    protected $signature   = 'armedia:import {file : Path ke file Excel master (storage/app relatif)}';
    protected $description = 'Import data master dari file Excel Armedia (Customers, Devices, ODPs, Packages, Registrations)';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $this->info("📂 Memulai import dari: {$file}");
        $this->newLine();

        // ── Cek apakah maatwebsite/excel terpasang ──────────────────────
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            $this->error('Package maatwebsite/excel belum terpasang.');
            $this->line('Jalankan: composer require maatwebsite/excel');
            return self::FAILURE;
        }

        $steps = [
            '1. Paket Internet' => fn () => $this->runImport(\App\Imports\PackageImport::class, $file, '4_Produk'),
            '2. ODP'            => fn () => $this->runImport(\App\Imports\OdpImport::class, $file, '8_Master_ODP'),
            '3. Perangkat'      => fn () => $this->runImport(\App\Imports\DeviceImport::class, $file, '3_Perangkat'),
            '4. Pelanggan'      => fn () => $this->runImport(\App\Imports\CustomerImport::class, $file, '2_Data_Pelanggan'),
            '5. Calon (PSB)'    => fn () => $this->runImport(\App\Imports\RegistrationImport::class, $file, '1_Calon_Pelanggan'),
        ];

        foreach ($steps as $label => $step) {
            $this->line("▶ {$label}");
            try {
                $count = $step();
                $this->line("  ✅ {$count} baris diproses");
            } catch (\Throwable $e) {
                $this->error("  ❌ Gagal: " . $e->getMessage());
                if ($this->option('verbose')) {
                    $this->line($e->getTraceAsString());
                }
            }
            $this->newLine();
        }

        $this->info('✅ Import selesai!');
        $this->warn('Jalankan: php artisan shield:generate --all');

        return self::SUCCESS;
    }

    private function runImport(string $importClass, string $file, string $sheet): int
    {
        $import = new $importClass();
        \Maatwebsite\Excel\Facades\Excel::import($import, $file, null, \Maatwebsite\Excel\Excel::XLSX, ['sheet' => $sheet]);
        return method_exists($import, 'getRowCount') ? $import->getRowCount() : 0;
    }
}
