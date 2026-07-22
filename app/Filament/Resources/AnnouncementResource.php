<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Portal & Informasi';
    protected static ?string $navigationLabel = 'Pengumuman';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengumuman')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Pengumuman')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'info' => 'Informasi',
                                'warning' => 'Peringatan / Maintenance',
                                'promo' => 'Promo',
                            ])
                            ->required()
                            ->default('info'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Berlaku Sampai')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Pengumuman')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'promo' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => 'Informasi',
                        'warning' => 'Peringatan',
                        'promo' => 'Promo',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Berlaku Sampai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
