<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OdpResource\Pages;
use App\Forms\Components\MapPicker;
use App\Models\Odp;
use App\Services\OdpCapacityService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OdpResource extends Resource
{
    protected static ?string $model = Odp::class;

    protected static ?string $navigationIcon  = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'ODP';
    protected static ?string $pluralModelLabel = 'ODP';
    protected static ?string $modelLabel       = 'ODP';
    protected static ?string $recordTitleAttribute = 'code';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identifikasi ODP')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Kode ODP')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('ODP-JB001'),
                    Forms\Components\TextInput::make('max_capacity')
                        ->label('Kapasitas Maks Port')
                        ->numeric()
                        ->default(8),
                    Forms\Components\Select::make('village_id')
                        ->label('Desa')
                        ->relationship('village', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'aktif'    => 'Aktif',
                            'nonaktif' => 'Non-Aktif',
                        ])
                        ->default('aktif'),
                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat / Deskripsi Lokasi')
                        ->placeholder('Contoh: Depan SDN Gumelar 01, RT 02/RW 03, Desa Gumelar')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan Teknis')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('📍 Lokasi di Peta')
                ->description('Klik pada peta untuk menentukan koordinat ODP. Bisa juga pakai tombol GPS jika sedang di lapangan.')
                ->schema([
                    MapPicker::make('koordinat')
                        ->label('Tentukan Lokasi')
                        ->defaultCenter(-7.5083, 108.7871, 13)
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->latitude && $record->longitude) {
                                $component->state([
                                    'lat' => (string) $record->latitude,
                                    'lng' => (string) $record->longitude,
                                ]);
                            }
                        })
                        ->columnSpanFull(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->placeholder('-7.5083000')
                            ->helperText('Diisi otomatis saat klik peta, atau isi manual.'),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->placeholder('108.7871000')
                            ->helperText('Diisi otomatis saat klik peta, atau isi manual.'),
                    ]),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode ODP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_capacity')
                    ->label('Maks Port')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customers_count')
                    ->label('Dipakai')
                    ->counts('customers')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'gray'    => 'nonaktif',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('village_id')
                    ->label('Desa')
                    ->relationship('village', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['village'])
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
            'index'  => Pages\ListOdps::route('/'),
            'create' => Pages\CreateOdp::route('/create'),
            'edit'   => Pages\EditOdp::route('/{record}/edit'),
        ];
    }
}
