<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MikrotikServerResource\Pages;
use App\Models\MikrotikServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Crypt;

class MikrotikServerResource extends Resource
{
    protected static ?string $model = MikrotikServer::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Mikrotik Server';
    protected static ?string $modelLabel = 'Mikrotik Server';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konfigurasi RouterOS API')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Server (Router)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Mikrotik Pusat'),
                        Forms\Components\TextInput::make('host')
                            ->label('Host / IP Address')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('192.168.88.1'),
                        Forms\Components\TextInput::make('port')
                            ->label('API Port')
                            ->numeric()
                            ->default(8728)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateMikrotikServer)
                            ->dehydrated(fn ($state) => filled($state)),
                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('host')
                    ->label('Host / IP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->label('Port'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('test_connection')
                    ->label('Test Koneksi')
                    ->icon('heroicon-o-signal')
                    ->color('success')
                    ->action(function (MikrotikServer $record) {
                        try {
                            $client = new \RouterOS\Client([
                                'host' => $record->host,
                                'user' => $record->username,
                                'pass' => $record->password, // Already decrypted via model getter
                                'port' => (int) $record->port,
                            ]);
                            
                            $query = new \RouterOS\Query('/system/resource/print');
                            $response = $client->query($query)->read();

                            if (!empty($response)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Koneksi Berhasil!')
                                    ->body("Router: " . ($response[0]['board-name'] ?? 'Unknown'))
                                    ->success()
                                    ->send();
                            } else {
                                throw new \Exception("Empty response from router.");
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Koneksi Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListMikrotikServers::route('/'),
            'create' => Pages\CreateMikrotikServer::route('/create'),
            'edit' => Pages\EditMikrotikServer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
