<?php

namespace App\Filament\Resources;

use App\Enums\DeviceStatus;
use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon  = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'Perangkat ONT';
    protected static ?string $pluralModelLabel = 'Perangkat ONT';
    protected static ?string $modelLabel       = 'Perangkat ONT';
    protected static ?string $recordTitleAttribute = 'device_code';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('device_code')
                ->label('Kode Perangkat')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('PG-1522602001'),
            Forms\Components\TextInput::make('name')
                ->label('Nama Perangkat')
                ->required()
                ->default('XPON ONT'),
            Forms\Components\TextInput::make('model')
                ->label('Model')
                ->placeholder('F680C'),
            Forms\Components\TextInput::make('serial_number')
                ->label('Serial Number')
                ->unique(ignoreRecord: true)
                ->placeholder('HWTCXXXXXXXX'),
            Forms\Components\TextInput::make('batch_month_year')
                ->label('Batch (Bulan-Tahun)')
                ->placeholder('2023-06'),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(DeviceStatus::class)
                ->required()
                ->default(DeviceStatus::STOK->value),
            Forms\Components\Select::make('customer_id')
                ->label('Pelanggan')
                ->relationship('customer', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('device_code')
            ->columns([
                Tables\Columns\TextColumn::make('device_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('S/N')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->placeholder('(stok)'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(DeviceStatus::class),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('provision')
                    ->label('Aktivasi OLT')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->modalWidth('4xl')
                    ->form(function (Device $record) {
                        return [
                            Forms\Components\Tabs::make('Provisioning')
                                ->tabs([
                                    Forms\Components\Tabs\Tab::make('Data OLT (ZTE)')
                                        ->schema([
                                            Forms\Components\Grid::make(3)->schema([
                                                Forms\Components\TextInput::make('olt_ip')->label('IP OLT')->required()->default('192.168.100.2'),
                                                Forms\Components\TextInput::make('olt_user')->label('Username OLT')->required()->default('admin'),
                                                Forms\Components\TextInput::make('olt_pass')->label('Password OLT')->password()->required(),
                                            ]),
                                            Forms\Components\Grid::make(3)->schema([
                                                Forms\Components\TextInput::make('port')->label('Port OLT (1-16)')->numeric()->required(),
                                                Forms\Components\TextInput::make('index')->label('Index Kosong')->numeric()->required(),
                                                Forms\Components\Toggle::make('is_replace')->label('Ganti ONT (Replace)')->default(false)->inline(false),
                                            ]),
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('sn')->label('Serial Number (SN)')->default($record->serial_number)->required()->maxLength(12)->minLength(12),
                                                Forms\Components\Select::make('profile')->label('Profile')->options(['20M_UP' => '20M_UP', '30M_UP' => '30M_UP', '50M_UP' => '50M_UP'])->required(),
                                                Forms\Components\TextInput::make('vlan')->label('VLAN')->numeric()->default(1521)->required(),
                                            ]),
                                        ]),
                                    Forms\Components\Tabs\Tab::make('Data Pelanggan & Netwatch')
                                        ->schema([
                                            Forms\Components\Grid::make(3)->schema([
                                                Forms\Components\TextInput::make('nama')->label('Nama Pelanggan')->default($record->customer?->name)->required(),
                                                Forms\Components\TextInput::make('hp')->label('No HP')->default($record->customer?->whatsapp)->required(),
                                                Forms\Components\TextInput::make('ssid')->label('SSID (Nama WiFi)')->required(),
                                            ]),
                                            Forms\Components\Grid::make(4)->schema([
                                                Forms\Components\TextInput::make('ip_address')->label('IP Address')->required(),
                                                Forms\Components\TextInput::make('rt_rw')->label('RT/RW')->required(),
                                                Forms\Components\TextInput::make('desa')->label('Desa')->default($record->customer?->village?->name)->required(),
                                                Forms\Components\TextInput::make('teknisi')->label('Nama Teknisi')->placeholder('Contoh: AGAN')->required(),
                                            ]),
                                            Forms\Components\Fieldset::make('MikroTik Detail')->schema([
                                                Forms\Components\TextInput::make('mikrotik_ip')->label('IP MikroTik')->required()->default('49.156.62.10'),
                                                Forms\Components\TextInput::make('mikrotik_port')->label('Port SSH MikroTik')->numeric()->required()->default(22022),
                                                Forms\Components\TextInput::make('mikrotik_user')->label('Username MikroTik')->required()->default('cs26'),
                                                Forms\Components\TextInput::make('mikrotik_pass')->label('Password MikroTik')->password()->required(),
                                            ])->columns(4),
                                        ]),
                                ]),
                        ];
                    })
                    ->action(function (Device $record, array $data): void {
                        \App\Jobs\ProvisionOltDeviceJob::dispatch($record, $data);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Proses Aktivasi OLT Dimulai')
                            ->body('Perintah sedang dikirim ke OLT dan MikroTik di latar belakang.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit'   => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
