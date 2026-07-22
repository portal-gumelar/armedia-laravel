<?php

namespace App\Filament\Resources;

use App\Enums\CustomerSubscriptionStatus;
use App\Enums\MonitoringStatus;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Odp;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Layanan & Pelanggan';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $pluralModelLabel = 'Pelanggan';
    protected static ?string $modelLabel       = 'Pelanggan';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int    $navigationSort   = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Laporan Teknisi (Auto-Parse)')
                ->description('Paste Laporan Instalasi dari WA ke kotak ini. Sistem akan membaca dan mengisi field form secara otomatis.')
                ->schema([
                    Forms\Components\Textarea::make('wa_report')
                        ->label('Paste Laporan WA')
                        ->placeholder("Contoh:\n*_--- DATA LAPANGAN ---_*\n📝 *_Tanggal Pendaftaran_* : 9 Juli 2026\n...")
                        ->rows(5)
                        ->columnSpanFull()
                        ->live(debounce: 1000)
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            if (!$state) return;
                            
                            if (preg_match('/Nama\s*:\s*(.+)/i', $state, $m)) $set('name', trim($m[1]));
                            if (preg_match('/NIK\s*:\s*(\d+)/i', $state, $m)) $set('nik', trim($m[1]));
                            if (preg_match('/No Telp\s*:\s*(.+)/i', $state, $m)) $set('whatsapp', trim($m[1]));
                            if (preg_match('/RT \/ RW\s*:\s*(\d+)\s*\/\s*(\d+)/i', $state, $m)) {
                                $set('rt', trim($m[1]));
                                $set('rw', trim($m[2]));
                            }
                            if (preg_match('/Alamat\s*:\s*(.+)/i', $state, $m)) $set('alamat', trim($m[1]));
                            
                            if (preg_match('/Desa\s*:\s*(.+)/i', $state, $m)) {
                                $village = \App\Models\Village::where('name', 'like', "%" . trim($m[1]) . "%")->first();
                                if ($village) $set('village_id', $village->id);
                            }
                            if (preg_match('/Panjang Kabel\s*:\s*(\d+)/i', $state, $m)) $set('cable_length_m', (int)trim($m[1]));
                            if (preg_match('/IP\s*:\s*([\d\.]+)/i', $state, $m)) $set('ip_address', trim($m[1]));
                            if (preg_match('/Port OLT\s*:\s*(.+)/i', $state, $m)) $set('pon_olt', trim($m[1]));
                            
                            if (preg_match('/SN ONU\s*:\s*([A-Za-z0-9]+)/i', $state, $m)) {
                                $device = \App\Models\Device::where('serial_number', trim($m[1]))->first();
                                if ($device) $set('device_id', $device->id);
                            }
                            
                            if (preg_match('/Paket\s*:\s*(\d+)/i', $state, $m)) {
                                $pkg = \App\Models\InternetPackage::where('speed_mbps', (int)trim($m[1]))->first();
                                if ($pkg) $set('internet_package_id', $pkg->id);
                            }
                            
                            if (preg_match('/Tgl & Jam Aktif Internet\s*:\s*(\d{1,2})\s*([a-zA-Z]+)\s*(\d{4})/i', $state, $m)) {
                                $months = ['januari'=>1, 'februari'=>2, 'maret'=>3, 'april'=>4, 'mei'=>5, 'juni'=>6, 'juli'=>7, 'agustus'=>8, 'september'=>9, 'oktober'=>10, 'november'=>11, 'desember'=>12, 'jan'=>1, 'feb'=>2, 'mar'=>3, 'apr'=>4, 'jun'=>6, 'jul'=>7, 'agu'=>8, 'sep'=>9, 'okt'=>10, 'nov'=>11, 'des'=>12];
                                $monthStr = strtolower(trim($m[2]));
                                if (isset($months[$monthStr])) {
                                    $set('activated_at', sprintf('%04d-%02d-%02d', $m[3], $months[$monthStr], $m[1]));
                                }
                            }
                            
                            if (preg_match('/Link Maps\s*:\s*(http\S+)/i', $state, $m)) $set('maps_url', trim($m[1]));
                            if (preg_match('/Link Folder\s*:\s*(http\S+)/i', $state, $m)) $set('drive_folder_url', trim($m[1]));
                        })
                        ->dehydrated(false),
                ]),
                
            Forms\Components\Section::make('Data Pelanggan')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('id_arm')
                        ->label('ID ARM')
                        ->unique(ignoreRecord: true)
                        ->placeholder('ARM-0001')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('id_lama')
                        ->label('ID Lama')
                        ->placeholder('G-152260')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('whatsapp')
                        ->label('No. WhatsApp')
                        ->tel()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')
                        ->maxLength(16),
                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat Lengkap')
                        ->columnSpanFull()
                        ->rows(2),
                    Forms\Components\TextInput::make('maps_url')
                        ->label('Link Google Maps')
                        ->url()
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('drive_folder_url')
                        ->label('Link Folder Drive')
                        ->url()
                        ->maxLength(255)
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Lokasi')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('village_id')
                        ->label('Desa')
                        ->relationship('village', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('kecamatan')
                        ->label('Kecamatan')
                        ->default('GUMELAR'),
                    Forms\Components\TextInput::make('rw')
                        ->label('RW')
                        ->maxLength(10),
                    Forms\Components\TextInput::make('rt')
                        ->label('RT')
                        ->maxLength(10),
                ]),

            Forms\Components\Section::make('Data Teknis ISP')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('internet_package_id')
                        ->label('Paket Internet')
                        ->relationship('internetPackage', 'nama_paket')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('odp_id')
                        ->label('ODP')
                        ->relationship('odp', 'code')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('mikrotik_server_id')
                        ->label('Mikrotik Server (Isolir Otomatis)')
                        ->relationship('mikrotikServer', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('pppoe_username')
                        ->label('PPPoE Username')
                        ->placeholder('nama-pelanggan'),
                    Forms\Components\TextInput::make('pppoe_password')
                        ->label('PPPoE Password')
                        ->password()
                        ->revealable(),
                    Forms\Components\Select::make('device_id')
                        ->label('Perangkat ONT')
                        ->relationship('device', 'device_code')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('ip_address')
                        ->label('IP Address')
                        ->placeholder('10.152.6.30'),
                    Forms\Components\TextInput::make('pon_olt')
                        ->label('PON OLT')
                        ->placeholder('1/1/3:2'),
                    Forms\Components\TextInput::make('cable_length_m')
                        ->label('Panjang Kabel (m)')
                        ->numeric(),
                    Forms\Components\DatePicker::make('activated_at')
                        ->label('Tanggal Aktif'),
                    Forms\Components\Select::make('subscription_status')
                        ->label('Status Langganan')
                        ->options(CustomerSubscriptionStatus::class)
                        ->default(CustomerSubscriptionStatus::AKTIF->value),
                    Forms\Components\Select::make('financial_status')
                        ->label('Status Keuangan / Tagihan')
                        ->options([
                            'active' => 'Aktif (Lunas)',
                            'arrears' => 'Nunggak (Jatuh Tempo)',
                            'suspended' => 'Isolir',
                        ])
                        ->default('active')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id_arm')
            ->columns([
                Tables\Columns\TextColumn::make('id_arm')
                    ->label('ID ARM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internetPackage.nama_paket')
                    ->label('Paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('subscription_status')
                    ->label('Status Langganan')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('monitoring_status')
                    ->label('Monitoring')
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('activated_at')
                    ->label('Tanggal Aktif')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_status')
                    ->label('Status Langganan')
                    ->options(CustomerSubscriptionStatus::class),
                Tables\Filters\SelectFilter::make('monitoring_status')
                    ->label('Status Monitoring')
                    ->options(MonitoringStatus::class),
                Tables\Filters\SelectFilter::make('village_id')
                    ->label('Desa')
                    ->relationship('village', 'name'),
                Tables\Filters\SelectFilter::make('internet_package_id')
                    ->label('Paket')
                    ->relationship('internetPackage', 'nama_paket'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('auto_provisioning')
                    ->label('Auto Provisioning')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('olt_server_id')
                            ->label('Pilih OLT Server')
                            ->options(\App\Models\OltServer::pluck('name', 'id'))
                            ->required(),
                        \Filament\Forms\Components\Select::make('port')
                            ->label('Port GPON OLT')
                            ->options(array_combine(range(1, 16), range(1, 16)))
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('sn')
                            ->label('Serial Number (SN) Modem')
                            ->placeholder('ZXIC074F3BEF')
                            ->length(12)
                            ->required(),
                        \Filament\Forms\Components\Select::make('profile')
                            ->label('TCONT Profile / Paket')
                            ->options([
                                '10M_UP' => '10M_UP',
                                '20M_UP' => '20M_UP',
                                '30M_UP' => '30M_UP',
                                '50M_UP' => '50M_UP',
                                '100M_UP' => '100M_UP',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('vlan')
                            ->label('VLAN')
                            ->default('1521')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address Netwatch')
                            ->default(fn ($record) => $record->ip_address)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('ssid')
                            ->label('SSID WiFi')
                            ->default(fn ($record) => 'Pelanggan ' . $record->name),
                    ])
                    ->action(function ($record, array $data, \Filament\Notifications\Notification $notification) {
                        try {
                            $olt = \App\Models\OltServer::find($data['olt_server_id']);
                            $oltService = new \App\Services\ZteOltService($olt);
                            
                            // 1. Cari index kosong di OLT
                            $index = $oltService->findEmptyIndex((int) $data['port']);
                            
                            // 2. Eksekusi registrasi OLT
                            $phone = $record->whatsapp ?? '0000';
                            $result = $oltService->registerOnu(
                                $data['port'], 
                                $index, 
                                $data['sn'], 
                                $data['profile'], 
                                $record->name, 
                                $data['ssid'], 
                                $data['ip_address'], 
                                $phone, 
                                $data['vlan']
                            );
                            
                            if (!$result['success']) {
                                throw new \Exception($result['message']);
                            }
                            
                            // 3. Simpan data PON OLT & IP ke database
                            $record->update([
                                'pon_olt' => "1/1/{$data['port']}:{$index}",
                                'ip_address' => $data['ip_address']
                            ]);
                            
                            // 4. Register ke Netwatch Mikrotik
                            if ($record->mikrotikServer) {
                                $mtService = new \App\Services\MikrotikService();
                                $comment = "{$record->name} - {$data['sn']} - {$phone} - {$data['ssid']} - {$record->rt}/{$record->rw} - {$record->village?->name}";
                                $mtService->addNetwatchHost($record->mikrotikServer, $data['ip_address'], $comment);
                            }
                            
                            $notification->title('Auto Provisioning Berhasil!')
                                ->body("SN {$data['sn']} terdaftar di Port {$data['port']} Index {$index} OLT.")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            $notification->title('Gagal Auto Provisioning')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('ubah_bandwidth')
                    ->label('Ubah Bandwidth')
                    ->icon('heroicon-o-arrows-up-down')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Select::make('internet_package_id')
                            ->label('Paket Internet Baru')
                            ->options(\App\Models\InternetPackage::pluck('nama_paket', 'id'))
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('mikrotik_profile')
                            ->label('Nama Profil PPPoE di MikroTik')
                            ->placeholder('Contoh: 50M_UP')
                            ->required()
                            ->helperText('Pastikan nama profil persis dengan yang ada di /ppp/profile MikroTik'),
                    ])
                    ->action(function ($record, array $data, \Filament\Notifications\Notification $notification) {
                        try {
                            $record->update([
                                'internet_package_id' => $data['internet_package_id']
                            ]);
                            
                            $mtService = new \App\Services\MikrotikService();
                            $success = $mtService->updateSecretProfile($record, $data['mikrotik_profile']);
                            
                            // 🚀 [FreeRADIUS] Sinkronisasi Paket
                            if ($record->username_pppoe) {
                                $radius = new \App\Services\RadiusService();
                                $radius->changeProfile($record->username_pppoe, $data['mikrotik_profile']);
                            }

                            if ($success) {
                                $notification->title('Bandwidth Berhasil Diubah')
                                    ->body("Profil PPPoE MikroTik diperbarui ke {$data['mikrotik_profile']} dan sesi telah di-reconnect.")
                                    ->success()
                                    ->send();
                            } else {
                                $notification->title('Sebagian Berhasil')
                                    ->body('Paket database berubah, namun sinkronisasi MikroTik gagal. Cek koneksi server.')
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            $notification->title('Gagal Mengubah Bandwidth')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('reboot_modem')
                    ->label('Reboot Modem')
                    ->icon('heroicon-o-power')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reboot Modem (ONT) Pelanggan')
                    ->modalDescription('Apakah Anda yakin ingin me-restart modem milik pelanggan ini secara remote? Internet akan terputus sesaat.')
                    ->visible(fn ($record) => !empty($record->pon_olt) && $record->olt_server_id)
                    ->action(function ($record, \Filament\Notifications\Notification $notification) {
                        try {
                            // Extract port and index from pon_olt e.g. "1/1/3:12"
                            if (preg_match('/1\/1\/(\d+):(\d+)/', $record->pon_olt, $matches)) {
                                $port = $matches[1];
                                $index = $matches[2];
                                
                                $olt = \App\Models\OltServer::find($record->olt_server_id);
                                $oltService = new \App\Services\ZteOltService($olt);
                                $success = $oltService->rebootOnu($port, $index);
                                
                                if ($success) {
                                    $notification->title('Perintah Reboot Dikirim')
                                        ->body("Modem pada OLT Port {$port} Index {$index} sedang di-restart.")
                                        ->success()
                                        ->send();
                                } else {
                                    throw new \Exception("Gagal mengeksekusi reboot di OLT.");
                                }
                            } else {
                                throw new \Exception("Format PON OLT tidak valid. Harus 1/1/PORT:INDEX.");
                            }
                        } catch (\Exception $e) {
                            $notification->title('Gagal Reboot Modem')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('cek_redaman')
                    ->label('Cek Redaman')
                    ->icon('heroicon-o-signal')
                    ->color('info')
                    ->visible(fn ($record) => !empty($record->pon_olt) && $record->olt_server_id)
                    ->action(function ($record, \Filament\Notifications\Notification $notification) {
                        try {
                            if (preg_match('/1\/1\/(\d+):(\d+)/', $record->pon_olt, $matches)) {
                                $port = $matches[1];
                                $index = $matches[2];
                                
                                $olt = \App\Models\OltServer::find($record->olt_server_id);
                                $oltService = new \App\Services\ZteOltService($olt);
                                $rx = $oltService->checkAttenuation($port, $index);
                                
                                if ($rx !== null) {
                                    $val = (float) $rx;
                                    $status = 'Aman 🟢';
                                    if ($val < -27) {
                                        $status = 'Kritis 🔴';
                                    } elseif ($val < -24) {
                                        $status = 'Waspada 🟡';
                                    }
                                    
                                    $notification->title('Status Redaman Modem')
                                        ->body("Nilai Rx Power: **{$rx} dBm**\nStatus: {$status}")
                                        ->success()
                                        ->send();
                                } else {
                                    throw new \Exception("Gagal membaca redaman dari OLT. Modem mungkin LOS atau OLT tidak merespons.");
                                }
                            }
                        } catch (\Exception $e) {
                            $notification->title('Gagal Cek Redaman')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('chat_wa')
                    ->label('Sapa / Chat WA')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('info')
                    ->url(function ($record) {
                        $phone = $record->whatsapp ?? $record->phone ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $text = "Halo Bapak/Ibu *{$record->name}*,\nIni dengan CS ARMEDIA. Ada yang bisa kami bantu terkait layanan internet Anda di rumah?";
                        return "https://wa.me/{$phone}?text=" . urlencode($text);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->whatsapp) || !empty($record->phone)),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['village', 'internetPackage'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\TicketsRelationManager::class,
            RelationManagers\NetwatchLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
