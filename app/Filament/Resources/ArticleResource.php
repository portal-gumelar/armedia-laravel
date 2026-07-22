<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pengaturan & Sistem';
    protected static ?string $navigationLabel = 'Artikel & Berita';
    protected static ?string $pluralModelLabel = 'Artikel & Berita';
    protected static ?string $modelLabel = 'Artikel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── KOLOM KIRI ──
                Forms\Components\Section::make('Informasi Artikel')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Artikel')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->required()
                            ->options([
                                'TIPS & TRIK'   => 'TIPS & TRIK',
                                'TEKNOLOGI'     => 'TEKNOLOGI',
                                'PROMO'         => 'PROMO',
                                'INFORMASI'     => 'INFORMASI',
                                'PENGUMUMAN'    => 'PENGUMUMAN',
                            ])
                            ->searchable(),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan / Deskripsi Singkat')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Konten Artikel (Lengkap)')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3',
                                'bulletList', 'orderedList',
                                'blockquote',
                                'link',
                                'undo', 'redo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                // ── KOLOM KANAN ──
                Forms\Components\Section::make('Foto Artikel')
                    ->schema([
                        Forms\Components\TextInput::make('image_url')
                            ->label('URL Foto Utama (Eksternal / CDN)')
                            ->placeholder('https://...')
                            ->helperText('Isi JIKA foto berasal dari link eksternal (ImageKit, Unsplash, dll). Kosongkan jika upload langsung.')
                            ->maxLength(500),

                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Upload Foto Cover / Thumbnail')
                            ->helperText('Upload gambar utama artikel. Format: JPG, PNG, WebP. Maks: 5MB.')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                            ->disk('public')
                            ->directory('articles/covers')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Upload Galeri Foto (Opsional)')
                            ->helperText('Upload beberapa foto tambahan untuk konten artikel. Maks 10 foto, 5MB/foto.')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->disk('public')
                            ->directory('articles/gallery')
                            ->maxSize(5120)
                            ->maxFiles(10)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->circular(false)
                    ->defaultImageUrl(fn ($record) => $record->image_url)
                    ->width(80)
                    ->height(50),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')->badge()
                    ->label('Kategori')
                    ->color(fn (string $state): string => match ($state) {
                        'TIPS & TRIK' => 'warning',
                        'TEKNOLOGI'   => 'success',
                        'PROMO'       => 'danger',
                        'INFORMASI'   => 'primary',
                        'PENGUMUMAN'  => 'secondary',
                        default       => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('excerpt')
                    ->label('Ringkasan')
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
