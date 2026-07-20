<?php

namespace App\Filament\Hrm\Resources;

use App\Filament\Hrm\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    protected static ?string $model            = Leave::class;
    protected static ?string $modelLabel       = 'Cuti & Izin';
    protected static ?string $pluralModelLabel = 'Daftar Cuti & Izin';
    protected static ?string $navigationIcon   = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup  = 'Kehadiran & Cuti';
    protected static ?int    $navigationSort   = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')->relationship('employee', 'name')->label('Karyawan')->searchable()->required(),
            Forms\Components\Select::make('type')->label('Jenis Pengajuan')
                ->options(['tahunan' => 'Cuti Tahunan', 'sakit' => 'Sakit', 'melahirkan' => 'Melahirkan', 'penting' => 'Keperluan Penting', 'izin' => 'Izin Lainnya'])
                ->required(),
            Forms\Components\DatePicker::make('start_date')->label('Mulai Tanggal')->native(false)->required(),
            Forms\Components\DatePicker::make('end_date')->label('Sampai Tanggal')->native(false)->required(),
            Forms\Components\TextInput::make('days_count')->label('Jumlah Hari')->numeric()->default(1)->required(),
            Forms\Components\Textarea::make('reason')->label('Alasan')->required()->columnSpanFull(),
            Forms\Components\Select::make('status')->label('Status')
                ->options(['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'])
                ->default('pending')->required(),
            Forms\Components\Textarea::make('rejection_reason')->label('Alasan Penolakan')->columnSpanFull()->visible(fn(Forms\Get $get) => $get('status') === 'rejected'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->label('Karyawan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Jenis')->badge()
                    ->colors(['primary' => 'tahunan', 'warning' => 'sakit', 'info' => 'melahirkan', 'success' => 'penting', 'gray' => 'izin']),
                Tables\Columns\TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                Tables\Columns\TextColumn::make('end_date')->label('Sampai')->date('d M Y'),
                Tables\Columns\TextColumn::make('days_count')->label('Hari')->numeric(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']),
                Tables\Filters\SelectFilter::make('type')->options(['tahunan' => 'Cuti Tahunan', 'sakit' => 'Sakit', 'melahirkan' => 'Melahirkan', 'penting' => 'Keperluan Penting', 'izin' => 'Izin Lainnya']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(Leave $r) => $r->status === 'pending')
                    ->action(fn(Leave $r) => $r->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()])),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(Leave $r) => $r->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')->label('Alasan Penolakan')->required(),
                    ])
                    ->action(fn(Leave $r, array $data) => $r->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason']])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit'   => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
