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
use Illuminate\Support\Facades\Auth;
use App\Enums\InvoiceStatus;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Tagihan Saya';
    protected static ?string $modelLabel = 'Tagihan';
    protected static ?string $pluralModelLabel = 'Tagihan Saya';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Members do not use form
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->date('F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tgl Bayar')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('bayar')
                    ->label('Bayar Sekarang')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->status === InvoiceStatus::BELUM)
                    ->modalHeading(fn (Invoice $record) => 'Pembayaran Tagihan ' . $record->invoice_no)
                    ->modalDescription(new \Illuminate\Support\HtmlString('
                        <div class="mt-4 space-y-4">
                            <p class="text-sm">Silakan lakukan transfer sesuai dengan nominal tagihan di bawah ini:</p>
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan:</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp <span x-text="new Intl.NumberFormat(\'id-ID\').format($wire.mountedTableActionsData[0].amount || 0)"></span></p>
                            </div>
                            <div class="space-y-2">
                                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg flex justify-between items-center bg-white dark:bg-gray-900">
                                    <div>
                                        <p class="font-bold">BCA</p>
                                        <p class="text-sm text-gray-500">1234567890 a.n. PT ARMEDIA</p>
                                    </div>
                                </div>
                                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg flex justify-between items-center bg-white dark:bg-gray-900">
                                    <div>
                                        <p class="font-bold">Mandiri</p>
                                        <p class="text-sm text-gray-500">0987654321 a.n. PT ARMEDIA</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Setelah melakukan pembayaran, tagihan Anda akan terkonfirmasi secara otomatis dalam waktu maksimal 1x24 jam. Jika internet Anda terisolir, otomatis akan aktif kembali.
                            </p>
                            <a href="https://wa.me/628211234011?text=Halo%20CS%20ARMEDIA,%20saya%20sudah%20melakukan%20pembayaran" target="_blank" class="mt-4 inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-500">
                                <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4"/>
                                Konfirmasi via WhatsApp CS
                            </a>
                        </div>
                    '))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->mountUsing(function (Forms\ComponentContainer $form, Invoice $record) {
                        $form->fill([
                            'amount' => $record->amount,
                        ]);
                    })
                    ->form([
                        Forms\Components\Hidden::make('amount')
                    ]),
            ])
            ->bulkActions([])
            ->defaultSort('period', 'desc')
            ->emptyStateHeading('Tidak ada tagihan')
            ->emptyStateDescription('Semua tagihan Anda sudah lunas atau belum diterbitkan.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('customer_id', Auth::guard('customer')->id());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
        ];
    }
}
