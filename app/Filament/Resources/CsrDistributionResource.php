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
                Forms\Components\Section::make('Informasi Penerima / Wilayah')
                    ->schema([
                        Forms\Components\TextInput::make('no')
                            ->label('Nomor / ID')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Penerima / Wilayah')
                            ->required(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('provinsi')
                                    ->default('Jawa Tengah'),
                                Forms\Components\TextInput::make('kabupaten')
                                    ->default('Banyumas'),
                                Forms\Components\TextInput::make('kecamatan'),
                                Forms\Components\TextInput::make('desa'),
                                Forms\Components\TextInput::make('rw'),
                                Forms\Components\TextInput::make('rt'),
                            ]),
                    ]),

                Forms\Components\Section::make('Rincian Dana')
                    ->schema([
                        Forms\Components\TextInput::make('total')
                            ->label('Total (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('dana_desa')
                            ->label('Dana Desa (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('dana_rt')
                            ->label('Dana RT (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Status Pencairan')
                    ->schema([
                        Forms\Components\Select::make('status_pencairan')
                            ->label('Status')
                            ->options([
                                'Belum Cair' => 'Belum Cair',
                                'Sudah Cair' => 'Sudah Cair',
                                'Batal' => 'Batal',
                            ])
                            ->default('Belum Cair')
                            ->required(),
                        Forms\Components\DatePicker::make('tgl_bayar')
                            ->label('Tanggal Pembayaran'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Penerima/Wilayah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('desa')
                    ->label('Desa/Kelurahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rt')
                    ->label('RT/RW')
                    ->formatStateUsing(fn ($record) => "RT {$record->rt} / RW {$record->rw}")
                    ->searchable(['rt', 'rw']),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Dana')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dana_desa')
                    ->label('Dana Desa')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dana_rt')
                    ->label('Dana RT')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_pencairan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sudah Cair' => 'success',
                        'Belum Cair' => 'warning',
                        'Batal' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tgl_bayar')
                    ->label('Tgl Bayar')
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
