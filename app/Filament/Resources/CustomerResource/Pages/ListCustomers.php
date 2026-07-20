<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Imports\CustomerImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_excel')
                ->label('Import dari Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel / CSV')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->disk('local')
                        ->directory('imports')
                        ->required()
                        ->helperText(
                            '📋 Kolom yang dikenali: id_arm, nama, whatsapp (atau hp), nik, alamat, rt, rw, desa, ' .
                            'produk_id (kode paket), odp, perangkat_id (device_code), ip_address, pon_olt, kabel_m, ' .
                            'tanggal_aktif, status. Format: .xlsx atau .csv'
                        ),
                ])
                ->action(function (array $data): void {
                    $filePath = storage_path('app/' . $data['file']);

                    $importer = new CustomerImport();
                    Excel::import($importer, $filePath);

                    $rowCount   = $importer->getRowCount();
                    $duplicates = $importer->duplicates;

                    if (!empty($duplicates)) {
                        Notification::make()
                            ->warning()
                            ->title("Import Selesai ({$rowCount} baris)")
                            ->body('Ditemukan ' . count($duplicates) . ' duplikat: ' . implode(', ', array_slice($duplicates, 0, 3)) . (count($duplicates) > 3 ? '...' : ''))
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title('Import Berhasil! 🎉')
                            ->body("{$rowCount} data pelanggan berhasil diimpor ke database.")
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}
