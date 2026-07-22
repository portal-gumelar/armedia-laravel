<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CsrDistributionResource\Pages;
use App\Filament\Resources\CsrDistributionResource\RelationManagers;
use App\Models\CsrDistribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CsrDistributionResource extends Resource
{
    protected static ?string $model = CsrDistribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Mitra & Ekosistem';
    protected static ?string $navigationLabel = 'Program CSR';
    protected static ?string $modelLabel = 'Program CSR';
    protected static ?string $pluralModelLabel = 'Program CSR';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no')
                    ->numeric(),
                Forms\Components\TextInput::make('nama'),
                Forms\Components\TextInput::make('provinsi'),
                Forms\Components\TextInput::make('kabupaten'),
                Forms\Components\TextInput::make('kecamatan'),
                Forms\Components\TextInput::make('desa'),
                Forms\Components\TextInput::make('rw'),
                Forms\Components\TextInput::make('rt'),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('dana_desa')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('dana_rt')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('status_pencairan')
                    ->required(),
                Forms\Components\DatePicker::make('tgl_bayar'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dana_desa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dana_rt')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_pencairan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_bayar')
                    ->date()
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
            'index' => Pages\ListCsrDistributions::route('/'),
            'create' => Pages\CreateCsrDistribution::route('/create'),
            'edit' => Pages\EditCsrDistribution::route('/{record}/edit'),
        ];
    }
}
