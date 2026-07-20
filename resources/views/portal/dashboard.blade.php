<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Saya — Portal ARMEDIA</title>
    <meta name="description" content="Cek tagihan internet, poin reward, dan status langganan Anda di portal pelanggan ARMEDIA.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --surface2: #273549;
            --border: rgba(255,255,255,0.08);
            --text: #f1f5f9;
            --muted: rgba(255,255,255,0.45);
            --blue: #3b82f6;
            --cyan: #06b6d4;
            --green: #22c55e;
            --yellow: #f59e0b;
            --red: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── TOP NAV ── */
        nav {
            background: rgba(30,41,59,0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0.9rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .nav-brand { font-weight: 800; font-size: 1.1rem; }
        .nav-brand span { color: var(--cyan); }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .nav-name { color: var(--muted); font-size: 0.875rem; }
        .btn-logout {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.25); }

        /* ── MAIN ── */
        main { max-width: 900px; margin: 0 auto; padding: 2rem 1.25rem 4rem; }

        h2 { font-size: 1.4rem; font-weight: 800; margin-bottom: 0.25rem; }
        .subtitle { color: var(--muted); font-size: 0.875rem; margin-bottom: 2rem; }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }
        .stat-card.blue::before { background: linear-gradient(90deg, var(--blue), var(--cyan)); }
        .stat-card.green::before { background: linear-gradient(90deg, var(--green), #84cc16); }
        .stat-card.yellow::before { background: var(--yellow); }
        .stat-card.red::before { background: var(--red); }

        .stat-label { font-size: 0.75rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.4rem; font-weight: 800; }
        .stat-sub { font-size: 0.75rem; color: var(--muted); margin-top: 0.2rem; }

        /* ── SECTION ── */
        .section-title {
            font-size: 1rem; font-weight: 700;
            margin-bottom: 1rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .badge {
            background: rgba(59,130,246,0.15);
            color: var(--blue);
            border-radius: 20px;
            padding: 0.1rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* ── INVOICE TABLE ── */
        .invoice-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2.5rem; }

        .invoice-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            transition: border-color 0.2s;
        }
        .invoice-card:hover { border-color: rgba(59,130,246,0.3); }

        .inv-no { font-size: 0.75rem; color: var(--muted); margin-bottom: 0.2rem; }
        .inv-period { font-weight: 700; font-size: 1rem; }
        .inv-amount { font-weight: 800; font-size: 1.1rem; color: var(--text); }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .status-badge.belum { background: rgba(245,158,11,0.15); color: #fcd34d; border: 1px solid rgba(245,158,11,0.3); }
        .status-badge.lunas { background: rgba(34,197,94,0.15); color: #86efac; border: 1px solid rgba(34,197,94,0.3); }
        .status-badge.gratis { background: rgba(59,130,246,0.15); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }

        .btn-pay {
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            border: none;
            border-radius: 10px;
            color: #fff;
            padding: 0.5rem 1.1rem;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 12px rgba(59,130,246,0.3);
        }
        .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }

        /* ── POINT HISTORY ── */
        .point-list { display: flex; flex-direction: column; gap: 0.6rem; }
        .point-row {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.7rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .point-desc { font-size: 0.875rem; }
        .point-date { font-size: 0.75rem; color: var(--muted); }
        .point-amt { font-weight: 700; font-size: 0.95rem; }
        .point-amt.plus { color: var(--green); }
        .point-amt.minus { color: var(--red); }

        .empty-state {
            text-align: center;
            color: var(--muted);
            padding: 2rem;
            font-size: 0.9rem;
        }

        .info-box {
            background: rgba(59,130,246,0.08);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }
        .info-box strong { color: var(--blue); }

        @media (max-width: 600px) {
            .invoice-card { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">ARM<span>EDIA</span> Portal</div>
        <div class="nav-right">
            <span class="nav-name">{{ $member->nama }}</span>
            <form method="POST" action="{{ route('portal.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <main>
        <h2>Selamat datang, {{ explode(' ', $member->nama)[0] }}! 👋</h2>
        <p class="subtitle">ID Pelanggan: <strong>{{ $customer->id_arm }}</strong> — {{ $customer->village?->name ?? $customer->kecamatan }}</p>

        {{-- Info Paket --}}
        <div class="info-box">
            <strong>📶 Paket Aktif:</strong> {{ $customer->internetPackage?->nama_paket ?? 'Belum ada paket' }}
            &nbsp;|&nbsp;
            <strong>Status:</strong> {{ $customer->subscription_status?->label() ?? $customer->subscription_status }}
        </div>

        {{-- Stat Cards --}}
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">ID Pelanggan</div>
                <div class="stat-value">{{ $customer->id_arm }}</div>
                <div class="stat-sub">{{ $customer->ip_address ?? 'IP belum diset' }}</div>
            </div>
            <div class="stat-card {{ $unpaidInvoices->count() > 0 ? 'red' : 'green' }}">
                <div class="stat-label">Tagihan Belum Lunas</div>
                <div class="stat-value">{{ $unpaidInvoices->count() }}</div>
                <div class="stat-sub">invoice</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">Total Poin ACR</div>
                <div class="stat-value">{{ number_format($member->total_poin) }}</div>
                <div class="stat-sub">poin reward</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Level Member</div>
                <div class="stat-value" style="font-size:1.1rem">{{ $member->level_member }}</div>
                <div class="stat-sub">sejak {{ $customer->activated_at?->format('M Y') ?? '-' }}</div>
            </div>
        </div>

        {{-- Tagihan Belum Lunas --}}
        @if($unpaidInvoices->count() > 0)
        <div class="section-title">
            🔴 Tagihan Belum Lunas
            <span class="badge">{{ $unpaidInvoices->count() }}</span>
        </div>
        <div class="invoice-list" style="margin-bottom:2.5rem">
            @foreach($unpaidInvoices as $inv)
            <div class="invoice-card">
                <div>
                    <div class="inv-no">{{ $inv->invoice_no ?? 'INV-' . str_pad($inv->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="inv-period">{{ \Carbon\Carbon::parse($inv->period)->translatedFormat('F Y') }}</div>
                    <div class="stat-sub">Jatuh tempo: {{ $inv->due_date?->format('d M Y') ?? '10 ' . \Carbon\Carbon::parse($inv->period)->translatedFormat('M Y') }}</div>
                </div>
                <div class="inv-amount">Rp {{ number_format($inv->amount, 0, ',', '.') }}</div>
                <span class="status-badge belum">Belum Lunas</span>
                <a href="{{ route('portal.invoice', $inv->id) }}" class="btn-pay">💳 Bayar Sekarang</a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Tiket Gangguan Aktif --}}
        @if($activeTickets->count() > 0)
        <div class="section-title" style="margin-top:2rem">
            🔴 Gangguan Sedang Dilaporkan
            <span class="badge">{{ $activeTickets->count() }}</span>
        </div>
        <div class="invoice-list" style="margin-bottom:2rem">
            @foreach($activeTickets as $tkt)
            @php $tktStatus = $tkt->status instanceof \App\Enums\TicketStatus ? $tkt->status->value : $tkt->status; @endphp
            <a href="{{ route('portal.ticket.show', $tkt->id) }}" style="text-decoration:none;color:inherit">
            <div class="invoice-card" style="border-color:rgba(239,68,68,0.25)">
                <div>
                    <div class="inv-no">{{ $tkt->ticket_no }}</div>
                    <div class="inv-period">
                        @switch($tkt->category instanceof \App\Enums\TicketCategory ? $tkt->category->value : $tkt->category)
                            @case('internet_mati') 📵 Internet Mati @break
                            @case('lambat') 🐢 Lambat @break
                            @case('wifi_masalah') 📶 Wi-Fi Masalah @break
                            @default 💬 Keluhan Lain
                        @endswitch
                    </div>
                    <div class="stat-sub">{{ $tkt->created_at->diffForHumans() }}</div>
                </div>
                <span class="status-badge {{ $tktStatus === 'process' ? 'belum' : 'belum' }}">
                    {{ $tktStatus === 'open' ? '🔴 Baru' : '🟡 Diproses' }}
                </span>
            </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Riwayat Tagihan --}}
        <div class="section-title">📋 Riwayat Tagihan (12 Bulan Terakhir)</div>
        <div class="invoice-list">
            @forelse($invoiceHistory as $inv)
            <div class="invoice-card">
                <div>
                    <div class="inv-no">{{ $inv->invoice_no ?? 'INV-' . str_pad($inv->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="inv-period">{{ \Carbon\Carbon::parse($inv->period)->translatedFormat('F Y') }}</div>
                </div>
                <div class="inv-amount">Rp {{ number_format($inv->amount, 0, ',', '.') }}</div>
                @php
                    $statusVal = $inv->status instanceof \App\Enums\InvoiceStatus
                        ? $inv->status->value
                        : $inv->status;
                @endphp
                <span class="status-badge {{ $statusVal }}">
                    {{ $statusVal === 'lunas' ? '✅ Lunas' : ($statusVal === 'gratis' ? '🎁 Gratis' : '⏳ Belum') }}
                </span>
                @if($inv->paid_at)
                    <span style="font-size:0.75rem;color:var(--muted)">{{ $inv->paid_at->format('d M Y') }}</span>
                @endif
            </div>
            @empty
            <div class="empty-state">Belum ada riwayat tagihan.</div>
            @endforelse
        </div>

        {{-- Riwayat Poin --}}
        @if($pointHistory->count() > 0)
        <div class="section-title" style="margin-top:2rem">⭐ Riwayat Poin ACR</div>
        <div class="point-list">
            @foreach($pointHistory as $pt)
            <div class="point-row">
                <div>
                    <div class="point-desc">{{ $pt->keterangan ?? $pt->description ?? 'Transaksi poin' }}</div>
                    <div class="point-date">{{ $pt->created_at->format('d M Y, H:i') }}</div>
                </div>
                <span class="point-amt {{ $pt->jenis === 'penambahan' ? 'plus' : 'minus' }}">
                    {{ $pt->jenis === 'penambahan' ? '+' : '-' }}{{ number_format($pt->jumlah_poin) }} poin
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </main>
</body>
</html>
