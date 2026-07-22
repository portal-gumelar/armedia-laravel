<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NetwatchMonitoringResource\Pages;
use App\Models\NetwatchMonitoring;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NetwatchMonitoringResource extends Resource
{
    protected static ?string $model = NetwatchMonitoring::class;
    protected static ?string $navigationIcon = 'heroicon-o-signal-slash';
    protected static ?string $navigationGroup = 'Jaringan & Infrastruktur';
    protected static ?string $navigationLabel = 'Monitoring Netwatch';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('ip_address')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('status_koneksi')->options(['UP' => 'UP', 'DOWN' => 'DOWN']),
            Forms\Components\Select::make('customer_id')->relationship('customer', 'nama')->searchable()->label('Pelanggan'),
            Forms\Components\TextInput::make('desa'),
            Forms\Components\TextInput::make('rw_rt'),
            Forms\Components\TextInput::make('paket_mbps')->numeric(),
            Forms\Components\TextInput::make('status_berlangganan'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('ip_address')->searchable(),
            Tables\Columns\TextColumn::make('status_koneksi')->badge()->color(['success' => 'UP', 'danger' => 'DOWN']),
            Tables\Columns\TextColumn::make('customer.nama')->label('Pelanggan')->searchable(),
            Tables\Columns\TextColumn::make('desa'),
            Tables\Columns\TextColumn::make('paket_mbps')->suffix(' Mbps'),
            Tables\Columns\TextColumn::make('status_berlangganan'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status_koneksi')->options(['UP' => 'UP', 'DOWN' => 'DOWN']),
        ])
        ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNetwatchMonitorings::route('/'),
            'create' => Pages\CreateNetwatchMonitoring::route('/create'),
            'edit' => Pages\EditNetwatchMonitoring::route('/{record}/edit'),
        ];
    }
}
