<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcrPointTransactionResource\Pages;
use App\Filament\Resources\AcrPointTransactionResource\RelationManagers;
use App\Models\AcrPointTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcrPointTransactionResource extends Resource
{
    protected static ?string $model = AcrPointTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Mitra & Ekosistem';
    protected static ?string $navigationLabel = 'Riwayat Transaksi Poin';
    protected static ?string $pluralModelLabel = 'Riwayat Transaksi Poin';
    protected static ?string $modelLabel = 'Transaksi Poin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_member')
                    ->label('Member ACR')
                    ->relationship('member', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('jenis')
                    ->label('Jenis Transaksi')
                    ->options([
                        'tambah' => 'Tambah Poin (+)',
                        'tukar' => 'Tukar Poin (-)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jumlah_poin')
                    ->label('Jumlah Poin')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.nama')
                    ->label('Nama Member')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tambah' => 'success',
                        'tukar' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tambah' => 'Tambah',
                        'tukar' => 'Tukar',
                        default => $state,
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_poin')
                    ->label('Jumlah Poin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(30),
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
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
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
            'index' => Pages\ListAcrPointTransactions::route('/'),
            'create' => Pages\CreateAcrPointTransaction::route('/create'),
            'edit' => Pages\EditAcrPointTransaction::route('/{record}/edit'),
        ];
    }
}
