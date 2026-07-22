<?php

namespace App\Filament\Resources;

use App\Enums\PipelineStatus;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Filament\Resources\RegistrationResource\RelationManagers;
use App\Models\Customer;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Layanan & Pelanggan'; // dipindah dari Layanan Pelanggan
    protected static ?string $navigationLabel = 'Calon Pelanggan (PSB)';
    protected static ?string $pluralModelLabel = 'Calon Pelanggan';
    protected static ?string $modelLabel = 'Calon Pelanggan';
    protected static ?int    $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Data Pendaftar (existing fields) ────────────────────
                Forms\Components\Section::make('Data Pendaftar')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('paket')
                            ->required()
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
                        Forms\Components\TextInput::make('rw')->maxLength(10),
                        Forms\Components\TextInput::make('rt')->maxLength(10),
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
                        Forms\Components\Textarea::make('catatan')->columnSpanFull(),
                    ]),

                // ── Data PSB (field baru) ────────────────────────────────
                Forms\Components\Section::make('Data PSB')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('report_no')
                            ->label('No. Laporan PSB')
                            ->placeholder('PSB15226062701'),
                        Forms\Components\DatePicker::make('jadwal_pasang')
                            ->label('Jadwal Pasang'),
                        Forms\Components\TextInput::make('marketing')
                            ->label('Nama Marketing'),
                        Forms\Components\Select::make('target_odp_id')
                            ->label('Target ODP')
                            ->relationship('targetOdp', 'code')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('pipeline_status')
                            ->label('Status Pipeline')
                            ->options(PipelineStatus::class)
                            ->default(PipelineStatus::BELUM->value),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // FIX bug lama: ganti internetPackage.name → paket, no_telp → whatsapp
                Tables\Columns\TextColumn::make('paket')
                    ->label('Paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('whatsapp') // fix: was 'no_telp'
                    ->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desa')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'menunggu_survei' => 'Menunggu Survei',
                        'instalasi' => 'Proses Instalasi',
                        'aktif' => 'Aktif',
                        'batal' => 'Batal',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('pipeline_status')->badge()
                    ->label('Pipeline')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jadwal_pasang')
                    ->label('Jadwal Pasang')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Filters\SelectFilter::make('pipeline_status')
                    ->label('Pipeline')
                    ->options(PipelineStatus::class),
            ])
            ->actions([
                Tables\Actions\Action::make('konversi')
                    ->label('Konversi ke Pelanggan')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konversi ke Pelanggan Aktif')
                    ->modalDescription('Data dari registrasi ini akan disalin ke tabel Pelanggan. Pastikan data sudah lengkap.')
                    ->visible(fn ($record) => $record->converted_customer_id === null)
                    ->action(function ($record) {
                        $customer = Customer::create([
                            'name'      => $record->nama,
                            'whatsapp'  => $record->whatsapp,
                            'nik'       => $record->nik,
                            'alamat'    => $record->alamat,
                            'kecamatan' => $record->kecamatan,
                            'rw'        => $record->rw,
                            'rt'        => $record->rt,
                            'subscription_status' => 'aktif',
                        ]);

                        $record->update([
                            'converted_customer_id' => $customer->id,
                            'pipeline_status'       => PipelineStatus::TERPASANG->value,
                        ]);

                        // Give referral points if applicable
                        if ($record->referral_id_arm && !$record->points_rewarded) {
                            $referrerCustomer = Customer::where('id_arm', $record->referral_id_arm)->first();
                            if ($referrerCustomer) {
                                $referrerMember = \App\Models\AcrMember::firstOrCreate(
                                    ['customer_id' => $referrerCustomer->id],
                                    [
                                        'id_pelanggan' => $referrerCustomer->id_arm,
                                        'nama' => $referrerCustomer->name,
                                        'whatsapp' => $referrerCustomer->whatsapp,
                                    ]
                                );

                                \App\Models\AcrPointTransaction::create([
                                    'id_member' => $referrerMember->id,
                                    'jenis' => 'MASUK',
                                    'jumlah_poin' => 50000,
                                    'keterangan' => 'Bonus Referral: Pendaftaran ' . $customer->name,
                                ]);
                                
                                $record->update(['points_rewarded' => true]);
                            }
                        }

                        Notification::make()
                            ->title('Berhasil dikonversi')
                            ->body("Pelanggan {$customer->name} telah dibuat.")
                            ->success()
                            ->send();
                    }),
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
