<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttenuationLogResource\Pages;
use App\Filament\Resources\AttenuationLogResource\RelationManagers;
use App\Models\AttenuationLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttenuationLogResource extends Resource
{
    protected static ?string $model = AttenuationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Jaringan & Infrastruktur';
    protected static ?string $navigationLabel = 'Log Redaman ODP';
    protected static ?string $modelLabel = 'Log Redaman';
    protected static ?string $pluralModelLabel = 'Log Redaman ODP';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_ukur')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('dbm_olt')
                    ->label('Redaman di OLT (dBm)')
                    ->numeric()
                    ->step('0.01'),
                Forms\Components\TextInput::make('dbm_ont')
                    ->label('Redaman di ONT (Pelanggan) (dBm)')
                    ->numeric()
                    ->step('0.01'),
                Forms\Components\Select::make('teknisi_id')
                    ->relationship('teknisi', 'name')
                    ->searchable(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_ukur')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dbm_olt')
                    ->label('OLT (dBm)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dbm_ont')
                    ->label('ONT (dBm)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teknisi.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttenuationLogs::route('/'),
            'create' => Pages\CreateAttenuationLog::route('/create'),
            'edit' => Pages\EditAttenuationLog::route('/{record}/edit'),
        ];
    }
}
