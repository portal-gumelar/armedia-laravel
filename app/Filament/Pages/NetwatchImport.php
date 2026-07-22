<?php

namespace App\Filament\Pages;

use App\Services\NetwatchMatchingService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;

class NetwatchImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'Import Monitoring';
    protected static ?string $title           = 'Import Data Netwatch';
    protected static ?int    $navigationSort  = 3;

    protected static string $view = 'filament.pages.netwatch-import';

    public ?string $csv_text   = null;
    public ?string $checked_at = null;
    public array   $results    = [];
    public array   $unmatched  = [];

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('csv_text')
                ->label('Data Netwatch (paste CSV)')
                ->helperText('Format per baris: IP,status (contoh: 10.1.1.1,up atau 10.1.1.1 up)')
                ->rows(15)
                ->required(),
            DateTimePicker::make('checked_at')
                ->label('Waktu Cek')
                ->default(now())
                ->required(),
        ]);
    }

    public function process(): void
    {
        $this->validate();

        $service = app(NetwatchMatchingService::class);
        $rows    = $service->parseCsvText($this->csv_text);
        $stats   = $service->processLog($rows, $this->checked_at);

        Notification::make()
            ->title('Monitoring diproses')
            ->body("Total: {$stats['total']} | Cocok: {$stats['matched']} | Tidak cocok: {$stats['unmatched']}")
            ->success()
            ->send();

        // Reset form setelah proses
        $this->csv_text   = null;
        $this->checked_at = now()->toDateTimeString();
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
