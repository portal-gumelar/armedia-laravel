<?php

namespace App\Filament\Mitra\Resources;

use App\Filament\Mitra\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $pluralModelLabel = 'Tagihan';
    protected static ?string $navigationGroup = 'Pelanggan & Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->label('Pelanggan')
                ->relationship('customer', 'name',
                    fn (Builder $q) => $q->where('mitra_id', Filament::getTenant()?->id)
                )
                ->searchable()->preload()->required(),
            Forms\Components\DatePicker::make('period')->label('Periode')->required(),
            Forms\Components\TextInput::make('amount')->label('Nominal (Rp)')->numeric()->required(),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(['unpaid' => 'Belum Lunas', 'paid' => 'Lunas'])
                ->default('unpaid'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')->label('No. Invoice')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Pelanggan')->searchable(),
                Tables\Columns\TextColumn::make('period')->label('Periode')->date('M Y'),
                Tables\Columns\TextColumn::make('amount')->label('Nominal')->money('IDR'),
                Tables\Columns\BadgeColumn::make('status')->label('Status')
                    ->colors(['danger' => 'unpaid', 'success' => 'paid']),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('mitra_id', Filament::getTenant()?->id);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
