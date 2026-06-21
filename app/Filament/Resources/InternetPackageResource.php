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
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Paket Internet';
    protected static ?string $pluralModelLabel = 'Paket Internet';
    protected static ?string $modelLabel = 'Paket Internet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_paket')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kecepatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('keterangan_promo')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecepatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan_promo')
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
            'index' => Pages\ListInternetPackages::route('/'),
            'create' => Pages\CreateInternetPackage::route('/create'),
            'edit' => Pages\EditInternetPackage::route('/{record}/edit'),
        ];
    }
}
