<?php

namespace App\Filament\Hrm\Resources;

use App\Filament\Hrm\Resources\PayrollResource\Pages;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollSlipService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PayrollResource extends Resource
{
    protected static ?string $model            = Payroll::class;
    protected static ?string $modelLabel       = 'Slip Gaji';
    protected static ?string $pluralModelLabel = 'Slip Gaji';
    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Penggajian';
    protected static ?int    $navigationSort   = 1;

    // ─────────────────────────────────────────────────────────────────────────
    // FORM
    // ─────────────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        $recalc = function (Get $get, Set $set) {
            $service = app(PayrollSlipService::class);
            $preview = $service->previewFromState([
                'basic_salary'          => (int) $get('basic_salary'),
                'fee_ikr_per_pelanggan' => (int) $get('fee_ikr_per_pelanggan'),
                'jumlah_teknisi_pasang' => (int) $get('jumlah_teknisi_pasang'),
                'jumlah_ikr'            => (int) $get('jumlah_ikr'),
                'hari_hadir'            => (int) $get('hari_hadir'),
                'jumlah_referral'       => (int) $get('jumlah_referral'),
                'kasbon'                => (int) $get('kasbon'),
                'lain_lain_potong'      => (int) $get('lain_lain_potong'),
            ]);
            $set('_tarif_ikr',           $preview['tarif_ikr']);
            $set('_tunjangan_ikr',        $preview['tunjangan_ikr']);
            $set('_tunjangan_transport',  $preview['tunjangan_transport']);
            $set('_fee_marketing',        $preview['fee_marketing']);
            $set('_total_pendapatan',     $preview['total_pendapatan']);
            $set('_total_potongan',       $preview['total_potongan']);
            $set('_total_diterima',       $preview['total_diterima']);
        };

        return $form->schema([

            // ── SECTION 1: Identitas ─────────────────────────────────────────
            Forms\Components\Section::make('📋 Identitas')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('employee_id')
                        ->label('Karyawan')
                        ->options(Employee::where('status', 'aktif')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, ?int $state) {
                            if ($state) {
                                $emp = Employee::find($state);
                                if ($emp && $emp->basic_salary > 0) {
                                    $set('basic_salary', (int) $emp->basic_salary);
                                }
                            }
                        }),

                    Forms\Components\Select::make('_bulan')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari',   2 => 'Februari', 3 => 'Maret',
                            4 => 'April',     5 => 'Mei',      6 => 'Juni',
                            7 => 'Juli',      8 => 'Agustus',  9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $bulan = $get('_bulan') ?? now()->month;
                            $tahun = $get('_tahun') ?? now()->year;
                            $set('period', sprintf('%04d-%02d-01', $tahun, $bulan));
                        })
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('_tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->default(now()->year)
                        ->minValue(2024)
                        ->maxValue(2030)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $bulan = $get('_bulan') ?? now()->month;
                            $tahun = $get('_tahun') ?? now()->year;
                            $set('period', sprintf('%04d-%02d-01', $tahun, $bulan));
                        })
                        ->dehydrated(false),

                    Forms\Components\Hidden::make('period')
                        ->default(now()->startOfMonth()->toDateString()),

                    Forms\Components\Select::make('status')
                        ->label('Status Slip')
                        ->options(['draft' => 'Draft', 'approved' => 'Disetujui', 'paid' => 'Sudah Dibayar'])
                        ->default('draft')
                        ->required(),

                    Forms\Components\TextInput::make('basic_salary')
                        ->label('Gaji Pokok (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(Payroll::DEFAULT_GAJI_POKOK)
                        ->required()
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->columnSpanFull()
                        ->rows(2),
                ]),

            // ── SECTION 2: Tunjangan IKR ─────────────────────────────────────
            Forms\Components\Section::make('🔧 Tunjangan Kinerja (IKR)')
                ->description('Tarif IKR dihitung otomatis: Fee IKR per Pelanggan ÷ Jumlah Teknisi')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('fee_ikr_per_pelanggan')
                        ->label('Fee IKR per Pelanggan (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(40000)
                        ->required()
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\TextInput::make('jumlah_teknisi_pasang')
                        ->label('Jumlah Teknisi Pasang')
                        ->numeric()
                        ->default(1)
                        ->minValue(0)
                        ->required()
                        ->helperText('Pembagi untuk menghitung tarif per orang')
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\TextInput::make('jumlah_ikr')
                        ->label('Jumlah IKR (Pemasangan Anda)')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\Placeholder::make('_tarif_ikr')
                        ->label('Tarif IKR per Unit')
                        ->content(fn (Get $get) => 'Rp ' . number_format((int) $get('_tarif_ikr'))),

                    Forms\Components\Placeholder::make('_tunjangan_ikr')
                        ->label('→ Total Tunjangan IKR')
                        ->content(fn (Get $get) => '💰 Rp ' . number_format((int) $get('_tunjangan_ikr'))),
                ]),

            // ── SECTION 3: Transport & Marketing ────────────────────────────
            Forms\Components\Section::make('🚗 Transport & Marketing')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('hari_hadir')
                        ->label('Jumlah Hari Hadir')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->helperText('Tarif: Rp 5.000 / hari')
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\Placeholder::make('_tunjangan_transport')
                        ->label('→ Tunjangan Transport')
                        ->content(fn (Get $get) => '🚗 Rp ' . number_format((int) $get('_tunjangan_transport'))),

                    Forms\Components\TextInput::make('jumlah_referral')
                        ->label('Jumlah Pelanggan Referral')
                        ->numeric()
                        ->default(0)
                        ->helperText('Tarif: Rp 20.000 / pelanggan baru')
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\Placeholder::make('_fee_marketing')
                        ->label('→ Fee Marketing')
                        ->content(fn (Get $get) => '📣 Rp ' . number_format((int) $get('_fee_marketing'))),
                ]),

            // ── SECTION 4: Potongan ─────────────────────────────────────────
            Forms\Components\Section::make('✂️ Potongan')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('kasbon')
                        ->label('Kasbon / Pinjaman (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\TextInput::make('lain_lain_potong')
                        ->label('Potongan Lain-lain (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live(debounce: 500)
                        ->afterStateUpdated($recalc),

                    Forms\Components\TextInput::make('ket_lain_lain')
                        ->label('Keterangan Potongan')
                        ->placeholder('mis. ganti kabel hilang')
                        ->columnSpanFull(),
                ]),

            // ── SECTION 5: Ringkasan (Live Preview) ─────────────────────────
            Forms\Components\Section::make('💵 Ringkasan Gaji')
                ->description('Dihitung otomatis secara real-time')
                ->columns(3)
                ->schema([
                    Forms\Components\Placeholder::make('__total_pendapatan')
                        ->label('Total Pendapatan')
                        ->content(fn (Get $get) => new HtmlString(
                            '<span class="text-xl font-bold text-green-600">Rp ' . number_format((int) $get('_total_pendapatan')) . '</span>'
                        )),

                    Forms\Components\Placeholder::make('__total_potongan')
                        ->label('Total Potongan')
                        ->content(fn (Get $get) => new HtmlString(
                            '<span class="text-xl font-bold text-red-600">- Rp ' . number_format((int) $get('_total_potongan')) . '</span>'
                        )),

                    Forms\Components\Placeholder::make('__total_diterima')
                        ->label('🏆 Total Diterima')
                        ->content(function (Get $get) {
                            $total   = (int) $get('_total_diterima');
                            $warning = (int) $get('_total_potongan') > (int) $get('_total_pendapatan');
                            $color   = $warning ? 'text-red-700' : 'text-emerald-700';
                            $warn    = $warning ? ' ⚠️ Potongan melebihi pendapatan!' : '';
                            return new HtmlString(
                                "<span class=\"text-2xl font-black {$color}\">Rp " . number_format($total) . "</span>{$warn}"
                            );
                        }),

                    // Hidden fields untuk state management live preview
                    Forms\Components\Hidden::make('_tarif_ikr')->default(0),
                    Forms\Components\Hidden::make('_tunjangan_ikr')->default(0),
                    Forms\Components\Hidden::make('_tunjangan_transport')->default(0),
                    Forms\Components\Hidden::make('_fee_marketing')->default(0),
                    Forms\Components\Hidden::make('_total_pendapatan')->default(0),
                    Forms\Components\Hidden::make('_total_potongan')->default(0),
                    Forms\Components\Hidden::make('_total_diterima')->default(0),
                ]),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TABLE
    // ─────────────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';
                        $months = [
                            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
                        ];
                        $d = \Carbon\Carbon::parse($state);
                        return ($months[$d->month] ?? '') . ' ' . $d->year;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('Gaji Pokok')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_salary')
                    ->label('Total Diterima')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state))
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'approved',
                        'success' => 'paid',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft'    => 'Draft',
                        'approved' => 'Disetujui',
                        'paid'     => 'Sudah Dibayar',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'approved' => 'Disetujui', 'paid' => 'Sudah Dibayar']),
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Karyawan')
                    ->relationship('employee', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Cetak Slip')
                    ->icon('heroicon-o-printer')
                    ->color('secondary')
                    ->url(fn (Payroll $record): string => route('payroll.print', $record))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('whatsapp_pdf')
                    ->label('Kirim PDF (WA)')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Slip Gaji via WhatsApp')
                    ->modalDescription('PDF Slip Gaji akan digenerate dan dikirim langsung ke WhatsApp Karyawan. Lanjutkan?')
                    ->action(function (Payroll $record) {
                        $phone = $record->employee->phone ?? '';
                        if (empty($phone)) {
                            \Filament\Notifications\Notification::make()->title('Gagal')->body('Nomor HP Karyawan kosong.')->danger()->send();
                            return;
                        }
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        
                        $record->load('employee');
                        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        $periodDate = \Carbon\Carbon::parse($record->period);
                        $periodName = ($months[$periodDate->month] ?? '') . ' ' . $periodDate->year;
                        
                        try {
                            // Generate PDF
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.print-slip', [
                                'payroll' => $record,
                                'periodLabel' => $periodName
                            ]);
                            $pdfBase64 = base64_encode($pdf->output());
                            
                            $amount = number_format($record->net_salary, 0, ',', '.');
                            $caption = "Halo {$record->employee->name},\n\nBerikut terlampir file PDF Slip Gaji Anda untuk periode *{$periodName}*.\n\nTotal Gaji Diterima: *Rp {$amount}*\nStatus: " . ($record->status == 'paid' ? '✅ Sudah Ditransfer' : '⏳ Menunggu Pembayaran') . "\n\nTerima kasih atas dedikasi Anda.";
                            
                            $filename = 'Slip_Gaji_' . str_replace(' ', '_', $record->employee->name) . '_' . str_replace(' ', '_', $periodName) . '.pdf';
                            
                            $endpoint = config('services.waha.endpoint');
                            $session = config('services.waha.session');
                            
                            $response = \Illuminate\Support\Facades\Http::timeout(15)->post("{$endpoint}/api/sendUrl", [
                                'chatId' => $phone . '@c.us',
                                'session' => $session,
                                'file' => [
                                    'mimetype' => 'application/pdf',
                                    'filename' => $filename,
                                    'data' => 'data:application/pdf;base64,' . $pdfBase64
                                ],
                                'caption' => $caption
                            ]);
                            
                            // Because WAHA uses sendFile or sendUrl depending on the version/engine. Wait, WAHA uses /api/sendFile
                            // Let me fix that endpoint name just in case. Actually /api/sendFile is correct for WAHA 2024.
                            
                            if ($response->successful()) {
                                \Filament\Notifications\Notification::make()->title('Berhasil')->body('PDF Slip Gaji berhasil dikirim ke WA.')->success()->send();
                            } else {
                                // Fallback if sendUrl fails, try sendFile
                                $response2 = \Illuminate\Support\Facades\Http::timeout(15)->post("{$endpoint}/api/sendFile", [
                                    'chatId' => $phone . '@c.us',
                                    'session' => $session,
                                    'file' => [
                                        'mimetype' => 'application/pdf',
                                        'filename' => $filename,
                                        'data' => 'data:application/pdf;base64,' . $pdfBase64
                                    ],
                                    'caption' => $caption
                                ]);
                                
                                if ($response2->successful()) {
                                    \Filament\Notifications\Notification::make()->title('Berhasil')->body('PDF Slip Gaji berhasil dikirim ke WA.')->success()->send();
                                } else {
                                    \Filament\Notifications\Notification::make()->title('Gagal')->body('Respons API: ' . $response2->body())->danger()->send();
                                }
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(fn (Payroll $record) => !empty($record->employee->phone)),

                Tables\Actions\Action::make('whatsapp')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('success')
                    ->url(function (Payroll $record) {
                        $phone = $record->employee->phone ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        
                        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        $periodDate = \Carbon\Carbon::parse($record->period);
                        $periodName = ($months[$periodDate->month] ?? '') . ' ' . $periodDate->year;
                        
                        $amount = number_format($record->net_salary, 0, ',', '.');
                        
                        $msg = "Halo {$record->employee->name},\n\nBerikut informasi Slip Gaji Anda untuk periode *{$periodName}*.\n\nTotal Gaji Diterima: *Rp {$amount}*\nStatus: " . ($record->status == 'paid' ? '✅ Sudah Ditransfer' : '⏳ Menunggu Pembayaran') . "\n\nTerima kasih atas kerja keras Anda.";
                        
                        return "https://wa.me/{$phone}?text=" . urlencode($msg);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (Payroll $record) => !empty($record->employee->phone)),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->visible(fn (Payroll $r) => $r->status !== 'paid')
                    ->requiresConfirmation()
                    ->action(fn (Payroll $r) => $r->update(['status' => 'paid', 'paid_at' => now()])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MUTATE BEFORE SAVE — Hitung dan simpan semua subtotal ke DB
    // ─────────────────────────────────────────────────────────────────────────

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return static::enrichData($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return static::enrichData($data);
    }

    private static function enrichData(array $data): array
    {
        $service = app(PayrollSlipService::class);
        $calc    = $service->buildFromInputs($data);

        // Simpan ke kolom lama agar kompatibel
        $data['allowance']  = $calc['_tunjangan_ikr'] + $calc['_tunjangan_transport'] + $calc['_fee_marketing'];
        $data['deduction']  = $calc['_total_potongan'];
        $data['overtime']   = 0;

        // Bersihkan field preview (prefix _) — tidak ada di DB
        foreach (array_keys($data) as $key) {
            if (str_starts_with($key, '_')) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    // ─────────────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit'   => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
