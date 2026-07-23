<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternetPackageResource\Pages;
use App\Filament\Resources\InternetPackageResource\RelationManagers;
use App\Models\InternetPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternetPackageResource extends Resource
{
    protected static ?string $model = InternetPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'Operasional ISP';
    protected static ?string $navigationLabel = 'Paket / Produk';
    protected static ?string $pluralModelLabel = 'Paket Internet';
    protected static ?string $modelLabel = 'Paket Internet';
    protected static ?string $recordTitleAttribute = 'nama_paket';
    protected static ?int    $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Paket Internet')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Paket')
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: AR-2, HR-11'),
                        Forms\Components\TextInput::make('nama_paket')
                            ->label('Nama Paket')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('brand')
                            ->label('Brand / Kategori')
                            ->options(\App\Enums\PackageBrand::class),
                        Forms\Components\TextInput::make('kecepatan')
                            ->label('Label Kecepatan')
                            ->placeholder('Contoh: Upto 30 Mbps')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('speed_mbps')
                            ->label('Kecepatan Asli (Mbps)')
                            ->numeric()
                            ->placeholder('30'),
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga (Rp)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('ip_allocation')
                            ->label('Alokasi IP Range')
                            ->placeholder('Contoh: 10.152.6-10.152.7'),
                        Forms\Components\TextInput::make('keterangan_promo')
                            ->label('Keterangan / Promo')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_paket')
                    ->label('Nama Paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')->badge()
                    ->label('Brand'),
                Tables\Columns\TextColumn::make('speed_mbps')
                    ->label('Kapasitas (Mbps)')
                    ->state(fn ($record) => $record->speed_mbps ?? $record->kecepatan)
                    ->suffix(' Mbps')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customers_count')
                    ->counts('customers')
                    ->label('Jml Pelanggan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_allocation')
                    ->label('Alokasi IP')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('keterangan_promo')
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
            'index' => Pages\ListInternetPackages::route('/'),
            'create' => Pages\CreateInternetPackage::route('/create'),
            'edit' => Pages\EditInternetPackage::route('/{record}/edit'),
        ];
    }
}
