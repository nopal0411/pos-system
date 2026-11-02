<?php
/**
 * LOGIN.PHP - Halaman Autentikasi
 * POS System Toko Fenny Grosir
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect(BASE_URL . '/modules/dashboard/index.php');
}

$error = '';

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Ambil data user dari database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verifikasi password menggunakan password_verify()
        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil - simpan ke session
            $_SESSION['user_id']  = $user['id_user'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            redirect(BASE_URL . '/modules/dashboard/index.php');
        } else {
            $error = 'Username atau password salah. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login POS System Toko Fenny Grosir">
    <title>Login – Toko Fenny Grosir POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <style>
        .login-bg-dots {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 30px 30px; pointer-events: none;
        }
        .info-box {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            padding: 14px 18px;
            color: rgba(255,255,255,0.8);
            font-size: 12.5px;
            margin-top: 28px;
        }
        .info-box code {
            background: rgba(255,255,255,0.15);
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 12px;
            color: #93c5fd;
        }
        .password-toggle {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; color: #9ca3af;
            cursor: pointer; font-size: 15px; padding: 0;
        }
        .password-toggle:hover { color: #1e3a5f; }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-bg-dots"></div>

    <div style="width:100%;max-width:420px;position:relative;z-index:1;">
        <!-- Login Card -->
        <div class="login-card">
            <!-- Logo -->
            <div class="login-logo">
                <div class="logo-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h4>Toko Fenny Grosir</h4>
                <p>Point of Sales System &bull; Masuk ke akun Anda</p>
            </div>

            <!-- Error Alert -->
            <?php if ($error): ?>
            <div class="alert alert-danger" style="font-size:13px;">
                <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="username">
                        <i class="fas fa-user" style="color:#2c5f9e;margin-right:6px;"></i>Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Masukkan username..."
                        value="<?= e($_POST['username'] ?? '') ?>"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>

                <div class="form-group" style="margin-bottom:24px;">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock" style="color:#2c5f9e;margin-right:6px;"></i>Password
                    </label>
                    <div style="position:relative;">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password..."
                            autocomplete="current-password"
                            required
                            style="padding-right:42px;"
                        >
                        <button type="button" class="password-toggle" id="togglePassword"
                                onclick="togglePwd()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg" id="loginBtn"
                        style="justify-content:center;">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <!-- Demo Credentials -->
        <div class="info-box">
            <div style="font-weight:600;margin-bottom:8px;color:white;">
                <i class="fas fa-info-circle" style="margin-right:6px;"></i>Akun Demo
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <div style="font-weight:600;color:#93c5fd;margin-bottom:4px;">Admin</div>
                    <div>User: <code>admin</code></div>
                    <div>Pass: <code>password</code></div>
                </div>
                <div>
                    <div style="font-weight:600;color:#6ee7b7;margin-bottom:4px;">Kasir</div>
                    <div>User: <code>kasir</code></div>
                    <div>Pass: <code>kasir123</code></div>
                </div>
            </div>
        </div>

        <p style="text-align:center;color:rgba(255,255,255,0.4);font-size:11.5px;margin-top:20px;">
            &copy; <?= date('Y') ?> Toko Fenny Grosir &bull; POS System for UMKM
        </p>
    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Loading state on submit
document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<span class="spinner"></span> Memproses...';
    btn.disabled = true;
});
</script>
</body>
</html>
