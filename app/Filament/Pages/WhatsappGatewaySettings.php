<?php

namespace App\Filament\Pages;

use App\Settings\WhatsappSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class WhatsappGatewaySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'WhatsApp Gateway';
    protected static ?string $title = 'Pengaturan WhatsApp Gateway';

    protected static string $view = 'filament.pages.whatsapp-gateway-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(WhatsappSettings::class);
        
        $this->form->fill([
            'waha_endpoint' => $settings->waha_endpoint,
            'waha_session' => $settings->waha_session,
            'is_active' => $settings->is_active,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('WAHA (WhatsApp HTTP API) Configuration')
                    ->description('Masukkan URL Endpoint WAHA dan Session ID Anda di sini. Default endpoint biasanya http://localhost:3000 atau https://waha.armedia.id/')
                    ->schema([
                        Forms\Components\TextInput::make('waha_endpoint')
                            ->label('WAHA Endpoint URL')
                            ->placeholder('https://waha.armedia.id/')
                            ->url()
                            ->required(),
                        Forms\Components\TextInput::make('waha_session')
                            ->label('Session Name')
                            ->placeholder('default')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Notifikasi WhatsApp')
                            ->helperText('Jika diaktifkan, sistem akan otomatis mengirim pesan tagihan, isolir, dan resi ke pelanggan Anda.'),
                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $settings = app(WhatsappSettings::class);
        $settings->waha_endpoint = $this->data['waha_endpoint'] ?? '';
        $settings->waha_session = $this->data['waha_session'] ?? '';
        $settings->is_active = $this->data['is_active'] ?? false;
        $settings->save();

        Notification::make()
            ->title('Berhasil disimpan')
            ->success()
            ->send();
    }
}
