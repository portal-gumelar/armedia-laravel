<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VpnAccountResource\Pages;
use App\Filament\Resources\VpnAccountResource\RelationManagers;
use App\Models\VpnAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VpnAccountResource extends Resource
{
    protected static ?string $model = VpnAccount::class;

    protected static ?string $navigationGroup = 'Layanan & Produk';
    protected static ?string $navigationLabel = 'Akun VPN Remote';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun VPN Remote')
                    ->schema([
                        Forms\Components\Select::make('vpn_server_id')
                            ->relationship('vpnServer', 'name')
                            ->searchable()
                            ->label('Server VPN Induk')
                            ->required(),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->label('Untuk Pelanggan (Opsional)'),
                        Forms\Components\Select::make('mikrotik_server_id')
                            ->relationship('mikrotikServer', 'name')
                            ->searchable()
                            ->label('Untuk Server MikroTik (Opsional)'),
                        Forms\Components\TextInput::make('username')
                            ->label('Username VPN')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Password VPN')
                            ->required()
                            ->maxLength(255)
                            ->password()
                            ->revealable(),
                        Forms\Components\TextInput::make('ip_lokal')
                            ->label('IP Lokal yang didapat (Remote Address)')
                            ->placeholder('Contoh: 10.10.10.2')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('port_forwarding')
                            ->label('Port Forwarding (Opsional)')
                            ->numeric(),
                        Forms\Components\Select::make('vpn_type')
                            ->label('Tipe VPN')
                            ->options([
                                'l2tp' => 'L2TP/IPSec',
                                'sstp' => 'SSTP',
                                'wireguard' => 'WireGuard',
                            ])
                            ->required()
                            ->default('l2tp'),
                        Forms\Components\DateTimePicker::make('expired_at')
                            ->label('Berlaku Sampai (Expired)'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vpnServer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_lokal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vpn_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expired_at')
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
            'index' => Pages\ListVpnAccounts::route('/'),
            'create' => Pages\CreateVpnAccount::route('/create'),
            'edit' => Pages\EditVpnAccount::route('/{record}/edit'),
        ];
    }
}
