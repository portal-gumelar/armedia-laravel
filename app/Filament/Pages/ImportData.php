<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ImportData extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Import Data Excel';
    protected static ?string $title = 'Import Data Excel (Master)';
    protected static string $view = 'filament.pages.import-data';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Upload File Master')
                    ->description('Upload file Excel (.xlsx) yang asli langsung dari komputer Anda. Ini menghindari kerusakan file (corrupt) yang sering terjadi jika lewat Git/Terminal.')
                    ->schema([
                        FileUpload::make('excel_file')
                            ->label('File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->disk('local')
                            ->directory('imports')
                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if (empty($data['excel_file'])) {
            return;
        }

        // Get full path
        $path = Storage::disk('local')->path($data['excel_file']);

        try {
            // Run the artisan command
            Artisan::call('app:import-master-data', [
                'file' => $path
            ]);

            $output = Artisan::output();

            if (str_contains($output, 'Exception') || str_contains($output, 'Error')) {
                Notification::make()
                    ->title('Terjadi Kesalahan (Sebagian Data Mungkin Masuk)')
                    ->body(substr($output, 0, 200))
                    ->warning()
                    ->send();
            } else {
                Notification::make()
                    ->title('Import Selesai')
                    ->body('Data pelanggan berhasil diproses ke database.')
                    ->success()
                    ->send();
            }

            // Clear form
            $this->form->fill();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Import')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
