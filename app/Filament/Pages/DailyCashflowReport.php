<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\Invoice;
use App\Models\OperationalExpense;
use Carbon\Carbon;
use App\Enums\InvoiceStatus;

class DailyCashflowReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Laporan & Keuangan';
    protected static ?string $navigationLabel = 'Arus Kas Harian';
    protected static ?string $title = 'Buku Kas Harian';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.daily-cashflow-report';

    public ?string $date = null;

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->form->fill(['date' => $this->date]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Pilih Tanggal')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->date = $state),
            ]);
    }

    protected function getViewData(): array
    {
        $selectedDate = $this->date ? Carbon::parse($this->date) : now();

        $invoices = Invoice::where('status', InvoiceStatus::LUNAS->value)
            ->whereDate('paid_at', $selectedDate)
            ->with('customer')
            ->get();

        $expenses = OperationalExpense::whereDate('created_at', $selectedDate)
            ->get();

        $totalIn = $invoices->sum('amount');
        $totalOut = $expenses->sum('total_harga');

        $byMethod = $invoices->groupBy('payment_method')->map(fn ($group) => $group->sum('amount'));

        return [
            'selectedDate' => $selectedDate,
            'invoices' => $invoices,
            'expenses' => $expenses,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'byMethod' => $byMethod,
            'saldoAkhir' => $totalIn - $totalOut,
        ];
    }
}
