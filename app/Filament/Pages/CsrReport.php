<?php

namespace App\Filament\Pages;

use App\Services\CsrCalculatorService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CsrReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'CSR & Komunitas';
    protected static ?string $navigationLabel = 'Laporan CSR';
    protected static ?string $title           = 'Laporan CSR Desa';
    protected static ?int    $navigationSort  = 1;

    protected static string $view = 'filament.pages.csr-report';

    public int $selectedMonth;
    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedMonth = (int) now()->format('n');
        $this->selectedYear  = (int) now()->format('Y');
    }

    public function form(Form $form): Form
    {
        $months = [
            1  => 'Januari', 2  => 'Februari',  3 => 'Maret',    4  => 'April',
            5  => 'Mei',     6  => 'Juni',       7 => 'Juli',     8  => 'Agustus',
            9  => 'September',10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];

        $years = [];
        for ($y = 2026; $y <= now()->year + 1; $y++) {
            $years[$y] = (string) $y;
        }

        return $form->schema([
            Select::make('selectedMonth')
                ->label('Bulan')
                ->options($months)
                ->default(now()->month)
                ->required(),
            Select::make('selectedYear')
                ->label('Tahun')
                ->options($years)
                ->default(now()->year)
                ->required(),
        ])->columns(2);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save_snapshot')
                ->label('Simpan Snapshot Bulan Ini')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Simpan Snapshot CSR')
                ->modalDescription('Data CSR akan dihitung berdasarkan pelanggan aktif saat ini dan disimpan ke database untuk arsip. Lanjutkan?')
                ->action(function () {
                    $service = app(CsrCalculatorService::class);
                    $period  = sprintf('%04d-%02d-01', $this->selectedYear, $this->selectedMonth);
                    $count   = $service->calculate($period);
                    $label   = Carbon::parse($period)->translatedFormat('F Y');

                    Notification::make()
                        ->title('Snapshot CSR Tersimpan!')
                        ->body("Berhasil menyimpan {$count} baris data CSR untuk {$label}.")
                        ->success()
                        ->send();
                }),

            Action::make('print')
                ->label('Cetak / Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url('#')
                ->extraAttributes(['onclick' => 'window.print(); return false;']),
        ];
    }

    public function getLiveDataByVillage(): \Illuminate\Support\Collection
    {
        return app(CsrCalculatorService::class)->getLiveDataByVillage();
    }

    public function getLiveData(): \Illuminate\Support\Collection
    {
        return app(CsrCalculatorService::class)->getLiveData();
    }

    public function getPeriodLabel(): string
    {
        $months = [
            1  => 'Januari', 2  => 'Februari',  3 => 'Maret',    4  => 'April',
            5  => 'Mei',     6  => 'Juni',       7 => 'Juli',     8  => 'Agustus',
            9  => 'September',10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        return ($months[$this->selectedMonth] ?? '') . ' ' . $this->selectedYear;
    }
}
