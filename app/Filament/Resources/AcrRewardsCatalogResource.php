<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcrRewardsCatalogResource\Pages;
use App\Filament\Resources\AcrRewardsCatalogResource\RelationManagers;
use App\Models\AcrRewardsCatalog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcrRewardsCatalogResource extends Resource
{
    protected static ?string $model = AcrRewardsCatalog::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Mitra & Ekosistem';
    protected static ?string $navigationLabel = 'Katalog Hadiah';
    protected static ?string $pluralModelLabel = 'Katalog Hadiah';
    protected static ?string $modelLabel = 'Katalog Hadiah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_hadiah')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('poin_dibutuhkan')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(99),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_hadiah')->searchable(),
                Tables\Columns\TextColumn::make('poin_dibutuhkan')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('stok')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAcrRewardsCatalogs::route('/'),
            'create' => Pages\CreateAcrRewardsCatalog::route('/create'),
            'edit' => Pages\EditAcrRewardsCatalog::route('/{record}/edit'),
        ];
    }
}
