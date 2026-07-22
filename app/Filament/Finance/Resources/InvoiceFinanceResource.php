<?php

namespace App\Filament\Finance\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Finance\Resources\InvoiceFinanceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceFinanceResource extends Resource
{
    protected static ?string $model            = Invoice::class;
    protected static ?string $modelLabel       = 'Invoice';
    protected static ?string $pluralModelLabel = 'Daftar Invoice';
    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Keuangan';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Status Pembayaran')
                ->options(InvoiceStatus::class)
                ->required(),
            Forms\Components\DatePicker::make('paid_at')
                ->label('Tanggal Bayar')
                ->native(false),
            Forms\Components\TextInput::make('payment_method')
                ->label('Metode Pembayaran'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label('No. Invoice')->searchable()->copyable()->weight('bold'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')->searchable()
                    ->description(fn($r) => $r->customer?->id_arm),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')->date('M Y')->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tgl Bayar')->date('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(InvoiceStatus::class),
                Tables\Filters\Filter::make('period')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Bulan'),
                        Forms\Components\DatePicker::make('to')->label('Sampai Bulan'),
                    ])
                    ->query(fn(Builder $q, array $data) => $q
                        ->when($data['from'], fn($q, $v) => $q->whereDate('period', '>=', $v))
                        ->when($data['to'],   fn($q, $v) => $q->whereDate('period', '<=', $v))
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('send_wa')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Notifikasi Tagihan WA')
                    ->modalDescription('Kirim pesan pengingat tagihan ke WhatsApp pelanggan ini via WAHA?')
                    ->action(function (Invoice $record) {
                        $phone = $record->customer->whatsapp ?? $record->customer->phone ?? '';
                        if (empty($phone)) {
                            \Filament\Notifications\Notification::make()->title('Gagal')->body('Nomor WA Pelanggan tidak ditemukan.')->danger()->send();
                            return;
                        }
                        
                        $amount = number_format($record->amount, 0, ',', '.');
                        $period = \Carbon\Carbon::parse($record->period)->translatedFormat('F Y');
                        $status = $record->status->value;
                        
                        $msg = "Halo *{$record->customer->name}*,\n\nInformasi tagihan internet ARMEDIA Anda:\n- Periode: *{$period}*\n- Tagihan: *Rp {$amount}*\n- Status: *" . ($status === 'lunas' ? 'LUNAS ✅' : 'BELUM BAYAR ⏳') . "*\n\n" . ($status === 'lunas' ? "Terima kasih atas pembayaran Anda!" : "Mohon segera melakukan pembayaran untuk menghindari isolir jaringan. Terima kasih.");
                        
                        $success = \App\Services\WhatsAppService::sendMessage($phone, $msg);
                        
                        if ($success) {
                            \Filament\Notifications\Notification::make()->title('Berhasil')->body('Notifikasi WA Tagihan telah terkirim!')->success()->send();
                        } else {
                            \Filament\Notifications\Notification::make()->title('Gagal')->body('Pastikan server WAHA aktif.')->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Invoice $r) => $r->status !== InvoiceStatus::LUNAS)
                    ->form([
                        Forms\Components\DatePicker::make('paid_at')->label('Tanggal Bayar')->default(now())->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Bayar')
                            ->options(['tunai'=>'Tunai','transfer'=>'Transfer Bank','qris'=>'QRIS','midtrans'=>'Midtrans'])
                            ->required(),
                    ])
                    ->action(fn(Invoice $r, array $data) => $r->update([
                        'status'         => InvoiceStatus::LUNAS->value,
                        'paid_at'        => $data['paid_at'],
                        'payment_method' => $data['payment_method'],
                    ])),
                Tables\Actions\EditAction::make()->label('Edit Status'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceFinance::route('/'),
            'edit'  => Pages\EditInvoiceFinance::route('/{record}/edit'),
        ];
    }
}
