<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketingReferralResource\Pages;
use App\Filament\Resources\MarketingReferralResource\RelationManagers;
use App\Models\MarketingReferral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketingReferralResource extends Resource
{
    protected static ?string $model = MarketingReferral::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?string $navigationGroup = 'Pengaturan & Sistem';
    protected static ?string $navigationLabel = 'Referral Marketing';
    protected static ?string $modelLabel = 'Referral Marketing';
    protected static ?string $pluralModelLabel = 'Referral Marketing';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_marketing')
                    ->required(),
                Forms\Components\TextInput::make('lokasi'),
                Forms\Components\TextInput::make('nama_client')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DatePicker::make('tgl_daftar'),
                Forms\Components\TextInput::make('sumber_data'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_marketing')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_daftar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sumber_data')
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
            'index' => Pages\ListMarketingReferrals::route('/'),
            'create' => Pages\CreateMarketingReferral::route('/create'),
            'edit' => Pages\EditMarketingReferral::route('/{record}/edit'),
        ];
    }
}
