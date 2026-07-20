<?php

namespace App\Filament\Hrm\Resources;

use App\Filament\Hrm\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model            = Employee::class;
    protected static ?string $modelLabel       = 'Karyawan';
    protected static ?string $pluralModelLabel = 'Daftar Karyawan';
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationGroup  = 'SDM & Karyawan';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Dasar')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nama Lengkap')->required(),
                Forms\Components\TextInput::make('nik')->label('NIK (KTP)'),
                Forms\Components\TextInput::make('phone')->label('Nomor Telepon'),
                Forms\Components\TextInput::make('email')->label('Email')->email(),
                Forms\Components\DatePicker::make('birth_date')->label('Tanggal Lahir')->native(false),
                Forms\Components\Textarea::make('address')->label('Alamat Lengkap')->columnSpanFull(),
                Forms\Components\FileUpload::make('photo')->label('Foto Profil')->image()->directory('employees'),
            ]),
            Forms\Components\Section::make('Informasi Pekerjaan')->columns(2)->schema([
                Forms\Components\TextInput::make('position')->label('Jabatan')->required(),
                Forms\Components\TextInput::make('division')->label('Divisi / Departemen'),
                Forms\Components\Select::make('employment_type')->label('Status Pekerjaan')
                    ->options(['tetap' => 'Tetap', 'kontrak' => 'Kontrak', 'magang' => 'Magang'])->required()->default('tetap'),
                Forms\Components\Select::make('status')->label('Status Karyawan')
                    ->options(['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Non-Aktif', 'resign' => 'Resign'])->required()->default('aktif'),
                Forms\Components\DatePicker::make('join_date')->label('Tanggal Bergabung')->native(false)->required(),
                Forms\Components\DatePicker::make('end_date')->label('Akhir Kontrak (Jika ada)')->native(false),
            ]),
            Forms\Components\Section::make('Keuangan & BPJS')->columns(2)->schema([
                Forms\Components\TextInput::make('basic_salary')->label('Gaji Pokok')->numeric()->prefix('Rp')->default(0),
                Forms\Components\TextInput::make('bank_name')->label('Nama Bank'),
                Forms\Components\TextInput::make('bank_account_no')->label('Nomor Rekening'),
                Forms\Components\TextInput::make('bpjs_kes_no')->label('No BPJS Kesehatan'),
                Forms\Components\TextInput::make('bpjs_tk_no')->label('No BPJS Ketenagakerjaan'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->label('Foto')->circular(),
                Tables\Columns\TextColumn::make('employee_no')->label('ID Karyawan')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('position')->label('Jabatan')->searchable(),
                Tables\Columns\TextColumn::make('division')->label('Divisi')->searchable(),
                Tables\Columns\TextColumn::make('employment_type')->label('Tipe')->badge(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->colors(['success' => 'aktif', 'warning' => 'cuti', 'danger' => 'nonaktif', 'gray' => 'resign']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Non-Aktif', 'resign' => 'Resign']),
                Tables\Filters\SelectFilter::make('employment_type')->options(['tetap' => 'Tetap', 'kontrak' => 'Kontrak', 'magang' => 'Magang']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit'   => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
