<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OnuDeviceResource\Pages;
use App\Filament\Resources\OnuDeviceResource\RelationManagers;
use App\Models\OnuDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OnuDeviceResource extends Resource
{
    protected static ?string $model = OnuDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'Data ONU/ONT Aktif';
    protected static ?string $pluralModelLabel = 'Data ONU/ONT Aktif';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('olt_port_id')
                    ->relationship('oltPort', 'port')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('sn')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('onu_id')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                        'los' => 'LOS (Putus)',
                    ])
                    ->required()
                    ->default('offline'),
                Forms\Components\TextInput::make('rx_power')
                    ->numeric()
                    ->step('0.01'),
                Forms\Components\DateTimePicker::make('last_online_at'),
                Forms\Components\DateTimePicker::make('last_offline_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('olt_port_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('onu_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rx_power')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_online_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_offline_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOnuDevices::route('/'),
            'create' => Pages\CreateOnuDevice::route('/create'),
            'view' => Pages\ViewOnuDevice::route('/{record}'),
            'edit' => Pages\EditOnuDevice::route('/{record}/edit'),
        ];
    }
}
