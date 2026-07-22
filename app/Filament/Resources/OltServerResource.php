<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OltServerResource\Pages;
use App\Filament\Resources\OltServerResource\RelationManagers;
use App\Models\OltServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OltServerResource extends Resource
{
    protected static ?string $model = OltServer::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'Server OLT';
    protected static ?string $pluralModelLabel = 'Server OLT';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('host')
                    ->required(),
                Forms\Components\TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->default(23),
                Forms\Components\TextInput::make('username')
                    ->required(),
                Forms\Components\Textarea::make('password')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('snmp_community')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('host')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListOltServers::route('/'),
            'create' => Pages\CreateOltServer::route('/create'),
            'view' => Pages\ViewOltServer::route('/{record}'),
            'edit' => Pages\EditOltServer::route('/{record}/edit'),
        ];
    }
}
