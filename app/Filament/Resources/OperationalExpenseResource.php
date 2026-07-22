<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperationalExpenseResource\Pages;
use App\Filament\Resources\OperationalExpenseResource\RelationManagers;
use App\Models\OperationalExpense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperationalExpenseResource extends Resource
{
    protected static ?string $model = OperationalExpense::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Pengaturan & Sistem';
    protected static ?string $navigationLabel = 'Biaya Operasional';
    protected static ?string $modelLabel = 'Biaya Operasional';
    protected static ?string $pluralModelLabel = 'Biaya Operasional';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nota'),
                Forms\Components\TextInput::make('operasional')
                    ->required(),
                Forms\Components\TextInput::make('qty'),
                Forms\Components\TextInput::make('harga_satuan')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_harga')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operasional')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
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
            'index' => Pages\ListOperationalExpenses::route('/'),
            'create' => Pages\CreateOperationalExpense::route('/create'),
            'edit' => Pages\EditOperationalExpense::route('/{record}/edit'),
        ];
    }
}
