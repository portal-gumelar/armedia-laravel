<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'Operasional ISP';
    protected static ?string $navigationLabel = 'Paket / Produk';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('kode')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\TextInput::make('kapasitas_mbps')->numeric()->suffix('Mbps')->required(),
            Forms\Components\TextInput::make('harga')->numeric()->prefix('Rp')->required(),
            Forms\Components\TextInput::make('alokasi_ip'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('kode')->sortable(),
            Tables\Columns\TextColumn::make('nama')->searchable(),
            Tables\Columns\TextColumn::make('kapasitas_mbps')->suffix(' Mbps'),
            Tables\Columns\TextColumn::make('harga')->money('IDR'),
            Tables\Columns\TextColumn::make('customers_count')->counts('customers')->label('Jml Pelanggan'),
            Tables\Columns\TextColumn::make('alokasi_ip'),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
