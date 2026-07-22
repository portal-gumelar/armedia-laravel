<?php

namespace App\Filament\Mitra\Resources;

use App\Filament\Mitra\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Odp;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $pluralModelLabel = 'Pelanggan';
    protected static ?string $modelLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Pelanggan & Tagihan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')->required(),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('No. WhatsApp')->required(),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK KTP'),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address'),
                        Forms\Components\Select::make('internet_package_id')
                            ->label('Paket Internet')
                            ->options(InternetPackage::pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('odp_id')
                            ->label('ODP')
                            ->options(
                                Odp::where('mitra_id', Filament::getTenant()?->id)
                                    ->pluck('code', 'id')
                            )
                            ->searchable(),
                    ]),
                Forms\Components\Section::make('Alamat')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')->columnSpanFull(),
                        Forms\Components\TextInput::make('rt')->label('RT'),
                        Forms\Components\TextInput::make('rw')->label('RW'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_arm')
                    ->label('ID ARM')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp'),
                Tables\Columns\TextColumn::make('internetPackage.name')
                    ->label('Paket'),
                Tables\Columns\TextColumn::make('subscription_status')->badge()
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    // Scoping otomatis: tampilkan hanya pelanggan milik Mitra ini
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('mitra_id', Filament::getTenant()?->id);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
