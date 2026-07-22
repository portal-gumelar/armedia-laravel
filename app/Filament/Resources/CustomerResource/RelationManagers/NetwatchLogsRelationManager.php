<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class NetwatchLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'netwatchLogs';
    protected static ?string $title       = 'Log Monitoring';

    public function form(Form $form): Form
    {
        return $form->schema([]); // readonly — tidak perlu create/edit
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('checked_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->label('Status')
                    ->color([
                        'success' => 'up',
                        'danger'  => 'down',
                    ]),
                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Waktu Cek')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([])  // readonly
            ->actions([])
            ->bulkActions([]);
    }
}
