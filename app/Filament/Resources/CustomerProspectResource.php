<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerProspectResource\Pages;
use App\Filament\Resources\CustomerProspectResource\RelationManagers;
use App\Models\CustomerProspect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerProspectResource extends Resource
{
    protected static ?string $model = CustomerProspect::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Prospek Pelanggan';
    protected static ?string $pluralModelLabel = 'Prospek Pelanggan';
    protected static ?string $navigationLabel = 'Prospek Pelanggan';
    protected static ?string $navigationGroup = 'Layanan & Pelanggan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('no_laporan')
                            ->label('Nomor PSB')
                            ->default(fn () => 'PSB-' . date('Ymd') . '-' . rand(100, 999)),
                        Forms\Components\TextInput::make('nama_pelanggan')
                            ->label('Nama Lengkap')
                            ->required(),
                        Forms\Components\TextInput::make('nomor_telepon')
                            ->label('No. WhatsApp / HP')
                            ->tel()
                            ->required(),
                        Forms\Components\Textarea::make('alamat_pemasangan')
                            ->label('Alamat Pemasangan')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('link_map')
                            ->label('Link Google Maps')
                            ->url()
                            ->prefixIcon('heroicon-m-map-pin')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Layanan & Berkas')
                    ->schema([
                        Forms\Components\Select::make('jenis_layanan')
                            ->label('Jenis Layanan')
                            ->options([
                                'Broadband 10 Mbps' => 'Broadband 10 Mbps',
                                'Broadband 20 Mbps' => 'Broadband 20 Mbps',
                                'Broadband 30 Mbps' => 'Broadband 30 Mbps',
                                'Broadband 50 Mbps' => 'Broadband 50 Mbps',
                                'Dedicated Internet' => 'Dedicated Internet',
                            ])
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->label('Status Prospek')
                            ->options([
                                'Menunggu Survey' => 'Menunggu Survey',
                                'Proses Instalasi' => 'Proses Instalasi',
                                'Selesai / Aktif' => 'Selesai / Aktif',
                                'Dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('Menunggu Survey')
                            ->required(),
                        Forms\Components\DatePicker::make('jadwal_pasang')
                            ->label('Rencana Jadwal Pasang'),
                        Forms\Components\TextInput::make('marketing')
                            ->label('Nama Sales/Marketing'),
                        Forms\Components\FileUpload::make('foto_ktp')
                            ->label('Scan/Foto KTP')
                            ->image()
                            ->directory('ktp-prospek')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_laporan')
                    ->label('No. PSB')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->nomor_telepon),
                Tables\Columns\TextColumn::make('alamat_pemasangan')
                    ->label('Lokasi')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('jenis_layanan')
                    ->label('Layanan')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Survey' => 'warning',
                        'Proses Instalasi' => 'primary',
                        'Selesai / Aktif' => 'success',
                        'Dibatalkan' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jadwal_pasang')
                    ->label('Jadwal Pasang')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('foto_ktp')
                    ->label('KTP')
                    ->circular(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Menunggu Survey' => 'Menunggu Survey',
                        'Proses Instalasi' => 'Proses Instalasi',
                        'Selesai / Aktif' => 'Selesai / Aktif',
                        'Dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('chat_wa')
                    ->label('Follow Up (WA)')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('success')
                    ->url(function ($record) {
                        $phone = $record->nomor_telepon ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $text = "Halo Bapak/Ibu *{$record->nama_pelanggan}*,\nIni dengan tim ARMEDIA. Terkait pendaftaran/prospek layanan *{$record->jenis_layanan}* di alamat Bapak/Ibu, apakah ada waktu untuk kami jadwalkan survei lokasi?";
                        return "https://wa.me/{$phone}?text=" . urlencode($text);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->nomor_telepon)),
                    
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
            'index' => Pages\ListCustomerProspects::route('/'),
            'create' => Pages\CreateCustomerProspect::route('/create'),
            'edit' => Pages\EditCustomerProspect::route('/{record}/edit'),
        ];
    }
}
