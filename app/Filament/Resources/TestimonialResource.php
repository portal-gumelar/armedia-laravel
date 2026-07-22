<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Testimoni';
    protected static ?string $pluralModelLabel = 'Testimoni';
    protected static ?string $modelLabel = 'Testimoni';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Testimoni')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->label('Nama Pengulas')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author_role')
                            ->label('Peran / Pekerjaan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('avatar_initials')
                            ->label('Inisial Avatar')
                            ->required()
                            ->maxLength(2)
                            ->default(fn (Forms\Get $get) => substr($get('author_name'), 0, 2)),
                        Forms\Components\Textarea::make('quote')
                            ->label('Isi Testimoni')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('avatar_initials')
                    ->label('Avatar')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('author_role')
                    ->label('Pekerjaan/Peran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quote')
                    ->label('Kutipan')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
