<?php

namespace App\Filament\Member\Resources;

use App\Filament\Member\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
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
    protected static ?string $navigationLabel = 'Tagihan Saya';
    protected static ?string $pluralModelLabel = 'Tagihan';
    protected static ?string $modelLabel = 'Tagihan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_no')
                    ->label('Nomor Tagihan')
                    ->disabled(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->disabled(),
                Forms\Components\TextInput::make('amount')
                    ->label('Total (Rp)')
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid' => 'Belum Lunas',
                        'paid' => 'Lunas',
                    ])
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label('Nomor Tagihan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->date('M Y'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total (Rp)')
                    ->money('IDR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'unpaid',
                        'success' => 'paid',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    // Hanya tampilkan tagihan milik pelanggan yang sedang login
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customer_id', auth('customer')->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
        ];
    }
}
