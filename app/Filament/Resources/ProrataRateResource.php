<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProrataRateResource\Pages;
use App\Filament\Resources\ProrataRateResource\RelationManagers;
use App\Models\ProrataRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProrataRateResource extends Resource
{
    protected static ?string $model = ProrataRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Pengaturan & Sistem';
    protected static ?string $navigationLabel = 'Tarif Prorata';
    protected static ?string $modelLabel = 'Tarif Prorata';
    protected static ?string $pluralModelLabel = 'Tarif Prorata';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tanggal_pasang')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'id')
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pasang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
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
            'index' => Pages\ListProrataRates::route('/'),
            'create' => Pages\CreateProrataRate::route('/create'),
            'edit' => Pages\EditProrataRate::route('/{record}/edit'),
        ];
    }
}
