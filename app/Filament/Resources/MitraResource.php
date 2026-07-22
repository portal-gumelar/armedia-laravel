<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MitraResource\Pages;
use App\Models\Mitra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MitraResource extends Resource
{
    protected static ?string $model = Mitra::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Data Mitra / Reseller';
    protected static ?string $pluralModelLabel = 'Data Mitra';
    protected static ?string $modelLabel = 'Mitra';
    protected static ?string $navigationGroup = 'Mitra & Ekosistem';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Mitra')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('kode_mitra')
                            ->label('Kode Mitra')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('ARM-MTR-001'),
                        Forms\Components\TextInput::make('nama_mitra')
                            ->label('Nama Mitra / Perusahaan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pemilik')
                            ->label('Nama Pemilik / PIC')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('wilayah')
                            ->label('Wilayah Cakupan')
                            ->maxLength(255)
                            ->placeholder('Kota/Kabupaten'),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Kantor')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pengaturan Bisnis')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('persentase_komisi')
                            ->label('Komisi (%)')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif'    => 'Aktif',
                                'nonaktif' => 'Non-Aktif',
                            ])
                            ->default('aktif')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Staf / Akses Login')
                    ->description('Pilih akun user yang berhak masuk ke Dasbor Mitra ini.')
                    ->schema([
                        Forms\Components\Select::make('users')
                            ->label('Staf / User')
                            ->relationship('users', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_mitra')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_mitra')
                    ->label('Nama Mitra')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemilik')
                    ->label('Pemilik / PIC'),
                Tables\Columns\TextColumn::make('wilayah')
                    ->label('Wilayah'),
                Tables\Columns\TextColumn::make('customers_count')
                    ->label('Pelanggan')
                    ->counts('customers')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('persentase_komisi')
                    ->label('Komisi')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMitras::route('/'),
            'create' => Pages\CreateMitra::route('/create'),
            'edit'   => Pages\EditMitra::route('/{record}/edit'),
        ];
    }
}
