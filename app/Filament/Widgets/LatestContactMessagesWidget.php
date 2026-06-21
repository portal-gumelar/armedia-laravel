<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ContactMessage;

class LatestContactMessagesWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Pesan Masuk Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContactMessage::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pengirim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Isi Pesan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
