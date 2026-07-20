<?php

namespace App\Filament\Resources;

use App\Models\Odp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OdpResource extends Resource
{
    protected static ?string $model = Odp::class;
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'Jaringan & Monitoring';
    protected static ?string $navigationLabel = 'Master ODP';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('kode_odp')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('port_terpakai')->numeric()->required(),
            Forms\Components\TextInput::make('kapasitas_maks')->numeric(),
            Forms\Components\TextInput::make('sisa_slot')->numeric(),
            Forms\Components\Select::make('status')->options([
                'Tersedia' => 'Tersedia', 'Penuh' => 'Penuh',
            ]),
            Forms\Components\TextInput::make('desa_lokasi'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('kode_odp')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('port_terpakai'),
            Tables\Columns\TextColumn::make('kapasitas_maks'),
            Tables\Columns\TextColumn::make('sisa_slot'),
            Tables\Columns\BadgeColumn::make('status')->colors(['success' => 'Tersedia', 'danger' => 'Penuh']),
            Tables\Columns\TextColumn::make('desa_lokasi'),
            Tables\Columns\TextColumn::make('customers_count')->counts('customers')->label('Jml Pelanggan'),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOdps::route('/'),
            'create' => Pages\CreateOdp::route('/create'),
            'edit' => Pages\EditOdp::route('/{record}/edit'),
        ];
    }
}
