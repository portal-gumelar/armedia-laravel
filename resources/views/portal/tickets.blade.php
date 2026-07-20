<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Gangguan — ARMEDIA Portal</title>
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
        main { max-width:800px; margin:0 auto; padding:2rem 1.25rem 4rem; }
        .back { color:#60a5fa; text-decoration:none; font-size:.875rem; display:inline-flex; align-items:center; gap:.4rem; margin-bottom:1.5rem; }
        h2 { font-size:1.4rem; font-weight:800; margin-bottom:.25rem; }
        .subtitle { color:var(--muted); font-size:.875rem; margin-bottom:2rem; }
        .alert-success { background:rgba(34,197,94,.15); border:1px solid rgba(34,197,94,.3); border-radius:10px; padding:.75rem 1rem; color:#86efac; font-size:.875rem; margin-bottom:1.5rem; }
        .btn-new { display:inline-flex; align-items:center; gap:.5rem; background:linear-gradient(135deg,#ef4444,#f97316); border:none; border-radius:10px; color:#fff; padding:.6rem 1.25rem; font-weight:700; font-size:.875rem; cursor:pointer; text-decoration:none; box-shadow:0 4px 14px rgba(239,68,68,.35); margin-bottom:1.5rem; }
        .ticket-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:1.25rem; margin-bottom:.75rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; text-decoration:none; color:var(--text); transition:border-color .2s; }
        .ticket-card:hover { border-color:rgba(59,130,246,.35); }
        .tkt-no { font-size:.75rem; color:var(--muted); margin-bottom:.25rem; font-family:monospace; }
        .tkt-cat { font-weight:700; font-size:1rem; margin-bottom:.25rem; }
        .tkt-desc { font-size:.8rem; color:var(--muted); max-width:480px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .tkt-date { font-size:.75rem; color:var(--muted); margin-top:.25rem; }
        .badge { padding:.3rem .8rem; border-radius:20px; font-size:.7rem; font-weight:800; white-space:nowrap; }
        .badge.open { background:rgba(239,68,68,.15); color:#fca5a5; border:1px solid rgba(239,68,68,.3); }
        .badge.process { background:rgba(245,158,11,.15); color:#fcd34d; border:1px solid rgba(245,158,11,.3); }
        .badge.resolved { background:rgba(34,197,94,.15); color:#86efac; border:1px solid rgba(34,197,94,.3); }
        .badge.closed { background:rgba(148,163,184,.1); color:#94a3b8; border:1px solid rgba(148,163,184,.2); }
        .empty { text-align:center; color:var(--muted); padding:3rem 1rem; }
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
        <a href="{{ route('portal.dashboard') }}" class="back">← Kembali ke Dashboard</a>
        <h2>🛠️ Riwayat Laporan Gangguan</h2>
        <p class="subtitle">Semua laporan gangguan dari ID Pelanggan <strong>{{ $customer->id_arm }}</strong></p>

        @if(session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        <a href="{{ route('portal.ticket.create') }}" class="btn-new">🚨 Laporkan Gangguan Baru</a>

        @forelse($tickets as $ticket)
        <a href="{{ route('portal.ticket.show', $ticket->id) }}" class="ticket-card">
            <div>
                <div class="tkt-no">{{ $ticket->ticket_no }}</div>
                <div class="tkt-cat">
                    @switch($ticket->category instanceof \App\Enums\TicketCategory ? $ticket->category->value : $ticket->category)
                        @case('internet_mati') 📵 Internet Mati / Putus Total @break
                        @case('lambat') 🐢 Internet Sangat Lambat @break
                        @case('wifi_masalah') 📶 Wi-Fi Tidak Terdeteksi @break
                        @default 💬 Keluhan Lainnya
                    @endswitch
                </div>
                <div class="tkt-desc">{{ $ticket->description }}</div>
                <div class="tkt-date">Dilaporkan: {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}</div>
            </div>
            @php $statusVal = $ticket->status instanceof \App\Enums\TicketStatus ? $ticket->status->value : $ticket->status; @endphp
            <span class="badge {{ $statusVal }}">
                @switch($statusVal)
                    @case('open') 🔴 Baru @break
                    @case('process') 🟡 Diproses @break
                    @case('resolved') 🟢 Selesai @break
                    @default ⚫ Ditutup
                @endswitch
            </span>
        </a>
        @empty
        <div class="empty">
            <p style="font-size:2.5rem;margin-bottom:.75rem">🎉</p>
            <p>Tidak ada laporan gangguan. Semoga internet Anda selalu lancar!</p>
        </div>
        @endforelse
    </main>
</body>
</html>
