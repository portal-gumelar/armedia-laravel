<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporkan Gangguan — ARMEDIA Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#0f172a;--surface:#1e293b;--border:rgba(255,255,255,0.08);--text:#f1f5f9;--muted:rgba(255,255,255,0.45);--blue:#3b82f6;--cyan:#06b6d4;--red:#ef4444; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
        nav { background:rgba(30,41,59,.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); padding:.9rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:10; }
        .nav-brand { font-weight:800; font-size:1.1rem; } .nav-brand span { color:var(--cyan); }
        .nav-right { display:flex; align-items:center; gap:1rem; }
        .nav-name { color:var(--muted); font-size:.875rem; }
        .btn-logout { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); color:#fca5a5; padding:.4rem .9rem; border-radius:8px; font-size:.8rem; font-weight:600; cursor:pointer; }
        main { max-width:600px; margin:0 auto; padding:2rem 1.25rem 4rem; }
        .back { color:#60a5fa; text-decoration:none; font-size:.875rem; display:inline-flex; align-items:center; gap:.4rem; margin-bottom:1.5rem; }
        h2 { font-size:1.4rem; font-weight:800; margin-bottom:.25rem; }
        .subtitle { color:var(--muted); font-size:.875rem; margin-bottom:2rem; }
        .alert-error { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); border-radius:10px; padding:.75rem 1rem; color:#fca5a5; font-size:.875rem; margin-bottom:1.25rem; }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:2rem; }
        label { display:block; color:var(--muted); font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.4rem; }
        select, textarea { width:100%; padding:.8rem 1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); border-radius:10px; color:var(--text); font-family:'Inter',sans-serif; font-size:.95rem; margin-bottom:1.25rem; transition:border-color .2s; }
        select { cursor:pointer; }
        select:focus, textarea:focus { outline:none; border-color:var(--blue); background:rgba(59,130,246,.08); }
        select option { background:#1e293b; }
        textarea { resize:vertical; min-height:120px; }
        .category-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.25rem; }
        .cat-option { display:none; }
        .cat-label { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.5rem; padding:1rem; background:rgba(255,255,255,.04); border:2px solid rgba(255,255,255,.08); border-radius:12px; cursor:pointer; font-size:.8rem; font-weight:600; text-align:center; transition:all .2s; }
        .cat-label .icon { font-size:1.8rem; }
        .cat-option:checked + .cat-label { border-color:var(--blue); background:rgba(59,130,246,.12); color:#93c5fd; }
        .cat-label:hover { border-color:rgba(59,130,246,.4); background:rgba(59,130,246,.06); }
        .btn-submit { width:100%; padding:.95rem; background:linear-gradient(135deg,#ef4444,#f97316); border:none; border-radius:12px; color:#fff; font-size:1rem; font-weight:800; cursor:pointer; box-shadow:0 6px 20px rgba(239,68,68,.35); transition:transform .15s; }
        .btn-submit:hover { transform:translateY(-2px); }
        .char-count { font-size:.75rem; color:var(--muted); text-align:right; margin-top:-.9rem; margin-bottom:1rem; }
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
        <a href="{{ route('portal.dashboard') }}" class="back">← Kembali ke Dashboard</a>
        <h2>🛠️ Laporkan Gangguan</h2>
        <p class="subtitle">Isi formulir di bawah. Tim teknisi kami akan langsung mendapat notifikasi.</p>

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="card">
            <form method="POST" action="{{ route('portal.ticket.store') }}">
                @csrf

                <label>Pilih Kategori Gangguan</label>
                <div class="category-grid">
                    @foreach($categories as $cat)
                    <div>
                        <input type="radio" name="category" id="cat_{{ $cat->value }}" value="{{ $cat->value }}" class="cat-option" {{ old('category') === $cat->value ? 'checked' : '' }}>
                        <label for="cat_{{ $cat->value }}" class="cat-label">
                            <span class="icon">
                                @switch($cat->value)
                                    @case('internet_mati') 📵 @break
                                    @case('lambat') 🐢 @break
                                    @case('wifi_masalah') 📶 @break
                                    @default 💬
                                @endswitch
                            </span>
                            {{ $cat->getLabel() }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <label for="description">Ceritakan Keluhan Anda</label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="Contoh: Sejak tadi pagi lampu LOS di modem berkedip merah setelah hujan lebat. Internet sama sekali tidak bisa konek..."
                    maxlength="1000"
                    oninput="document.getElementById('charCount').textContent = this.value.length"
                    required
                >{{ old('description') }}</textarea>
                <div class="char-count"><span id="charCount">0</span>/1000 karakter</div>

                <button type="submit" class="btn-submit">🚨 Kirim Laporan Gangguan</button>
            </form>
        </div>
    </main>
</body>
</html>
