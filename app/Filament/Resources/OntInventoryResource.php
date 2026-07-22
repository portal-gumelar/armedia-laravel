<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OntInventoryResource\Pages;
use App\Filament\Resources\OntInventoryResource\RelationManagers;
use App\Models\OntInventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OntInventoryResource extends Resource
{
    protected static ?string $model = OntInventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Jaringan & Infrastruktur';
    protected static ?string $navigationLabel = 'Inventaris ONT';
    protected static ?string $modelLabel = 'Inventaris ONT';
    protected static ?string $pluralModelLabel = 'Inventaris ONT';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Barang')
                    ->schema([
                        Forms\Components\Select::make('tipe')
                            ->options([
                                'Baru' => 'Baru',
                                'Bekas' => 'Bekas',
                                'Rusak' => 'Rusak',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('nama_barang')
                            ->required(),
                        Forms\Components\TextInput::make('merek'),
                        Forms\Components\TextInput::make('sn')
                            ->label('Serial Number (SN)'),
                        Forms\Components\TextInput::make('jumlah')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Tersedia' => 'Tersedia',
                                'Terpakai' => 'Terpakai',
                                'Rusak' => 'Rusak',
                                'Dipinjam' => 'Dipinjam',
                            ])
                            ->default('Tersedia'),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Konfigurasi Jaringan')
                    ->schema([
                        Forms\Components\TextInput::make('mac_address')
                            ->label('MAC Address'),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address'),
                        Forms\Components\TextInput::make('ssid_2g')
                            ->label('SSID 2.4GHz'),
                        Forms\Components\TextInput::make('ssid_5g')
                            ->label('SSID 5GHz'),
                    ])->columns(2)->collapsed(),

                Forms\Components\Section::make('Alokasi / Pengeluaran')
                    ->schema([
                        Forms\Components\DatePicker::make('tgl_keluar')
                            ->label('Tanggal Keluar'),
                        Forms\Components\TextInput::make('teknisi')
                            ->label('Nama Teknisi'),
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(3)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('merek')
                    ->label('Merek')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sn')
                    ->label('SN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')->badge()
                    ->label('Tipe')
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'success',
                        'Bekas' => 'warning',
                        'Rusak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'Tersedia' => 'success',
                        'Terpakai' => 'primary',
                        'Dipinjam' => 'warning',
                        'Rusak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teknisi')
                    ->label('Teknisi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->label('Tgl Keluar')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mac_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOntInventories::route('/'),
            'create' => Pages\CreateOntInventory::route('/create'),
            'edit' => Pages\EditOntInventory::route('/{record}/edit'),
        ];
    }
}
