<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji — {{ $payroll->employee->name }} — {{ $periodLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #f5f5f5;
        }

        .slip-wrapper {
            max-width: 700px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        /* ── KOP ─────────────────────────────── */
        .kop {
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
            color: white;
            padding: 24px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .kop-logo img { height: 52px; }
        .kop-logo .logo-placeholder {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 10px; text-align: center; color: rgba(255,255,255,0.9);
        }
        .kop-text h1 { font-size: 20px; font-weight: 800; letter-spacing: 0.5px; }
        .kop-text p { font-size: 10px; opacity: 0.85; margin-top: 2px; }
        .kop-right { margin-left: auto; text-align: right; }
        .kop-right .slip-label {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
        }
        .kop-right .period { font-size: 14px; font-weight: 700; margin-top: 6px; }
        .kop-right .print-date { font-size: 10px; opacity: 0.75; margin-top: 2px; }

        /* ── BODY ────────────────────────────── */
        .body { padding: 24px 30px; }

        /* ── DATA KARYAWAN ───────────────────── */
        .employee-card {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 14px 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 30px;
            margin-bottom: 20px;
        }
        .employee-card .field-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .employee-card .field-value { font-size: 12px; font-weight: 600; color: #111; margin-top: 1px; }

        /* ── TABEL ───────────────────────────── */
        h3.section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        table.slip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.slip-table th {
            background: #f3f4f6;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            padding: 8px 12px;
            text-align: left;
        }
        table.slip-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }
        table.slip-table .td-note {
            font-size: 10px;
            color: #9ca3af;
        }
        table.slip-table .td-amount {
            text-align: right;
            font-weight: 600;
        }
        table.slip-table .td-subtotal {
            font-size: 11px;
            font-weight: 700;
            color: #374151;
        }

        /* ── SUBTOTAL ROW ────────────────────── */
        .subtotal-row td {
            background: #f9fafb;
            font-weight: 700;
            border-top: 2px solid #e5e7eb;
            font-size: 12px;
        }

        /* ── TOTAL DITERIMA ──────────────────── */
        .total-box {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
            color: white;
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .total-box .label { font-size: 13px; font-weight: 700; opacity: 0.9; }
        .total-box .amount { font-size: 24px; font-weight: 900; letter-spacing: 0.5px; }

        @php $isNegative = $payroll->net_salary < 0; @endphp
        @if($isNegative)
        .total-box { background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%); }
        @endif

        /* ── CATATAN ─────────────────────────── */
        .catatan {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 10.5px;
            color: #78350f;
        }
        .catatan ol { padding-left: 16px; margin-top: 6px; }
        .catatan li { margin-bottom: 3px; }

        /* ── TANDA TANGAN ────────────────────── */
        .ttd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 8px;
        }
        .ttd-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            text-align: center;
        }
        .ttd-box .ttd-label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .ttd-box .ttd-name { font-size: 12px; font-weight: 700; color: #111; }
        .ttd-box .ttd-space { height: 56px; }
        .ttd-box .ttd-sign { border-top: 1px solid #d1d5db; padding-top: 6px; font-size: 10px; color: #6b7280; }

        /* ── FOOTER ──────────────────────────── */
        .slip-footer {
            border-top: 1px solid #e5e7eb;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5px;
            color: #9ca3af;
        }
        .confidential { color: #dc2626; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ── PRINT ───────────────────────────── */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #b91c1c;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(185,28,28,0.4);
        }
        .print-btn:hover { background: #991b1b; }

        @media print {
            body { background: white; }
            .slip-wrapper { box-shadow: none; margin: 0; max-width: 100%; border-radius: 0; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨️ Cetak</button>

<div class="slip-wrapper">

    {{-- ── KOP ─────────────────────────────────────────────────── --}}
    <div class="kop">
        <div class="kop-logo">
            <img src="https://ik.imagekit.io/Gumelar/LogO/logo%20pt.png?updatedAt=1778213993513"
                 alt="ARMEDIA" onerror="this.style.display='none'">
        </div>
        <div class="kop-text">
            <h1>ARMEDIA NET</h1>
            <p>PT. AKSES ARTHA MEDIA</p>
            <p>Gumelar, Banyumas, Jawa Tengah</p>
        </div>
        <div class="kop-right">
            <div class="slip-label">Slip Gaji Resmi</div>
            <div class="period">{{ $periodLabel }}</div>
            <div class="print-date">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="body">

        {{-- ── DATA KARYAWAN ─────────────────────────────────────── --}}
        <div class="employee-card">
            <div>
                <div class="field-label">Nama Karyawan</div>
                <div class="field-value">{{ $payroll->employee->name }}</div>
            </div>
            <div>
                <div class="field-label">No. Karyawan</div>
                <div class="field-value">{{ $payroll->employee->employee_no ?? '—' }}</div>
            </div>
            <div>
                <div class="field-label">Jabatan</div>
                <div class="field-value">{{ $payroll->employee->position ?? '—' }}</div>
            </div>
            <div>
                <div class="field-label">Status</div>
                <div class="field-value">{{ ucfirst($payroll->employee->employment_type ?? 'Karyawan Tetap') }}</div>
            </div>
            <div>
                <div class="field-label">Tanggal Bergabung</div>
                <div class="field-value">{{ optional($payroll->employee->join_date)->format('d M Y') ?? '—' }}</div>
            </div>
            <div>
                <div class="field-label">Masa Kerja</div>
                <div class="field-value">{{ $payroll->masa_kerja }}</div>
            </div>
        </div>

        {{-- ── PENDAPATAN ────────────────────────────────────────── --}}
        <h3 class="section-title">Rincian Pendapatan</h3>
        <table class="slip-table">
            <thead>
                <tr>
                    <th style="width:50%">Komponen</th>
                    <th>Perhitungan</th>
                    <th style="text-align:right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="td-note">1 × Rp {{ number_format((int) $payroll->basic_salary) }}</td>
                    <td class="td-amount">Rp {{ number_format((int) $payroll->basic_salary) }}</td>
                </tr>
                <tr>
                    <td>
                        Tunjangan Kinerja (IKR)
                        <div class="td-note">{{ $payroll->jumlah_ikr }} pemasangan × Rp {{ number_format($payroll->tarif_ikr) }}</div>
                        <div class="td-note">(Fee Rp {{ number_format((int) $payroll->fee_ikr_per_pelanggan) }} ÷ {{ $payroll->jumlah_teknisi_pasang }} teknisi = Rp {{ number_format($payroll->tarif_ikr) }}/unit)</div>
                    </td>
                    <td class="td-note">{{ $payroll->jumlah_ikr }} × Rp {{ number_format($payroll->tarif_ikr) }}</td>
                    <td class="td-amount">Rp {{ number_format($payroll->tunjangan_ikr) }}</td>
                </tr>
                <tr>
                    <td>
                        Tunjangan Transport
                        <div class="td-note">{{ $payroll->hari_hadir }} hari hadir × Rp {{ number_format(\App\Models\Payroll::TARIF_TRANSPORT) }}</div>
                    </td>
                    <td class="td-note">{{ $payroll->hari_hadir }} × Rp {{ number_format(\App\Models\Payroll::TARIF_TRANSPORT) }}</td>
                    <td class="td-amount">Rp {{ number_format($payroll->tunjangan_transport) }}</td>
                </tr>
                @if($payroll->jumlah_referral > 0)
                <tr>
                    <td>
                        Fee Marketing (Referral)
                        <div class="td-note">{{ $payroll->jumlah_referral }} pelanggan × Rp {{ number_format(\App\Models\Payroll::TARIF_REFERRAL) }}</div>
                    </td>
                    <td class="td-note">{{ $payroll->jumlah_referral }} × Rp {{ number_format(\App\Models\Payroll::TARIF_REFERRAL) }}</td>
                    <td class="td-amount">Rp {{ number_format($payroll->fee_marketing) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="2">Total Pendapatan</td>
                    <td class="td-amount td-subtotal">Rp {{ number_format($payroll->total_pendapatan) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- ── POTONGAN ───────────────────────────────────────────── --}}
        @if($payroll->total_potongan > 0)
        <h3 class="section-title">Rincian Potongan</h3>
        <table class="slip-table">
            <thead>
                <tr>
                    <th style="width:50%">Komponen</th>
                    <th>Keterangan</th>
                    <th style="text-align:right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if($payroll->kasbon > 0)
                <tr>
                    <td>Kasbon / Pinjaman</td>
                    <td class="td-note">—</td>
                    <td class="td-amount" style="color: #dc2626;">- Rp {{ number_format($payroll->kasbon) }}</td>
                </tr>
                @endif
                @if($payroll->lain_lain_potong > 0)
                <tr>
                    <td>Potongan Lain-lain</td>
                    <td class="td-note">{{ $payroll->ket_lain_lain ?? '—' }}</td>
                    <td class="td-amount" style="color: #dc2626;">- Rp {{ number_format($payroll->lain_lain_potong) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="2">Total Potongan</td>
                    <td class="td-amount td-subtotal" style="color: #dc2626;">- Rp {{ number_format($payroll->total_potongan) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- ── TOTAL DITERIMA ────────────────────────────────────── --}}
        <div class="total-box">
            <div class="label">💵 TOTAL GAJI DITERIMA</div>
            <div class="amount">Rp {{ number_format($payroll->net_salary) }}</div>
        </div>

        @if($payroll->notes)
        <div class="catatan" style="margin-bottom: 12px; background: #f0f9ff; border-color: #bae6fd; color: #0c4a6e;">
            <strong>Catatan:</strong> {{ $payroll->notes }}
        </div>
        @endif

        {{-- ── CATATAN RESMI ─────────────────────────────────────── --}}
        <div class="catatan">
            <strong>Catatan Resmi:</strong>
            <ol>
                <li>Tunjangan Kinerja (IKR): fee IKR per pelanggan dibagi rata ke seluruh teknisi pemasang pada periode ini.</li>
                <li>Tunjangan Transport dihitung berdasarkan jumlah hari kerja sesuai laporan harian.</li>
                <li>Fee Marketing dihitung dari jumlah pelanggan baru yang merupakan hasil referral karyawan bersangkutan.</li>
                <li>Slip gaji ini bersifat <strong>resmi dan rahasia</strong>. Mohon tidak disebarluaskan.</li>
            </ol>
        </div>

        {{-- ── TANDA TANGAN ─────────────────────────────────────── --}}
        <div class="ttd-grid">
            <div class="ttd-box">
                <div class="ttd-label">Karyawan</div>
                <div class="ttd-space"></div>
                <div class="ttd-sign">
                    <div class="ttd-name">{{ $payroll->employee->name }}</div>
                    <div>( Tanda Tangan )</div>
                </div>
            </div>
            <div class="ttd-box">
                <div class="ttd-label">Diketahui Oleh</div>
                <div class="ttd-space"></div>
                <div class="ttd-sign">
                    <div class="ttd-name">PT. AKSES ARTHA MEDIA</div>
                    <div>( Pimpinan )</div>
                </div>
            </div>
        </div>

    </div>{{-- /body --}}

    {{-- ── FOOTER ────────────────────────────────────────────────── --}}
    <div class="slip-footer">
        <span>ARMEDIA NET — PT. AKSES ARTHA MEDIA | Gumelar, Banyumas</span>
        <span class="confidential">🔒 Dokumen Rahasia</span>
        <span>{{ $periodLabel }}</span>
    </div>

</div>{{-- /slip-wrapper --}}

</body>
</html>
