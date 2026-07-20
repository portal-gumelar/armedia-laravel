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
