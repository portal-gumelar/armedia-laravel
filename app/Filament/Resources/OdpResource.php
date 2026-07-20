<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OdpResource\Pages;
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
            Forms\Components\TextInput::make('code')
                ->label('Kode ODP')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('1/1/3'),
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
            Forms\Components\Textarea::make('notes')
                ->label('Catatan')
                ->columnSpanFull(),
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
