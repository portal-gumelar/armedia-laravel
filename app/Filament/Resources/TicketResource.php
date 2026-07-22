<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model              = Ticket::class;
    protected static ?string $modelLabel         = 'Tiket Pengaduan';
    protected static ?string $pluralModelLabel   = 'Tiket Pengaduan';
    protected static ?string $recordTitleAttribute = 'ticket_no';
    protected static ?string $navigationIcon     = 'heroicon-o-ticket';
    protected static ?string $navigationGroup    = 'Layanan Pelanggan';
    protected static ?string $navigationLabel    = 'Tiket Pengaduan';
    protected static ?int    $navigationSort     = 8;

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) static::getModel()::where('status', TicketStatus::OPEN->value)->count() ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ── FORM ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Utama')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('ticket_no')
                        ->label('Nomor Tiket')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Otomatis di-generate'),

                    Forms\Components\Select::make('customer_id')
                        ->label('Pelanggan')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('category')
                        ->label('Kategori Gangguan')
                        ->options(TicketCategory::class)
                        ->required(),

                    Forms\Components\Select::make('priority')
                        ->label('Tingkat Prioritas')
                        ->options(TicketPriority::class)
                        ->default(TicketPriority::LOW->value)
                        ->helperText('Di-assign otomatis jika dikosongkan.'),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(TicketStatus::class)
                        ->default(TicketStatus::OPEN->value)
                        ->required(),

                    Forms\Components\Textarea::make('description')
                        ->label('Kronologi Keluhan')
                        ->required()
                        ->columnSpanFull()
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Respon Teknisi')
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('assigned_to')
                        ->label('Ditugaskan Kepada (Teknisi)')
                        ->relationship('assignedTo', 'name', fn (Builder $query) => $query->role('teknisi'))
                        ->searchable()
                        ->preload(),

                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label('Jadwal Kunjungan')
                        ->native(false),

                    Forms\Components\Textarea::make('technician_notes')
                        ->label('Catatan Tindakan Teknisi')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\DateTimePicker::make('resolved_at')
                        ->label('Waktu Selesai')
                        ->native(false),
                ]),
        ]);
    }

    // ── TABLE ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_no')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->description(fn(Ticket $r) => $r->customer?->id_arm . ' — ' . ($r->customer?->whatsapp ?? '-')),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 END $direction")),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dilaporkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Teknisi')
                    ->searchable()
                    ->placeholder('Belum di-assign')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal Kunjungan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Diselesaikan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->options(TicketPriority::class)
                    ->label('Prioritas'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(TicketStatus::class)
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('category')
                    ->options(TicketCategory::class)
                    ->label('Kategori'),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name', fn (Builder $query) => $query->role('teknisi'))
                    ->label('Teknisi'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Ticket $r) => $r->status !== TicketStatus::RESOLVED && $r->status !== TicketStatus::CLOSED)
                    ->form([
                        Forms\Components\Textarea::make('technician_notes')
                            ->label('Catatan Tindakan Teknisi')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Ticket $record, array $data): void {
                        $record->update([
                            'status'           => TicketStatus::RESOLVED,
                            'technician_notes' => $data['technician_notes'],
                            'resolved_at'      => now(),
                        ]);
                    })
                    ->requiresConfirmation(false),

                Tables\Actions\Action::make('kirimWaTeknisi')
                    ->label('WA Teknisi (Gangguan)')
                    ->icon('heroicon-o-signal-slash')
                    ->color('warning')
                    ->url(function ($record) {
                        $customer = $record->customer;
                        $phone = $customer?->whatsapp ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        } elseif (!str_starts_with($phone, '62') && !empty($phone)) {
                            $phone = '62' . $phone;
                        }

                        $name = $customer?->name ?? 'Bapak/Ibu';
                        $idPelanggan = $customer?->id_arm ?? '-';
                        $ticketNo = $record->ticket_no ?? '-';
                        $teknisi = auth()->user()->name ?? 'Teknisi ARMEDIA';
                        
                        $text = "Selamat siang, nama saya *{$teknisi}*, teknisi ARMEDIA yang menangani gangguan.\n\n"
                              . "TIKET : {$ticketNo}\n"
                              . "ID PEL: {$idPelanggan}\n\n"
                              . "STATUS JARINGAN PELANGGAN :: hasil daya ukur jaringan *loss/offline*. Normalnya adalah -15 dbm s/d -22 dbm.\n\n"
                              . "Jika tidak segera diperbaiki maka layanan internet akan terganggu / mengalami lambat, putus-putus atau bahkan tidak ada koneksi internet.\n"
                              . "Terkait dengan hal tersebut, mohon dibantu untuk *alamat lengkap serta patokan alamatnya* agar kami dapat memperbaiki layanan internet {$name}.\n\n"
                              . "Harap menghubungi tim teknisi kami jika terjadi kendala lebih lanjut agar segera di-follow up perbaikannya.\n\n"
                              . "Izin konfirmasi untuk jaringan internetnya. Ini dari ARMEDIA untuk referensi, terukur sistem terbaca mati/offline di cek sistem. Mohon ditunggu ya jika internetnya kendala loss merah karena kendala dari sisi kabelnya 🙏🏻";
                              
                        return "https://wa.me/{$phone}?text=" . urlencode($text);
                    })
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('kirimWaSelesai')
                    ->label('WA (Telah Selesai)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->url(function ($record) {
                        $customer = $record->customer;
                        $phone = $customer?->whatsapp ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        } elseif (!str_starts_with($phone, '62') && !empty($phone)) {
                            $phone = '62' . $phone;
                        }

                        $name = $customer?->name ?? 'Bapak/Ibu';
                        $ticketNo = $record->ticket_no ?? '-';
                        
                        $text = "Halo {$name},\n\nKami informasikan bahwa Tiket Laporan Gangguan Anda (*{$ticketNo}*) telah selesai ditangani oleh tim teknisi kami.\n\nLayanan internet seharusnya sudah kembali normal. Silakan di-restart (cabut-pasang kabel power) modem di rumah Anda jika masih ada kendala, dan jangan ragu menghubungi kami kembali.\n\nTerima kasih atas kesabaran Anda! 🚀";
                              
                        return "https://wa.me/{$phone}?text=" . urlencode($text);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->status === \App\Enums\TicketStatus::RESOLVED || $record->status === \App\Enums\TicketStatus::CLOSED),

                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['customer.village'])
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->orderBy('created_at', 'asc');

        if (auth()->user() && auth()->user()->hasRole('teknisi') && !auth()->user()->hasRole(['super_admin', 'admin', 'cs'])) {
            $query->where('assigned_to', auth()->id());
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit'   => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
