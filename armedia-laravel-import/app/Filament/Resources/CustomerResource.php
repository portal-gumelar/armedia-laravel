<?php

namespace App\Filament\Resources;

use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Operasional ISP';
    protected static ?string $navigationLabel = 'Data Pelanggan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')->schema([
                Forms\Components\TextInput::make('id_baru')->label('ID (ARM)')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('id_lama'),
                Forms\Components\TextInput::make('nama')->required(),
                Forms\Components\TextInput::make('nik_ktp')->label('NIK KTP'),
                Forms\Components\TextInput::make('no_hp')->tel(),
            ])->columns(2),

            Forms\Components\Section::make('Lokasi')->schema([
                Forms\Components\TextInput::make('kec'),
                Forms\Components\TextInput::make('desa'),
                Forms\Components\TextInput::make('rw'),
                Forms\Components\TextInput::make('rt'),
                Forms\Components\TextInput::make('kota_kab'),
                Forms\Components\TextInput::make('link_maps')->url(),
            ])->columns(3),

            Forms\Components\Section::make('Layanan & Jaringan')->schema([
                Forms\Components\Select::make('product_id')->relationship('product', 'nama')->label('Paket'),
                Forms\Components\TextInput::make('harga')->numeric()->prefix('Rp'),
                Forms\Components\TextInput::make('ip'),
                Forms\Components\Select::make('odp_id')->relationship('odp', 'kode_odp')->label('ODP'),
                Forms\Components\TextInput::make('sn'),
                Forms\Components\TextInput::make('ssid'),
                Forms\Components\TextInput::make('password_wifi'),
                Forms\Components\TextInput::make('vlan'),
                Forms\Components\Select::make('status')->options([
                    'Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif', 'FREE' => 'FREE',
                ])->required(),
                Forms\Components\DatePicker::make('tgl_aktif'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_baru')->label('ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('desa')->searchable(),
                Tables\Columns\TextColumn::make('product.nama')->label('Paket'),
                Tables\Columns\TextColumn::make('harga')->money('IDR'),
                Tables\Columns\TextColumn::make('odp.kode_odp')->label('ODP'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'success' => 'Aktif', 'danger' => 'Nonaktif', 'gray' => 'FREE',
                ]),
                Tables\Columns\TextColumn::make('tgl_aktif')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif', 'FREE' => 'FREE',
                ]),
                Tables\Filters\SelectFilter::make('product_id')->relationship('product', 'nama')->label('Paket'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
