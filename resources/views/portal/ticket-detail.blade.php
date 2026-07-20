<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket {{ $ticket->ticket_no }} — ARMEDIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#0f172a;--surface:#1e293b;--border:rgba(255,255,255,0.08);--text:#f1f5f9;--muted:rgba(255,255,255,0.45);--blue:#3b82f6;--cyan:#06b6d4; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
        nav { background:rgba(30,41,59,.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); padding:.9rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:10; }
        .nav-brand { font-weight:800; font-size:1.1rem; } .nav-brand span { color:var(--cyan); }
        .nav-right { display:flex; align-items:center; gap:1rem; }
        .nav-name { color:var(--muted); font-size:.875rem; }
        .btn-logout { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); color:#fca5a5; padding:.4rem .9rem; border-radius:8px; font-size:.8rem; font-weight:600; cursor:pointer; }
        main { max-width:620px; margin:0 auto; padding:2rem 1.25rem 4rem; }
        .back { color:#60a5fa; text-decoration:none; font-size:.875rem; display:inline-flex; align-items:center; gap:.4rem; margin-bottom:1.5rem; }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
        .card-header { background:linear-gradient(135deg,#1e3a5f,#0f2744); padding:1.5rem 1.75rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:.75rem; }
        .tkt-no { font-size:.75rem; color:rgba(255,255,255,.45); font-family:monospace; margin-bottom:.25rem; }
        .tkt-title { font-size:1.2rem; font-weight:800; }
        .badge { padding:.3rem .9rem; border-radius:20px; font-size:.75rem; font-weight:800; }
        .badge.open { background:rgba(239,68,68,.2); color:#fca5a5; border:1px solid rgba(239,68,68,.3); }
        .badge.process { background:rgba(245,158,11,.2); color:#fcd34d; border:1px solid rgba(245,158,11,.3); }
        .badge.resolved { background:rgba(34,197,94,.2); color:#86efac; border:1px solid rgba(34,197,94,.3); }
        .badge.closed { background:rgba(148,163,184,.1); color:#94a3b8; border:1px solid rgba(148,163,184,.2); }
        .card-body { padding:1.5rem 1.75rem; }
        .row { padding:.65rem 0; border-bottom:1px solid rgba(255,255,255,.05); font-size:.875rem; display:flex; justify-content:space-between; gap:1rem; }
        .row:last-child { border:none; }
        .row label { color:var(--muted); min-width:120px; }
        .row span { font-weight:600; text-align:right; }
        .desc-box { background:rgba(255,255,255,.04); border:1px solid var(--border); border-radius:10px; padding:1rem; margin:1rem 0; font-size:.9rem; line-height:1.6; color:#cbd5e1; }
        .tech-box { background:rgba(34,197,94,.07); border:1px solid rgba(34,197,94,.2); border-radius:10px; padding:1rem; margin-top:1rem; }
        .tech-box .tech-title { font-size:.75rem; color:#86efac; font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.5rem; }
        .tech-box p { font-size:.9rem; color:#d1fae5; line-height:1.6; }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">ARM<span>EDIA</span> Portal</div>
        <div class="nav-right">
            <span class="nav-name">{{ $member->nama }}</span>
            <form method="POST" action="{{ route('portal.logout') }}" style="display:inline;">
                @csrf <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>
    <main>
        <a href="{{ route('portal.tickets') }}" class="back">← Kembali ke Daftar Tiket</a>

        @php
            $statusVal = $ticket->status instanceof \App\Enums\TicketStatus ? $ticket->status->value : $ticket->status;
            $catVal    = $ticket->category instanceof \App\Enums\TicketCategory ? $ticket->category->value : $ticket->category;
            $catLabel  = match($catVal) {
                'internet_mati' => '📵 Internet Mati / Putus Total',
                'lambat'        => '🐢 Internet Sangat Lambat',
                'wifi_masalah'  => '📶 Wi-Fi Tidak Terdeteksi',
                default         => '💬 Keluhan Lainnya',
            };
        @endphp

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="tkt-no">{{ $ticket->ticket_no }}</div>
                    <div class="tkt-title">{{ $catLabel }}</div>
                </div>
                <span class="badge {{ $statusVal }}">
                    @switch($statusVal)
                        @case('open') 🔴 Baru / Open @break
                        @case('process') 🟡 Sedang Diproses @break
                        @case('resolved') ✅ Selesai @break
                        @default ⚫ Ditutup
                    @endswitch
                </span>
            </div>

            <div class="card-body">
                <div class="row">
                    <label>ID Pelanggan</label>
                    <span>{{ $customer->id_arm }}</span>
                </div>
                <div class="row">
                    <label>Dilaporkan</label>
                    <span>{{ $ticket->created_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
                @if($ticket->resolved_at)
                <div class="row">
                    <label>Diselesaikan</label>
                    <span>{{ $ticket->resolved_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
                @endif

                <p style="font-size:.75rem;color:var(--muted);margin:1rem 0 .4rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Kronologi Keluhan</p>
                <div class="desc-box">{{ $ticket->description }}</div>

                @if($ticket->technician_notes)
                <div class="tech-box">
                    <div class="tech-title">🔧 Respon / Catatan Teknisi</div>
                    <p>{{ $ticket->technician_notes }}</p>
                </div>
                @else
                <p style="text-align:center;color:var(--muted);font-size:.85rem;margin-top:1.25rem;">
                    ⏳ Menunggu respon teknisi...
                </p>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
