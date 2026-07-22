<?php

namespace App\Filament\Pages;

use App\Settings\PaymentSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class PaymentGatewaySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Payment Gateway';
    protected static ?string $title = 'Pengaturan Payment Gateway';

    protected static string $view = 'filament.pages.payment-gateway-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(PaymentSettings::class);
        
        $this->form->fill([
            'midtrans_server_key' => $settings->midtrans_server_key,
            'midtrans_client_key' => $settings->midtrans_client_key,
            'midtrans_is_production' => $settings->midtrans_is_production,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Midtrans Configuration')
                    ->description('Masukkan API Key Midtrans Anda di sini. Jangan bagikan kunci ini ke siapapun.')
                    ->schema([
                        Forms\Components\TextInput::make('midtrans_server_key')
                            ->label('Server Key')
                            ->password()
                            ->revealable()
                            ->required(),
                        Forms\Components\TextInput::make('midtrans_client_key')
                            ->label('Client Key')
                            ->required(),
                        Forms\Components\Toggle::make('midtrans_is_production')
                            ->label('Production Mode')
                            ->helperText('Aktifkan jika Anda ingin menerima pembayaran uang asli (Production). Matikan untuk simulasi (Sandbox).'),
                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $settings = app(PaymentSettings::class);
        $settings->midtrans_server_key = $this->data['midtrans_server_key'] ?? '';
        $settings->midtrans_client_key = $this->data['midtrans_client_key'] ?? '';
        $settings->midtrans_is_production = $this->data['midtrans_is_production'] ?? false;
        $settings->save();

        Notification::make()
            ->title('Berhasil disimpan')
            ->success()
            ->send();
    }
}
