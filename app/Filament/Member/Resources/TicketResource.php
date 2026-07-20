<?php

namespace App\Filament\Member\Resources;

use App\Filament\Member\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = 'Layanan / Tiket';
    protected static ?string $pluralModelLabel = 'Layanan / Tiket';
    protected static ?string $modelLabel = 'Tiket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'technical' => 'Gangguan Teknis',
                        'billing' => 'Info Tagihan',
                        'upgrade' => 'Upgrade Layanan',
                        'other' => 'Lain-lain',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->label('Subjek')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Keluhan')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('status')
                    ->default('open'),
                Forms\Components\Hidden::make('customer_id')
                    ->default(fn () => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Nomor Tiket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'open',
                        'primary' => 'in_progress',
                        'success' => 'resolved',
                        'danger' => 'closed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    // Hanya tampilkan tiket milik pelanggan yang sedang login
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customer_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
        ];
    }
}
