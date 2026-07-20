<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_no ?? '#'.$invoice->id }} — ARMEDIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #1565C0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem 1rem 3rem;
        }

        /* ── Top status icon ── */
        .status-circle {
            width: 64px; height: 64px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            margin-bottom: -32px;
            position: relative; z-index: 2;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .status-circle.paid   { background: #22c55e; }
        .status-circle.unpaid { background: #f59e0b; }

        /* ── Main card ── */
        .invoice-card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 580px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }

        /* ── Card Header ── */
        .card-header {
            padding: 2.5rem 2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px dashed #e2e8f0;
        }
        .logo-area {
            display: flex; align-items: center; gap: 0.75rem;
        }
        .logo-box {
            width: 72px; height: 48px;
            background: linear-gradient(135deg, #1565C0, #0288d1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900; font-size: 0.9rem;
            letter-spacing: -0.5px;
        }
        .logo-tagline { font-size: 0.7rem; color: #94a3b8; margin-top: 2px; }

        .invoice-title-block { text-align: right; }
        .invoice-title-block h1 { font-size: 1.6rem; font-weight: 900; color: #1e293b; letter-spacing: 2px; }
        .invoice-date { font-size: 0.78rem; color: #64748b; margin-top: 2px; }

        /* ── Meta row (date + customer) ── */
        .meta-row {
            padding: 0.75rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
            background: #f8fafc;
            font-size: 0.8rem; color: #64748b;
            border-bottom: 1px dashed #e2e8f0;
        }
        .meta-row strong { color: #1e293b; }

        /* ── Detail rows ── */
        .detail-section { padding: 1.25rem 2rem; }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.55rem 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }
        .detail-row:last-child { border-bottom: none; }
        .det-label { color: #475569; font-weight: 500; min-width: 140px; }
        .det-value { color: #1e293b; font-weight: 600; text-align: right; max-width: 55%; word-break: break-all; }
        .det-value.success { color: #16a34a; font-weight: 700; }
        .det-value.pending { color: #d97706; font-weight: 700; }

        /* ── Divider ── */
        .divider { border: none; border-top: 1px dashed #cbd5e1; margin: 0.25rem 2rem; }

        /* ── Grand Total ── */
        .grand-total {
            padding: 1rem 2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .grand-total .lbl { font-size: 1.1rem; font-weight: 900; color: #1e293b; }
        .grand-total .amt { font-size: 1.3rem; font-weight: 900; color: #1e293b; }

        /* ── Keterangan ── */
        .keterangan {
            padding: 0.75rem 2rem 1.5rem;
            font-size: 0.85rem; color: #475569;
        }
        .keterangan span { color: #16a34a; font-weight: 700; }

        /* ── Pay Button ── */
        .pay-section { padding: 0 2rem 1.5rem; }
        .btn-pay {
            display: block; width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1565C0, #0288d1);
            border: none; border-radius: 12px;
            color: #fff; font-size: 1rem; font-weight: 800;
            cursor: pointer; text-align: center; text-decoration: none;
            box-shadow: 0 6px 20px rgba(21,101,192,0.35);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(21,101,192,0.45); }

        /* ── Thankyou ── */
        .thankyou {
            text-align: center;
            padding: 1rem 2rem 1.5rem;
            font-size: 0.875rem; color: #64748b;
            font-weight: 500;
            border-top: 1px dashed #e2e8f0;
        }

        /* ── Footer branding ── */
        .card-footer {
            padding: 0.75rem 2rem;
            background: #f8fafc;
            display: flex; justify-content: space-between; align-items: center;
            border-top: 1px solid #e2e8f0;
        }
        .powered { font-size: 0.7rem; color: #94a3b8; }
        .powered strong { color: #1565C0; }

        /* ── Bottom dots decoration (like Putra.Net) ── */
        .dots {
            display: flex; justify-content: center; gap: 0.5rem;
            margin-top: 1.5rem;
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.4); }
        .dot.active { background: rgba(255,255,255,0.85); }

        /* ── Back link ── */
        .back-link {
            color: rgba(255,255,255,0.8); font-size: 0.85rem;
            text-decoration: none; margin-bottom: 1rem;
            display: inline-flex; align-items: center; gap: 0.4rem;
            align-self: flex-start; max-width: 580px; width: 100%;
        }
        .back-link:hover { color: #fff; }

        @media (max-width: 480px) {
            .card-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .invoice-title-block { text-align: left; }
        }
    </style>
</head>
<body>

    <a href="{{ route('portal.dashboard') }}" class="back-link">← Kembali ke Dashboard</a>

    @php
        $statusVal = $invoice->status instanceof \App\Enums\InvoiceStatus
            ? $invoice->status->value : $invoice->status;
        $isPaid = in_array($statusVal, ['lunas', 'gratis']);
        $adminFee = 0; // Bisa diset sesuai kebijakan
        $grandTotal = $invoice->amount + $adminFee;
        $invoiceNo = $invoice->invoice_no ?? 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);
    @endphp

    {{-- Status circle (di atas kartu) --}}
    <div class="status-circle {{ $isPaid ? 'paid' : 'unpaid' }}">
        {{ $isPaid ? '✓' : '⏳' }}
    </div>

    <div class="invoice-card">

        {{-- Header --}}
        <div class="card-header">
            <div class="logo-area">
                <div class="logo-box">ARM<br>EDIA</div>
                <div>
                    <div style="font-weight:800;font-size:0.95rem;color:#1e293b">PT. Akses Artha Media</div>
                    <div class="logo-tagline">Internet Provider — Gumelar</div>
                </div>
            </div>
            <div class="invoice-title-block">
                <h1>INVOICE</h1>
                <div class="invoice-date">
                    {{ now()->translatedFormat('d M Y') }} — {{ now()->format('H:i') }}
                </div>
            </div>
        </div>

        {{-- Meta --}}
        <div class="meta-row">
            <span>{{ \Carbon\Carbon::parse($invoice->period)->translatedFormat('d M Y') }}</span>
            <strong>{{ $customer->name }} ({{ $customer->village?->name ?? $customer->kecamatan }})</strong>
        </div>

        {{-- Detail Rows --}}
        <div class="detail-section">
            <div class="detail-row">
                <span class="det-label">Nomor Invoice</span>
                <span class="det-value">{{ $invoiceNo }}</span>
            </div>
            <div class="detail-row">
                <span class="det-label">Nama</span>
                <span class="det-value">{{ $customer->name }} ({{ $customer->village?->name ?? '-' }})</span>
            </div>
            <div class="detail-row">
                <span class="det-label">ID Pelanggan</span>
                <span class="det-value">{{ $customer->id_arm }}</span>
            </div>
            @if($invoice->paid_at)
            <div class="detail-row">
                <span class="det-label">Tanggal Bayar</span>
                <span class="det-value">{{ $invoice->paid_at->translatedFormat('d M Y H:i') }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="det-label">Paket</span>
                <span class="det-value">{{ $customer->internetPackage?->nama_paket ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="det-label">Berlangganan</span>
                <span class="det-value">1 Bulan</span>
            </div>
            @if($invoice->payment_method)
            <div class="detail-row">
                <span class="det-label">Method</span>
                <span class="det-value">{{ strtoupper($invoice->payment_method) }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="det-label">Status</span>
                <span class="det-value {{ $isPaid ? 'success' : 'pending' }}">
                    {{ $isPaid ? 'Lunas ✓' : 'Menunggu Pembayaran' }}
                </span>
            </div>
            <div class="detail-row">
                <span class="det-label">Masa Aktif</span>
                <span class="det-value">
                    {{ \Carbon\Carbon::parse($invoice->period)->translatedFormat('d M Y') }}
                    — {{ \Carbon\Carbon::parse($invoice->period)->addMonth()->subDay()->translatedFormat('d M Y') }}
                </span>
            </div>
            <div class="detail-row">
                <span class="det-label">Nominal</span>
                <span class="det-value">Rp. {{ number_format($invoice->amount, 0, ',', '.') }}</span>
            </div>
            <div class="detail-row">
                <span class="det-label">Diskon</span>
                <span class="det-value">-</span>
            </div>
            <div class="detail-row">
                <span class="det-label">PPN</span>
                <span class="det-value">-</span>
            </div>
            <div class="detail-row">
                <span class="det-label">Admin</span>
                <span class="det-value">{{ $adminFee > 0 ? 'Rp. '.number_format($adminFee,0,',','.') : '-' }}</span>
            </div>
        </div>

        <hr class="divider">

        {{-- Grand Total --}}
        <div class="grand-total">
            <span class="lbl">GRAND TOTAL :</span>
            <span class="amt">Rp. {{ number_format($grandTotal, 0, ',', '.') }}</span>
        </div>

        {{-- Keterangan --}}
        <div class="keterangan">
            <strong>Keterangan :</strong><br>
            @if($isPaid)
                <span>Pembayaran berhasil</span>
            @else
                <span style="color:#d97706">Menunggu pembayaran</span> — Jatuh tempo:
                {{ $invoice->due_date?->translatedFormat('d M Y') ?? '10 '.\Carbon\Carbon::parse($invoice->period)->translatedFormat('M Y') }}
            @endif
        </div>

        {{-- Tombol Bayar (jika belum lunas) --}}
        @if(!$isPaid)
        <div class="pay-section">
            <button id="btn-pay" class="btn-pay" onclick="bayarSekarang()">
                💳 Bayar Sekarang
            </button>
            <p id="pay-error" style="display:none;color:#ef4444;font-size:.8rem;text-align:center;margin-top:.5rem"></p>
        </div>
        @endif

        {{-- Thank you --}}
        <div class="thankyou">
            Terima kasih telah menggunakan layanan kami.
        </div>

        {{-- Footer --}}
        <div class="card-footer">
            <span class="powered">
                CS: <strong>{{ env('ARMEDIA_CS_WA', '0812-XXXX-XXXX') }}</strong>
            </span>
            <span class="powered">
                Powered by <strong>PT. Akses Artha Media</strong>
            </span>
        </div>

    </div>

    {{-- Dots decoration --}}
    <div class="dots">
        <div class="dot active"></div>
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>

</body>

{{-- Midtrans Snap.js --}}
@if(!$isPaid)
@php
    $midtransClientKey = config('midtrans.client_key');
    $midtransEnv = config('midtrans.is_production') ? '' : '.sandbox';
@endphp
<script src="https://app{{ $midtransEnv }}.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
<script>
async function bayarSekarang() {
    const btn = document.getElementById('btn-pay');
    const errEl = document.getElementById('pay-error');
    btn.disabled = true;
    btn.textContent = '⏳ Memproses...';
    errEl.style.display = 'none';

    try {
        const res = await fetch('{{ route('portal.invoice.pay', $invoice->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        });
        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || 'Gagal mendapatkan token pembayaran.');
        }

        // Buka popup Midtrans Snap
        window.snap.pay(data.snap_token, {
            onSuccess: function(result) {
                window.location.reload();
            },
            onPending: function(result) {
                btn.textContent = '⏳ Pembayaran Pending';
                btn.disabled = false;
            },
            onError: function(result) {
                errEl.textContent = 'Pembayaran gagal. Silakan coba lagi.';
                errEl.style.display = 'block';
                btn.textContent = '💳 Bayar Sekarang';
                btn.disabled = false;
            },
            onClose: function() {
                btn.textContent = '💳 Bayar Sekarang';
                btn.disabled = false;
            }
        });
    } catch (err) {
        errEl.textContent = err.message;
        errEl.style.display = 'block';
        btn.textContent = '💳 Bayar Sekarang';
        btn.disabled = false;
    }
}
</script>
@endif
</html>
