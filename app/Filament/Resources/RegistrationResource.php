<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Filament\Resources\RegistrationResource\RelationManagers;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Layanan Pelanggan';
    protected static ?string $navigationLabel = 'Registrasi Pemasangan';
    protected static ?string $pluralModelLabel = 'Registrasi Pemasangan';
    protected static ?string $modelLabel = 'Registrasi Pemasangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('paket')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('langganan_sebelumnya')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kecamatan')
                    ->required()
                    ->maxLength(255)
                    ->default('GUMELAR'),
                Forms\Components\TextInput::make('desa')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tanggal_pemasangan')
                    ->required()
                    ->maxLength(255)
                    ->default('Secepatnya'),
                Forms\Components\TextInput::make('waktu_survei')
                    ->required()
                    ->maxLength(255)
                    ->default('Pagi (08:00 - 11:00)'),
                Forms\Components\Select::make('status')
                    ->options([
                        'menunggu_survei' => 'Menunggu Survei',
                        'instalasi' => 'Proses Instalasi',
                        'aktif' => 'Aktif',
                        'batal' => 'Batal',
                    ])
                    ->default('menunggu_survei')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('internetPackage.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'menunggu_survei' => 'Menunggu Survei',
                        'instalasi' => 'Proses Instalasi',
                        'aktif' => 'Aktif',
                        'batal' => 'Batal',
                    ])
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu_survei' => 'Menunggu Survei',
                        'instalasi' => 'Proses Instalasi',
                        'aktif' => 'Aktif',
                        'batal' => 'Batal',
                    ]),
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
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}
