<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal Pelanggan — ARMEDIA</title>
    <meta name="description" content="Login ke portal pelanggan PT. Akses Artha Media untuk cek tagihan, poin, dan status langganan internet Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f2744 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-wrap .icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(59,130,246,0.4);
        }
        .logo-wrap h1 { color: #fff; font-size: 1.5rem; font-weight: 800; }
        .logo-wrap p { color: rgba(255,255,255,0.55); font-size: 0.875rem; margin-top: 0.25rem; }

        .alert-error {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #fca5a5;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .alert-success {
            background: rgba(34,197,94,0.15);
            border: 1px solid rgba(34,197,94,0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #86efac;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            margin-bottom: 1.1rem;
            transition: border-color 0.2s, background 0.2s;
        }
        input::placeholder { color: rgba(255,255,255,0.3); }
        input:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(59,130,246,0.08);
        }

        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 6px 20px rgba(59,130,246,0.35);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(59,130,246,0.45);
        }
        .btn-login:active { transform: translateY(0); }

        .hint {
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
            margin-top: 1.5rem;
            line-height: 1.6;
        }
        .hint a {
            color: #60a5fa;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-wrap">
            <div class="icon">🌐</div>
            <h1>Portal Pelanggan</h1>
            <p>PT. Akses Artha Media — ARMEDIA</p>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('portal.login.post') }}">
            @csrf
            <label for="id_arm">ID Pelanggan</label>
            <input
                type="text"
                id="id_arm"
                name="id_arm"
                placeholder="Contoh: ARM-0001"
                value="{{ old('id_arm') }}"
                autocomplete="off"
                required
            >

            <label for="whatsapp">Nomor WhatsApp</label>
            <input
                type="tel"
                id="whatsapp"
                name="whatsapp"
                placeholder="Contoh: 08123456789"
                value="{{ old('whatsapp') }}"
                autocomplete="tel"
                required
            >

            <button type="submit" class="btn-login">
                🔑 Masuk ke Portal
            </button>
        </form>

        <p class="hint">
            Tidak tahu ID Pelanggan Anda?<br>
            Hubungi CS ARMEDIA: <a href="https://wa.me/{{ env('ARMEDIA_CS_WA') }}">WhatsApp CS</a>
        </p>
    </div>
</body>
</html>
