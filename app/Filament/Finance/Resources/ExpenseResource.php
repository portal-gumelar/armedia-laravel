<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model            = Expense::class;
    protected static ?string $modelLabel       = 'Pengeluaran';
    protected static ?string $pluralModelLabel = 'Daftar Pengeluaran';
    protected static ?string $navigationIcon   = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationGroup  = 'Keuangan';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Pengeluaran')->columns(2)->schema([
                Forms\Components\DatePicker::make('expense_date')
                    ->label('Tanggal')->default(now())->required()->native(false),
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'operasional'   => '⚙️ Operasional',
                        'infrastruktur' => '🏗️ Infrastruktur Jaringan',
                        'sdm'           => '👥 SDM / Karyawan',
                        'pemasaran'     => '📢 Pemasaran',
                        'lainnya'       => '📦 Lainnya',
                    ])->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Keterangan')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah (Rp)')->numeric()->prefix('Rp')->required(),
                Forms\Components\Select::make('payment_method')
                    ->label('Cara Bayar')
                    ->options(['transfer'=>'Transfer Bank','tunai'=>'Tunai','kartu'=>'Kartu Kredit/Debit']),
                Forms\Components\FileUpload::make('receipt_file')
                    ->label('Bukti Pembayaran')
                    ->image()->directory('expenses')->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('expense_no')->label('No.')->copyable()->weight('bold'),
                Tables\Columns\TextColumn::make('expense_date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()
                    ->formatStateUsing(fn($s) => match($s) {
                        'operasional'=>'⚙️ Operasional','infrastruktur'=>'🏗️ Infrastruktur',
                        'sdm'=>'👥 SDM','pemasaran'=>'📢 Pemasaran',default=>'📦 Lainnya'
                    }),
                Tables\Columns\TextColumn::make('description')->label('Keterangan')->limit(40),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('payment_method')->label('Cara Bayar')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options([
                    'operasional'=>'Operasional','infrastruktur'=>'Infrastruktur',
                    'sdm'=>'SDM','pemasaran'=>'Pemasaran','lainnya'=>'Lainnya',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
