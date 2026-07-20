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
    protected static ?string $navigationGroup = 'Operasional ISP';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $pluralModelLabel = 'Pelanggan';
    protected static ?string $modelLabel       = 'Pelanggan';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int    $navigationSort   = 2;

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
