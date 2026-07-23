<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstallationTaskResource\Pages;
use App\Filament\Resources\InstallationTaskResource\RelationManagers;
use App\Models\InstallationTask;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstallationTaskResource extends Resource
{
    protected static ?string $model = InstallationTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Project Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Tugas Instalasi')
                    ->schema([
                        Forms\Components\TextInput::make('task_no')
                            ->label('Nomor Tugas')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(2),
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan Baru (Prospek)')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Tugas')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'survey' => 'Survey',
                                'kabel' => 'Tarik Kabel',
                                'aktivasi' => 'Aktivasi OLT/MikroTik',
                                'selesai' => 'Selesai',
                                'batal' => 'Batal',
                            ])
                            ->default('survey')
                            ->required(),
                        Forms\Components\Select::make('assigned_to')
                            ->label('Ditugaskan Ke (Teknisi)')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Pelaksanaan')
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi / Catatan')
                            ->columnSpan(2)
                            ->rows(4),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task_no')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Pelanggan'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('assignee.name')->label('Teknisi'),
                Tables\Columns\TextColumn::make('scheduled_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('chat_wa')
                    ->label('Info Jadwal (WA)')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('info')
                    ->url(function ($record) {
                        $phone = $record->customer?->whatsapp ?? $record->customer?->nomor_telepon ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $jadwal = $record->scheduled_at ? \Carbon\Carbon::parse($record->scheduled_at)->translatedFormat('d M Y, H:i') : 'Segera (akan dihubungi kembali)';
                        $teknisi = $record->assignee?->name ?? 'Tim ARMEDIA';
                        
                        $text = "Halo Bapak/Ibu *{$record->customer?->name}*,\nKami dari tim teknisi ARMEDIA menginformasikan jadwal kunjungan instalasi/survei layanan internet Anda yang direncanakan pada:\n\n*Waktu:* {$jadwal}\n*Teknisi:* {$teknisi}\n\nMohon pastikan ada pihak keluarga di rumah pada waktu tersebut. Terima kasih!";
                        return "https://wa.me/{$phone}?text=" . urlencode($text);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->customer?->whatsapp) || !empty($record->customer?->nomor_telepon)),
                    
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
            'index' => Pages\ListInstallationTasks::route('/'),
            'create' => Pages\CreateInstallationTask::route('/create'),
            'edit' => Pages\EditInstallationTask::route('/{record}/edit'),
        ];
    }
}
