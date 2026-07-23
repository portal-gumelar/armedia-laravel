<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\MarketingFee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Laporan & Keuangan';
    protected static ?string $navigationLabel = 'Payroll (Gaji & Fee)';
    protected static ?string $title = 'Rekapitulasi Gaji & Marketing Fee';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.payroll-report';

    public ?string $month = null;

    public function mount()
    {
        $this->month = now()->startOfMonth()->toDateString();
        $this->form->fill(['month' => $this->month]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('month')
                    ->label('Pilih Bulan')
                    ->displayFormat('M Y')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->month = $state),
            ]);
    }

    protected function getViewData(): array
    {
        $selectedDate = $this->month ? Carbon::parse($this->month) : now();
        $start = $selectedDate->copy()->startOfMonth();
        $end = $selectedDate->copy()->endOfMonth();

        // Get marketing fees grouped by marketing_name
        $payrolls = MarketingFee::whereBetween('created_at', [$start, $end])
            ->select('marketing_name', DB::raw('SUM(total_fee) as total_fee'), DB::raw('COUNT(*) as total_psb'))
            ->groupBy('marketing_name')
            ->get();

        return [
            'selectedMonth' => $selectedDate,
            'payrolls' => $payrolls,
        ];
    }
}
