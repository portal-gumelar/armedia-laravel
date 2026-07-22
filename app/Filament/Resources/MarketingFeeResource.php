<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketingFeeResource\Pages;
use App\Models\MarketingFee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketingFeeResource extends Resource
{
    protected static ?string $model = MarketingFee::class;

    protected static ?string $navigationIcon  = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Pengaturan & Sistem';
    protected static ?string $navigationLabel = 'Fee Marketing';
    protected static ?string $pluralModelLabel = 'Fee Marketing';
    protected static ?string $modelLabel       = 'Fee Marketing';
    protected static ?string $recordTitleAttribute = 'marketing_name';
    protected static ?int    $navigationSort   = 9;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('marketing_name')
                ->label('Nama Marketing')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('location')
                ->label('Lokasi')
                ->maxLength(255),
            Forms\Components\Select::make('customer_id')
                ->label('Pelanggan')
                ->relationship('customer', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            Forms\Components\TextInput::make('client_name')
                ->label('Nama Klien')
                ->maxLength(255),
            Forms\Components\TextInput::make('fee_amount')
                ->label('Jumlah Fee (Rp)')
                ->required()
                ->numeric()
                ->default(0),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending'  => 'Pending',
                    'dibayar'  => 'Dibayar',
                    'batal'    => 'Batal',
                ])
                ->default('pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('marketing_name')
                    ->label('Marketing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Nama Klien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee_amount')
                    ->label('Fee')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->label('Status')
                    ->color([
                        'warning' => 'pending',
                        'success' => 'dibayar',
                        'danger'  => 'batal',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'dibayar' => 'Dibayar',
                        'batal'   => 'Batal',
                    ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMarketingFees::route('/'),
            'create' => Pages\CreateMarketingFee::route('/create'),
            'edit'   => Pages\EditMarketingFee::route('/{record}/edit'),
        ];
    }
}
