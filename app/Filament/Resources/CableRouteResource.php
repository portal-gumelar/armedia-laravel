<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CableRouteResource\Pages;
use App\Filament\Resources\CableRouteResource\RelationManagers;
use App\Models\CableRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CableRouteResource extends Resource
{
    protected static ?string $model = CableRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';
    protected static ?string $navigationGroup = 'Jaringan & Infrastruktur';
    protected static ?string $navigationLabel = 'Jalur Kabel';
    protected static ?string $modelLabel = 'Jalur Kabel';
    protected static ?string $pluralModelLabel = 'Jalur Kabel';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'core' => 'Core',
                        'distribution' => 'Distribution',
                        'drop' => 'Drop Cable',
                    ])
                    ->required()
                    ->default('distribution'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'cut' => 'Cut / Putus',
                        'maintenance' => 'Maintenance',
                    ])
                    ->required()
                    ->default('active'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('polyline')
                    ->label('Polyline JSON (Lat/Lng array)')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'core' => 'danger',
                        'distribution' => 'warning',
                        'drop' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'cut' => 'danger',
                        'maintenance' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'core' => 'Core',
                        'distribution' => 'Distribution',
                        'drop' => 'Drop Cable',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'cut' => 'Cut / Putus',
                        'maintenance' => 'Maintenance',
                    ]),
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
            'index' => Pages\ListCableRoutes::route('/'),
            'create' => Pages\CreateCableRoute::route('/create'),
            'edit' => Pages\EditCableRoute::route('/{record}/edit'),
        ];
    }
}
