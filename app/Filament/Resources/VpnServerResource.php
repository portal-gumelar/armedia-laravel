<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VpnServerResource\Pages;
use App\Filament\Resources\VpnServerResource\RelationManagers;
use App\Models\VpnServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VpnServerResource extends Resource
{
    protected static ?string $model = VpnServer::class;

    protected static ?string $navigationGroup = 'Layanan & Produk';
    protected static ?string $navigationLabel = 'VPN Server (CHR)';
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('host')
                    ->required()
                    ->maxLength(255)
                    ->label('IP Publik CHR'),
                Forms\Components\TextInput::make('port')
                    ->numeric()
                    ->default(8728)
                    ->label('API Port'),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'mikrotik_chr' => 'MikroTik CHR',
                        'linux_wg' => 'Linux WireGuard',
                    ])
                    ->required()
                    ->default('mikrotik_chr'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
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
            'index' => Pages\ListVpnServers::route('/'),
            'create' => Pages\CreateVpnServer::route('/create'),
            'edit' => Pages\EditVpnServer::route('/{record}/edit'),
        ];
    }
}
