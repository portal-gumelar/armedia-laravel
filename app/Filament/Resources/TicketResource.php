<?php

namespace App\Filament\Resources;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
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
    protected static ?string $modelLabel         = 'Tiket Gangguan';
    protected static ?string $pluralModelLabel   = 'Tiket Gangguan';
    protected static ?string $recordTitleAttribute = 'ticket_no';
    protected static ?string $navigationIcon     = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup    = 'Operasional ISP';
    protected static ?int    $navigationSort     = 6;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', TicketStatus::OPEN->value)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ── FORM ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Tiket')
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

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(TicketStatus::class)
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
            ->defaultSort('created_at', 'desc')
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

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Diselesaikan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TicketStatus::class)
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('category')
                    ->options(TicketCategory::class)
                    ->label('Kategori'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                // Aksi cepat: Selesaikan Tiket
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
        return parent::getEloquentQuery()
            ->with(['customer.village'])
            ->withoutGlobalScopes([SoftDeletingScope::class]);
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
