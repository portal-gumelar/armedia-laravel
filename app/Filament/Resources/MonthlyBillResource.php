<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyBillResource\Pages;
use App\Filament\Resources\MonthlyBillResource\RelationManagers;
use App\Models\MonthlyBill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonthlyBillResource extends Resource
{
    protected static ?string $model = MonthlyBill::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'Layanan & Pelanggan';
    protected static ?string $navigationLabel = 'Tagihan Bulanan';
    protected static ?string $modelLabel = 'Tagihan Bulanan';
    protected static ?string $pluralModelLabel = 'Tagihan Bulanan';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bulan')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('harga_acuan_snapshot')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bulan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_acuan_snapshot')
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
            'index' => Pages\ListMonthlyBills::route('/'),
            'create' => Pages\CreateMonthlyBill::route('/create'),
            'edit' => Pages\EditMonthlyBill::route('/{record}/edit'),
        ];
    }
}
