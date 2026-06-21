<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Registration;

class LatestRegistrationsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Pendaftar Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Registration::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No. Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(50),
                Tables\Columns\TextColumn::make('internetPackage.name')
                    ->label('Paket Dipilih')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Mendaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
