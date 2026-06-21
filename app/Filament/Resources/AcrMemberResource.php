<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcrMemberResource\Pages;
use App\Filament\Resources\AcrMemberResource\RelationManagers;
use App\Models\AcrMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcrMemberResource extends Resource
{
    protected static ?string $model = AcrMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Member & Reward';
    protected static ?string $navigationLabel = 'Data Member ACR';
    protected static ?string $pluralModelLabel = 'Data Member ACR';
    protected static ?string $modelLabel = 'Data Member ACR';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_pelanggan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_poin')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('level_member')
                    ->required()
                    ->maxLength(255)
                    ->default('Reguler'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_poin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level_member')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
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
            'index' => Pages\ListAcrMembers::route('/'),
            'create' => Pages\CreateAcrMember::route('/create'),
            'edit' => Pages\EditAcrMember::route('/{record}/edit'),
        ];
    }
}
