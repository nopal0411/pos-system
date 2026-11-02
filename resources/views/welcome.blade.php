<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Toko Fenny Grosir – Sistem Point of Sales berbasis Algoritma Linear Search">
    <title>Toko Fenny Grosir – POS System</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:   #1e3a5f;
            --accent:    #2c7be5;
            --success:   #10b981;
            --warning:   #f59e0b;
            --danger:    #ef4444;
            --bg:        #0f172a;
            --card:      #1e293b;
            --border:    rgba(255,255,255,0.08);
            --text:      #f1f5f9;
            --muted:     #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(44, 123, 229, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(16, 185, 129, 0.06) 0%, transparent 60%);
        }

        /* ---- HERO ---- */
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }
        .hero-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; color: white;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 40px rgba(44, 123, 229, 0.4);
            animation: pulse-glow 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 40px rgba(44,123,229,0.4); }
            50%       { box-shadow: 0 0 60px rgba(44,123,229,0.7); }
        }
        .hero h1 {
            font-size: 2.2rem; font-weight: 800;
            background: linear-gradient(135deg, #fff, #93c5fd);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        .hero p {
            color: var(--muted); font-size: 1rem; max-width: 500px; margin: 0 auto;
        }
        .badge-version {
            display: inline-block;
            background: rgba(44,123,229,0.15);
            border: 1px solid rgba(44,123,229,0.3);
            color: #93c5fd; font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 20px;
            margin-bottom: 1rem; letter-spacing: 0.05em; text-transform: uppercase;
        }

        /* ---- CARDS ---- */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            width: 100%; max-width: 900px;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.75rem;
            transition: transform .2s, border-color .2s, box-shadow .2s;
            position: relative; overflow: hidden;
        }
        .card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--success));
        }
        .card:hover {
            transform: translateY(-4px);
            border-color: rgba(44,123,229,0.3);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .card-icon {
            width: 44px; height: 44px;
            background: rgba(44,123,229,0.12);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: var(--accent);
            margin-bottom: 1rem;
        }
        .card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 0.4rem; }
        .card p  { font-size: 0.83rem; color: var(--muted); line-height: 1.6; margin-bottom: 1.25rem; }

        /* ---- BUTTONS ---- */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 10px;
            font-size: 13.5px; font-weight: 600; text-decoration: none;
            transition: all .2s; cursor: pointer; border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            color: white;
        }
        .btn-primary:hover { filter: brightness(1.15); transform: translateY(-1px); }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        /* ---- CREDENTIALS TABLE ---- */
        .cred-table {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            width: 100%; max-width: 900px;
            margin-bottom: 2rem;
        }
        .cred-table h4 {
            font-size: 0.85rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 1rem;
        }
        .cred-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .cred-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px; padding: 1rem;
        }
        .cred-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
        .cred-role  { font-size: 13px; font-weight: 700; color: var(--accent); margin-bottom: 6px; }
        .cred-row   { font-size: 12.5px; color: var(--muted); margin-bottom: 3px; }
        .cred-row code {
            background: rgba(44,123,229,0.12); color: #93c5fd;
            padding: 2px 8px; border-radius: 5px; font-size: 12px;
        }

        /* ---- STEPS ---- */
        .steps {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            width: 100%; max-width: 900px;
            margin-bottom: 2rem;
        }
        .steps h4 {
            font-size: 0.85rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 1.25rem;
        }
        .step {
            display: flex; align-items: flex-start; gap: 1rem;
            margin-bottom: 1rem;
        }
        .step:last-child { margin-bottom: 0; }
        .step-num {
            width: 28px; height: 28px; min-width: 28px;
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: white;
        }
        .step-content h5 { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
        .step-content p  { font-size: 12px; color: var(--muted); }
        .step-content code {
            background: rgba(255,255,255,0.06); color: #93c5fd;
            padding: 2px 7px; border-radius: 4px; font-size: 11.5px;
        }

        /* ---- FOOTER ---- */
        footer {
            text-align: center; color: var(--muted); font-size: 12px; margin-top: 1rem;
        }
        footer strong { color: var(--text); }

        /* ---- STATUS DOT ---- */
        .status-row { display: flex; align-items: center; gap: 8px; margin-bottom: 0.5rem; font-size: 13px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .dot-green  { background: var(--success); box-shadow: 0 0 8px var(--success); }
        .dot-yellow { background: var(--warning); box-shadow: 0 0 8px var(--warning); }

        @media (max-width: 600px) {
            .hero h1 { font-size: 1.6rem; }
            .cred-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- HERO -->
    <div class="hero">
        <div class="hero-icon"><i class="fas fa-cash-register"></i></div>
        <div class="badge-version">Laravel {{ app()->version() }} + PHP POS</div>
        <h1>Toko Fenny Grosir</h1>
        <p>Sistem Point of Sales berbasis Algoritma Linear Search — Skripsi 2026</p>
    </div>

    <!-- MAIN CARDS -->
    <div class="cards">
        <!-- POS App -->
        <div class="card">
            <div class="card-icon"><i class="fas fa-store"></i></div>
            <h3>Buka Aplikasi POS</h3>
            <p>Akses sistem kasir lengkap: dashboard, transaksi, produk, laporan, dan manajemen user.</p>
            <a href="/SkripsiFibril/pos_system/" class="btn btn-primary" id="btn-pos-laragon">
                <i class="fas fa-arrow-right"></i> Buka via Laragon
            </a>
        </div>

        <!-- Database Status -->
        <div class="card">
            <div class="card-icon" style="background:rgba(16,185,129,0.12); color:var(--success);"><i class="fas fa-database"></i></div>
            <h3>Status Database</h3>
            <p>Konfigurasi koneksi MySQL untuk database <strong>pos_fenny</strong>.</p>
            <div class="status-row">
                <span class="dot dot-green"></span> Host: <code style="font-size:12px;background:rgba(255,255,255,0.06);padding:1px 6px;border-radius:4px;color:#93c5fd;">127.0.0.1:3306</code>
            </div>
            <div class="status-row">
                <span class="dot dot-green"></span> Database: <code style="font-size:12px;background:rgba(255,255,255,0.06);padding:1px 6px;border-radius:4px;color:#93c5fd;">pos_fenny</code>
            </div>
            <div class="status-row">
                <span class="dot dot-yellow"></span> User: <code style="font-size:12px;background:rgba(255,255,255,0.06);padding:1px 6px;border-radius:4px;color:#93c5fd;">root</code>
            </div>
        </div>

        <!-- Tech Stack -->
        <div class="card">
            <div class="card-icon" style="background:rgba(245,158,11,0.12); color:var(--warning);"><i class="fas fa-code"></i></div>
            <h3>Teknologi</h3>
            <p>Proyek skripsi yang mengimplementasikan algoritma Linear Search pada sistem POS.</p>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:auto;">
                @foreach(['PHP 8+', 'Laravel 13', 'MySQL', 'Bootstrap 5', 'Linear Search'] as $tech)
                    <span style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);padding:3px 10px;border-radius:20px;font-size:11px;color:var(--muted);">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CREDENTIALS -->
    <div class="cred-table">
        <h4><i class="fas fa-key" style="margin-right:6px;"></i> Akun Login Demo</h4>
        <div class="cred-grid">
            <div class="cred-item">
                <div class="cred-label">Role</div>
                <div class="cred-role" style="color:#93c5fd;">&#128272; Admin</div>
                <div class="cred-row">Username: <code>admin</code></div>
                <div class="cred-row">Password: <code>password</code></div>
                <div class="cred-row" style="margin-top:6px;font-size:11.5px;color:#64748b;">Akses penuh semua fitur</div>
            </div>
            <div class="cred-item">
                <div class="cred-label">Role</div>
                <div class="cred-role" style="color:#6ee7b7;">&#128104;&#8205;&#128188; Kasir</div>
                <div class="cred-row">Username: <code>kasir</code></div>
                <div class="cred-row">Password: <code>kasir123</code></div>
                <div class="cred-row" style="margin-top:6px;font-size:11.5px;color:#64748b;">Akses transaksi & dashboard</div>
            </div>
        </div>
    </div>

    <!-- SETUP STEPS -->
    <div class="steps">
        <h4><i class="fas fa-list-check" style="margin-right:6px;"></i> Langkah Setup (jika belum dilakukan)</h4>

        <div class="step">
            <div class="step-num">1</div>
            <div class="step-content">
                <h5>Pastikan Laragon berjalan</h5>
                <p>Apache dan MySQL harus aktif di Laragon.</p>
            </div>
        </div>

        <div class="step">
            <div class="step-num">2</div>
            <div class="step-content">
                <h5>Import database</h5>
                <p>Buka phpMyAdmin → Import file <code>pos_system/database/pos_fenny.sql</code></p>
            </div>
        </div>

        <div class="step">
            <div class="step-num">3</div>
            <div class="step-content">
                <h5>Akses POS via Laragon web server</h5>
                <p>Buka browser ke <code>localhost/SkripsiFibril/pos_system/</code></p>
            </div>
        </div>

        <div class="step">
            <div class="step-num">4</div>
            <div class="step-content">
                <h5>Login dengan akun demo</h5>
                <p>Gunakan kredensial di atas untuk masuk ke sistem.</p>
            </div>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} <strong>Toko Fenny Grosir POS System</strong> &bull;
        Skripsi &bull; Laravel {{ app()->version() }} &bull;
        PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}
    </footer>

</body>
</html>
