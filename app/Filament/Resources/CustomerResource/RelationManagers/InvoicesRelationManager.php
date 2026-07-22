<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Enums\InvoiceStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';
    protected static ?string $title       = 'Tagihan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('period')
                ->label('Periode')
                ->required()
                ->displayFormat('M Y'),
            Forms\Components\TextInput::make('amount')
                ->label('Jumlah (Rp)')
                ->required()
                ->numeric(),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(InvoiceStatus::class)
                ->default(InvoiceStatus::BELUM->value)
                ->required(),
            Forms\Components\DatePicker::make('paid_at')
                ->label('Tanggal Bayar'),
            Forms\Components\Select::make('payment_method')
                ->label('Metode Pembayaran')
                ->options([
                    'tunai'    => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris'     => 'QRIS',
                ])
                ->nullable(),
            Forms\Components\Textarea::make('notes')
                ->label('Catatan')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->date('M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tagihan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tgl Bayar')
                    ->date()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(InvoiceStatus::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Tagihan'),
            ])
            ->actions([
                Tables\Actions\Action::make('lunas')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== InvoiceStatus::LUNAS)
                    ->action(fn ($record) => $record->update([
                        'status'  => InvoiceStatus::LUNAS->value,
                        'paid_at' => now()->toDateString(),
                    ])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
