<?php

namespace App\Filament\Member\Widgets;

use App\Models\AcrMember;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;

class CustomerInfoWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.member.widgets.customer-info-widget';
    protected int | string | array $columnSpan = 'full';
    
    #[Computed]
    public function customer()
    {
        return auth('customer')->user();
    }

    public function registerAcr()
    {
        $customer = $this->customer;
        
        if (!$customer->acrMember) {
            AcrMember::create([
                'customer_id' => $customer->id,
                'id_pelanggan' => $customer->id_arm,
                'nama' => $customer->name,
                'whatsapp' => $customer->whatsapp ?? '-',
                'password' => bcrypt('123456'), // Default password
                'pin' => '123456',
                'total_poin' => 0,
                'level_member' => 'Bronze',
            ]);
            
            Notification::make()
                ->title('Berhasil Terdaftar di ACR Rewards!')
                ->success()
                ->body('Kumpulkan poin untuk mendapatkan berbagai keuntungan menarik.')
                ->send();
        }
    }

    public function upgradeAction(): Action
    {
        return Action::make('upgrade')
            ->label('Upgrade Kecepatan')
            ->icon('heroicon-o-rocket-launch')
            ->color('primary')
            ->size('sm')
            ->extraAttributes([
                'style' => 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); transition: transform 0.2s;',
                'onmouseover' => 'this.style.transform="translateY(-2px)"',
                'onmouseout' => 'this.style.transform="none"',
            ])
            ->form([
                Select::make('new_package')
                    ->label('Pilih Paket Baru (Kecepatan)')
                    ->options([
                        '20 Mbps' => '20 Mbps - Rp 150.000',
                        '30 Mbps' => '30 Mbps - Rp 200.000',
                        '50 Mbps' => '50 Mbps - Rp 300.000',
                        '100 Mbps' => '100 Mbps - Rp 500.000',
                    ])
                    ->required(),
                Textarea::make('notes')
                    ->label('Pesan Tambahan (Opsional)')
                    ->placeholder('Misal: Mohon diproses segera ya, butuh buat kerja dari rumah.')
            ])
            ->action(function (array $data) {
                $customer = $this->customer;
                
                Ticket::create([
                    'customer_id' => $customer->id,
                    'category' => \App\Enums\TicketCategory::LAINNYA->value,
                    'description' => "Request Upgrade Layanan - " . $data['new_package'] . "\n\nCatatan Tambahan:\n" . ($data['notes'] ?? '-'),
                    'status' => \App\Enums\TicketStatus::OPEN->value,
                ]);

                // Redirect to WhatsApp CS
                $waMessage = "Halo CS ARMEDIA, saya {$customer->name} ({$customer->id_arm}).\n\nSaya baru saja merequest *Upgrade Layanan* ke paket *{$data['new_package']}* melalui Member Portal.\n\nMohon informasi lebih lanjut terkait proses dan pembayarannya. Terima kasih.";
                $waUrl = "https://wa.me/628211234011?text=" . urlencode($waMessage);

                return redirect()->away($waUrl);
            });
    }
}
