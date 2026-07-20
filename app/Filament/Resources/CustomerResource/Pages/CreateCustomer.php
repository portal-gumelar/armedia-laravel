<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\InternetPackage;
use App\Models\Village;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('parse_wa')
                ->label('📋 Paste Laporan WA')
                ->color('success')
                ->form([
                    Textarea::make('wa_text')
                        ->label('Teks Laporan WhatsApp')
                        ->rows(15)
                        ->required()
                        ->placeholder("Paste data laporan lapangan di sini...\nContoh:\n_Nama_ : SALSADILA\n_NIK_ : 330215...\n..."),
                ])
                ->action(function (array $data) {
                    $text = $data['wa_text'];
                    $parsed = [];

                    if (preg_match('/_Nama_\s*:\s*(.+)/', $text, $matches)) $parsed['name'] = trim($matches[1]);
                    if (preg_match('/_NIK_\s*:\s*(\d+)/', $text, $matches)) $parsed['nik'] = trim($matches[1]);
                    if (preg_match('/_No Telp_\s*:\s*([\d\+\-\s]+)/', $text, $matches)) $parsed['whatsapp'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    if (preg_match('/_Alamat_\s*:\s*(.+)/', $text, $matches)) $parsed['alamat'] = trim($matches[1]);
                    
                    if (preg_match('/_RT \/ RW_\s*:\s*(.+)/', $text, $matches)) {
                        $rtrw = explode('/', str_replace(' ', '', $matches[1]));
                        if(isset($rtrw[0])) $parsed['rt'] = $rtrw[0];
                        if(isset($rtrw[1])) $parsed['rw'] = $rtrw[1];
                    }
                    
                    if (preg_match('/_IP_\s*:\s*([\d\.]+)/', $text, $matches)) $parsed['ip_address'] = trim($matches[1]);
                    if (preg_match('/_Panjang Kabel_\s*:\s*(\d+)/', $text, $matches)) $parsed['cable_length_m'] = trim($matches[1]);
                    
                    if (preg_match('/_Paket_\s*:\s*([\d\w\s]+)/i', $text, $matches)) {
                        $pkgName = trim($matches[1]);
                        $pkg = InternetPackage::where('nama_paket', 'like', "%{$pkgName}%")->first();
                        if ($pkg) $parsed['internet_package_id'] = $pkg->id;
                    }

                    if (preg_match('/_Desa_\s*:\s*(.+)/', $text, $matches)) {
                        $villageName = trim($matches[1]);
                        $village = Village::where('name', 'like', "%{$villageName}%")->first();
                        if ($village) $parsed['village_id'] = $village->id;
                    }
                    
                    // Create Device if SN ONU is present
                    if (preg_match('/_SN ONU_\s*:\s*([\w\d]+)/', $text, $matches)) {
                        $snOnu = trim($matches[1]);
                        $onuType = 'UNKNOWN';
                        if (preg_match('/_Tipe ONU_\s*:\s*(.+?)(?:\[cite|\n|$)/', $text, $typeMatches)) {
                            $onuType = trim($typeMatches[1]);
                        }
                        
                        // Find or create device
                        $device = \App\Models\Device::firstOrCreate(
                            ['serial_number' => $snOnu],
                            [
                                'device_code' => 'DEV-'.$snOnu,
                                'brand' => 'ZTE', // default assumption
                                'type' => $onuType,
                                'status' => 'dipinjamkan',
                            ]
                        );
                        $parsed['device_id'] = $device->id;
                    }

                    // Set state ke dalam array $data Filament (untuk halaman CreateRecord)
                    $this->data = array_merge($this->data, $parsed);
                    
                    // Juga panggil form->fill agar UI refresh sepenuhnya (opsional tapi disarankan di V3)
                    $this->form->fill($this->data);

                    Notification::make()
                        ->title('Berhasil mem-parsing data WA!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
