<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebSettingResource\Pages;
use App\Filament\Resources\WebSettingResource\RelationManagers;
use App\Models\WebSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebSettingResource extends Resource
{
    protected static ?string $model = WebSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Pengaturan Web';
    protected static ?string $pluralModelLabel = 'Pengaturan Web';
    protected static ?string $modelLabel = 'Pengaturan Web';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konfigurasi Web')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Kunci (Key)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Data')
                            ->options([
                                'text' => 'Teks Pendek',
                                'textarea' => 'Teks Panjang',
                                'image' => 'Gambar / URL',
                                'json' => 'Format JSON',
                            ])
                            ->required()
                            ->default('text'),
                        Forms\Components\Textarea::make('value')
                            ->label('Nilai (Value)')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Kunci Pengaturan')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'textarea' => 'info',
                        'image' => 'success',
                        'json' => 'warning',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Isi Nilai')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListWebSettings::route('/'),
            'create' => Pages\CreateWebSetting::route('/create'),
            'edit' => Pages\EditWebSetting::route('/{record}/edit'),
        ];
    }
}
