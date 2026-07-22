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
                Forms\Components\TextInput::make('tipe')
                    ->required(),
                Forms\Components\TextInput::make('nama_barang')
                    ->required(),
                Forms\Components\TextInput::make('merek'),
                Forms\Components\TextInput::make('sn'),
                Forms\Components\TextInput::make('mac_address'),
                Forms\Components\TextInput::make('ip_address'),
                Forms\Components\TextInput::make('ssid_2g'),
                Forms\Components\TextInput::make('ssid_5g'),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('keterangan'),
                Forms\Components\TextInput::make('status'),
                Forms\Components\DatePicker::make('tgl_keluar'),
                Forms\Components\TextInput::make('teknisi'),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merek')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mac_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ssid_2g')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ssid_5g')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teknisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
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
            'index' => Pages\ListOntInventories::route('/'),
            'create' => Pages\CreateOntInventory::route('/create'),
            'edit' => Pages\EditOntInventory::route('/{record}/edit'),
        ];
    }
}
