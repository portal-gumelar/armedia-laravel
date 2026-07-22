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
    protected static ?string $navigationGroup = 'Laporan & Keuangan';
    protected static ?string $navigationLabel = 'Biaya Operasional';
    protected static ?string $modelLabel = 'Biaya Operasional';
    protected static ?string $pluralModelLabel = 'Biaya Operasional';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nota')
                    ->label('Nomor Nota / Bukti')
                    ->maxLength(255),
                Forms\Components\TextInput::make('operasional')
                    ->label('Keterangan Operasional')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('qty')
                            ->label('Kuantitas (Qty)')
                            ->numeric()
                            ->default(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $harga = (float) $get('harga_satuan');
                                $qty = (float) $state;
                                $set('total_harga', $harga * $qty);
                            }),
                        Forms\Components\TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $qty = (float) $get('qty');
                                $harga = (float) $state;
                                $set('total_harga', $harga * $qty);
                            }),
                        Forms\Components\TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nota')
                    ->label('Nota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operasional')
                    ->label('Keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Qty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable(),
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
