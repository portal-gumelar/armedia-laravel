<?php

namespace App\Filament\Resources\Radius;

use App\Models\Radius\RadCheck;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\Radius\RadCheckResource\Pages;

class RadCheckResource extends Resource
{
    protected static ?string $model = RadCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'Jaringan & Infrastruktur';
    protected static ?string $modelLabel = 'Radius User (PPPoE)';
    protected static ?string $pluralModelLabel = 'Radius Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')
                    ->label('PPPoE Username')
                    ->required()
                    ->maxLength(64)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('attribute')
                    ->label('Attribute')
                    ->default('Cleartext-Password')
                    ->required()
                    ->maxLength(64),
                Forms\Components\TextInput::make('op')
                    ->label('Operator')
                    ->default(':=')
                    ->required()
                    ->maxLength(2),
                Forms\Components\TextInput::make('value')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->maxLength(253),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attribute')
                    ->label('Attribute')
                    ->searchable(),
                Tables\Columns\TextColumn::make('op')
                    ->label('Operator'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Password (Masked)')
                    ->formatStateUsing(fn ($state) => '********'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRadChecks::route('/'),
            'create' => Pages\CreateRadCheck::route('/create'),
            'edit' => Pages\EditRadCheck::route('/{record}/edit'),
        ];
    }
}
