<?php

namespace App\Filament\Resources;

use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Operasional ISP';
    protected static ?string $navigationLabel = 'Stok Perangkat (ONT)';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\TextInput::make('model'),
            Forms\Components\TextInput::make('kode_id')->label('ID Perangkat')->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('sn')->label('S/N'),
            Forms\Components\DatePicker::make('tgl_ambil_dari_stok'),
            Forms\Components\Select::make('status')->options([
                'STOK' => 'Stok', 'TERPASANG' => 'Terpasang',
            ]),
            Forms\Components\Select::make('customer_id')->relationship('customer', 'nama')->searchable()->label('Pelanggan'),
            Forms\Components\Select::make('kondisi')->options([
                'Baik' => 'Baik', 'Rusak' => 'Rusak',
            ]),
            Forms\Components\Textarea::make('catatan'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('kode_id')->label('ID')->searchable(),
            Tables\Columns\TextColumn::make('sn')->label('S/N')->searchable(),
            Tables\Columns\TextColumn::make('model'),
            Tables\Columns\BadgeColumn::make('status')->colors(['success' => 'TERPASANG', 'gray' => 'STOK']),
            Tables\Columns\TextColumn::make('customer.nama')->label('Pelanggan'),
            Tables\Columns\TextColumn::make('kondisi'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')->options(['STOK' => 'Stok', 'TERPASANG' => 'Terpasang']),
        ])
        ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
