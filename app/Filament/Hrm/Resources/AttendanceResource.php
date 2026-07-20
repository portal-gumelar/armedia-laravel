<?php

namespace App\Filament\Hrm\Resources;

use App\Filament\Hrm\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model            = Attendance::class;
    protected static ?string $modelLabel       = 'Presensi';
    protected static ?string $pluralModelLabel = 'Daftar Presensi';
    protected static ?string $navigationIcon   = 'heroicon-o-clock';
    protected static ?string $navigationGroup  = 'Kehadiran & Cuti';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')->relationship('employee', 'name')->label('Karyawan')->searchable()->required(),
            Forms\Components\DatePicker::make('attendance_date')->label('Tanggal')->default(now())->native(false)->required(),
            Forms\Components\TimePicker::make('check_in')->label('Jam Masuk')->seconds(false),
            Forms\Components\TimePicker::make('check_out')->label('Jam Keluar')->seconds(false),
            Forms\Components\Select::make('status')->label('Status Kehadiran')
                ->options(['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha', 'libur' => 'Libur', 'cuti' => 'Cuti'])
                ->required()->default('hadir'),
            Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('attendance_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->label('Karyawan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('attendance_date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('check_in')->label('Masuk')->time('H:i')->placeholder('-'),
                Tables\Columns\TextColumn::make('check_out')->label('Keluar')->time('H:i')->placeholder('-'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->colors(['success' => 'hadir', 'warning' => 'terlambat', 'info' => 'izin', 'danger' => 'alpha', 'gray' => 'libur']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha', 'libur' => 'Libur', 'cuti' => 'Cuti']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit'   => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
