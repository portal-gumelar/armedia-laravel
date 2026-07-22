<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoiceGeneratorService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Layanan & Pelanggan';
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $pluralModelLabel = 'Tagihan';
    protected static ?string $modelLabel       = 'Tagihan';
    protected static ?int    $navigationSort   = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->label('Pelanggan')
                ->relationship('customer', 'name')
                ->searchable()
                ->preload()
                ->required(),
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
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.village.name')
                    ->label('Desa')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->date('M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tagihan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tgl Bayar')
                    ->date()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(InvoiceStatus::class),
                Tables\Filters\Filter::make('period')
                    ->form([
                        Forms\Components\DatePicker::make('period_month')
                            ->label('Bulan')
                            ->displayFormat('M Y'),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['period_month'])) {
                            $start = Carbon::parse($data['period_month'])->startOfMonth();
                            $query->whereDate('period', $start->toDateString());
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['period_month']) return null;
                        return 'Bulan: ' . Carbon::parse($data['period_month'])->format('M Y');
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate Tagihan Bulan Ini')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('period')
                            ->label('Bulan Tagihan')
                            ->required()
                            ->default(now()->startOfMonth())
                            ->displayFormat('M Y'),
                    ])
                    ->action(function (array $data) {
                        $service = app(InvoiceGeneratorService::class);
                        $stats   = $service->generateForMonth($data['period']);

                        Notification::make()
                            ->title("Tagihan berhasil digenerate")
                            ->body("Dibuat: {$stats['created']} | Diupdate: {$stats['updated']} | Dilewati: {$stats['skipped']}")
                            ->success()
                            ->send();
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
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
                Tables\Actions\Action::make('kirimWa')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('success')
                    ->url(function ($record) {
                        $customer = $record->customer;
                        $phone = $customer?->whatsapp ?? '';
                        
                        // Format nomor WA (pastikan pakai 62)
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        } elseif (!str_starts_with($phone, '62') && !empty($phone)) {
                            $phone = '62' . $phone;
                        }

                        $name = $customer?->name ?? 'Pelanggan';
                        $idPelanggan = $customer?->id_arm ?? '-';
                        $invNo = $record->invoice_no ?? '-';
                        $layanan = $customer?->internetPackage?->nama_paket ?? ($customer?->paket_mbps ? $customer->paket_mbps . ' Mbps' : 'Internet Home');
                        $harga = number_format($record->amount, 0, ',', '.');
                        $periode = $record->period ? $record->period->format('M Y') : '-';
                        $jatuhTempo = $record->due_date ? $record->due_date->format('d-M-Y') : '-';
                        
                        $appUrl = config('app.url');
                        $loginLink = $appUrl . '/member/login';
                        $invoiceLink = $appUrl . '/member/invoices'; // atau link payment langsung jika ada
                        
                        $text = "Yth. Bapak/Ibu *{$name}* 👋✨\n\n"
                            . "Invoice Tagihan internet kamu sudah siap nih! Yuk cek detail lengkapnya:\n\n"
                            . "🆔 *ID Pelanggan*: {$idPelanggan}\n"
                            . "📄 *Nomor Invoice*: {$invNo}\n"
                            . "📶 *Layanan*: {$layanan}\n"
                            . "💳 *Harga Paket*: Rp. {$harga}\n"
                            . "🗓 *Periode*: {$periode}\n"
                            . "⏰ *Jatuh Tempo*: {$jatuhTempo}\n\n"
                            . "💵 *Total Tagihan*: Rp. {$harga}\n\n"
                            . "🔗 Bayar lebih cepat dan mudah:\n"
                            . "👉 {$invoiceLink}\n\n"
                            . "Atau login ke Aplikasi pelanggan:\n"
                            . "🌐 {$loginLink}\n\n"
                            . "- Pembayaran Tunai bisa Ke Office ARMEDIA\n"
                            . "- Keterlambatan pembayaran internet akan diputus sementara dan tagihan tetap berjalan, sesuai tgl pemasangan.\n\n"
                            . "Terima kasih - by Billing\n"
                            . "*CS/Admin ARMEDIA*\n"
                            . "WA : 081234567890 (Hanya CHAT) 📵";
                            
                        return "https://wa.me/{$phone}?text=" . urlencode($text);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer.village'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
