<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OltPortResource\Pages;
use App\Filament\Resources\OltPortResource\RelationManagers;
use App\Models\OltPort;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OltPortResource extends Resource
{
    protected static ?string $model = OltPort::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'Port OLT';
    protected static ?string $pluralModelLabel = 'Port OLT';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('olt_server_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('slot')
                    ->required(),
                Forms\Components\TextInput::make('port')
                    ->required(),
                Forms\Components\TextInput::make('max_capacity')
                    ->required()
                    ->numeric()
                    ->default(128),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('olt_server_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListOltPorts::route('/'),
            'create' => Pages\CreateOltPort::route('/create'),
            'view' => Pages\ViewOltPort::route('/{record}'),
            'edit' => Pages\EditOltPort::route('/{record}/edit'),
        ];
    }
}
