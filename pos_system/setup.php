<?php
/**
 * SETUP.PHP - Script Instalasi Otomatis
 * POS System Toko Fenny Grosir
 *
 * Jalankan file ini SEKALI untuk:
 * 1. Membuat database & tabel
 * 2. Mengisi data awal (seed)
 * 3. Membuat user admin dengan password terenkripsi
 *
 * Akses: http://localhost/SkripsiFibril/pos_system/setup.php
 */

// Konfigurasi database (tanpa nama database dulu)
$host    = 'localhost';
$user    = 'root';
$pass    = '';
$dbname  = 'pos_fenny';

$messages = [];
$errors   = [];
$success  = false;

if (isset($_POST['install'])) {
    try {
        // 1. Koneksi ke MySQL (tanpa pilih database)
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $messages[] = '✅ Koneksi ke MySQL berhasil.';

        // 2. Buat database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        $messages[] = '✅ Database <strong>pos_fenny</strong> berhasil dibuat/ditemukan.';

        // 3. Buat tabel-tabel
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id_user    INT AUTO_INCREMENT PRIMARY KEY,
                nama       VARCHAR(100) NOT NULL,
                username   VARCHAR(50) NOT NULL UNIQUE,
                password   VARCHAR(255) NOT NULL,
                role       ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS kategori (
                id_kategori   INT AUTO_INCREMENT PRIMARY KEY,
                nama_kategori VARCHAR(100) NOT NULL,
                created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS produk (
                id_produk   INT AUTO_INCREMENT PRIMARY KEY,
                id_kategori INT NOT NULL,
                nama_produk VARCHAR(150) NOT NULL,
                harga_beli  DECIMAL(15,2) NOT NULL DEFAULT 0,
                harga_jual  DECIMAL(15,2) NOT NULL DEFAULT 0,
                stok        INT NOT NULL DEFAULT 0,
                gambar      VARCHAR(255) DEFAULT NULL,
                deskripsi   TEXT DEFAULT NULL,
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS transaksi (
                id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
                no_transaksi VARCHAR(30) NOT NULL UNIQUE,
                tanggal      DATETIME NOT NULL,
                total_harga  DECIMAL(15,2) NOT NULL DEFAULT 0,
                uang_bayar   DECIMAL(15,2) NOT NULL DEFAULT 0,
                kembalian    DECIMAL(15,2) NOT NULL DEFAULT 0,
                id_user      INT NOT NULL,
                created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS detail_transaksi (
                id_detail    INT AUTO_INCREMENT PRIMARY KEY,
                id_transaksi INT NOT NULL,
                id_produk    INT NOT NULL,
                jumlah       INT NOT NULL DEFAULT 1,
                harga        DECIMAL(15,2) NOT NULL DEFAULT 0,
                subtotal     DECIMAL(15,2) NOT NULL DEFAULT 0,
                FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (id_produk)    REFERENCES produk(id_produk) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $messages[] = '✅ Semua tabel berhasil dibuat.';

        // 4. Seed Data Users (generate hash yang benar)
        $checkUser = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($checkUser == 0) {
            $passAdmin = password_hash('password', PASSWORD_DEFAULT);
            $passKasir = password_hash('kasir123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?,?,?,?), (?,?,?,?)")
                ->execute([
                    'Administrator', 'admin', $passAdmin, 'admin',
                    'Kasir Fenny',   'kasir', $passKasir, 'kasir'
                ]);
            $messages[] = '✅ User admin & kasir berhasil dibuat.';
        } else {
            $messages[] = '⚠️ Tabel users sudah ada data, dilewati.';
        }

        // 5. Seed Kategori
        $checkKat = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
        if ($checkKat == 0) {
            $pdo->exec("INSERT INTO kategori (nama_kategori) VALUES
                ('Sembako'),('Minuman'),('Makanan Ringan'),('Perlengkapan Rumah'),('Alat Tulis')");
            $messages[] = '✅ Data kategori (5 kategori) berhasil diisi.';
        } else {
            $messages[] = '⚠️ Kategori sudah ada data, dilewati.';
        }

        // 6. Seed Produk
        $checkProduk = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
        if ($checkProduk == 0) {
            $pdo->exec("INSERT INTO produk (id_kategori, nama_produk, harga_beli, harga_jual, stok, deskripsi) VALUES
                (1,'Beras Premium 5kg',62000,68000,50,'Beras putih kualitas premium, pulen dan wangi'),
                (1,'Minyak Goreng 2L',28000,32000,30,'Minyak goreng kelapa sawit murni 2 liter'),
                (1,'Gula Pasir 1kg',12500,14000,40,'Gula pasir putih bersih 1 kilogram'),
                (1,'Tepung Terigu 1kg',9000,11000,35,'Tepung terigu serbaguna'),
                (1,'Garam Dapur 500gr',3000,4000,60,'Garam dapur beryodium'),
                (2,'Aqua Galon',18000,22000,20,'Air mineral dalam kemasan galon 19L'),
                (2,'Teh Botol Sosro 450ml',4000,5500,48,'Minuman teh dalam kemasan botol plastik'),
                (2,'Kopi Kapal Api Sachet',1500,2000,100,'Kopi kapal api spesial sachet 25gr'),
                (2,'Susu Ultra 200ml',4500,6000,60,'Susu UHT ultra milk 200ml'),
                (2,'Pocari Sweat 500ml',6000,8000,36,'Minuman isotonik pocari sweat 500ml'),
                (3,'Chitato Rasa Sapi 68gr',8500,11000,24,'Keripik kentang chitato rasa sapi panggang'),
                (3,'Oreo Original 119gr',10000,13000,30,'Biskuit sandwich oreo original'),
                (3,'Indomie Goreng',2500,3500,120,'Mie instan goreng'),
                (3,'Supermi Ayam',2000,3000,80,'Mie instan supermi rasa ayam bawang'),
                (3,'Pilus Garuda 90gr',5000,7000,40,'Snack pilus garuda rasa pedas'),
                (4,'Sabun Lifebuoy 85gr',3500,5000,45,'Sabun mandi antibakteri lifebuoy'),
                (4,'Shampo Pantene 170ml',18000,22000,20,'Shampo pantene total damage care'),
                (4,'Detergen Rinso 800gr',18000,22000,25,'Detergen bubuk rinso anti noda'),
                (5,'Pulpen Ballpoint Standard',2500,4000,50,'Pulpen ballpoint standard 0.5mm'),
                (5,'Buku Tulis Sidu 38 Lembar',4000,6000,30,'Buku tulis sidu isi 38 lembar bergaris')
            ");
            $messages[] = '✅ Data produk (20 produk contoh) berhasil diisi.';
        } else {
            $messages[] = '⚠️ Produk sudah ada data, dilewati.';
        }

        // 7. Seed Transaksi Contoh
        $checkTrx = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
        if ($checkTrx == 0) {
            $pdo->exec("INSERT INTO transaksi (no_transaksi, tanggal, total_harga, uang_bayar, kembalian, id_user) VALUES
                ('TRX-20260501-001','2026-05-01 09:15:00',85500,100000,14500,2),
                ('TRX-20260501-002','2026-05-01 11:30:00',46000,50000,4000,2),
                ('TRX-20260510-001','2026-05-10 14:20:00',68000,70000,2000,2),
                ('TRX-20260515-001','2026-05-15 09:45:00',155000,200000,45000,2),
                ('TRX-20260520-001','2026-05-20 16:00:00',37500,40000,2500,2),
                ('TRX-20260525-001','2026-05-25 10:30:00',93000,100000,7000,2),
                ('TRX-20260531-001','2026-05-31 08:45:00',78000,80000,2000,2),
                ('TRX-20260531-002','2026-05-31 10:15:00',45500,50000,4500,2),
                ('TRX-20260531-003','2026-05-31 13:00:00',132000,150000,18000,2)
            ");

            $pdo->exec("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga, subtotal) VALUES
                (1,1,1,68000,68000),(1,8,5,2000,10000),(1,5,2,4000,8000),
                (2,2,1,32000,32000),(2,6,1,22000,22000),
                (3,17,1,22000,22000),(3,18,1,22000,22000),(3,3,1,14000,14000),
                (4,1,1,68000,68000),(4,9,3,6000,18000),(4,10,2,8000,16000),
                (4,11,2,11000,22000),(4,19,5,4000,20000),(4,20,2,6000,12000),
                (5,13,5,3500,17500),(5,14,5,3000,15000),
                (6,7,3,5500,16500),(6,12,2,13000,26000),(6,15,2,7000,14000),(6,6,2,22000,44000),
                (7,3,2,14000,28000),(7,4,1,11000,11000),(7,5,2,4000,8000),(7,8,4,2000,8000),
                (8,11,1,11000,11000),(8,12,1,13000,13000),(8,15,1,7000,7000),(8,19,2,4000,8000),(8,13,2,3500,7000),
                (9,17,1,22000,22000),(9,18,1,22000,22000),(9,2,1,32000,32000),(9,9,3,6000,18000),(9,16,2,5000,10000),(9,8,4,2000,8000)
            ");
            $messages[] = '✅ Data transaksi contoh berhasil diisi.';
        } else {
            $messages[] = '⚠️ Transaksi sudah ada data, dilewati.';
        }

        // 8. Buat folder upload jika belum ada
        $uploadPath = __DIR__ . '/assets/img/produk/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            $messages[] = '✅ Folder upload gambar berhasil dibuat.';
        }

        $success = true;
        $messages[] = '<strong style="color:#10b981;font-size:16px;">🎉 Instalasi selesai! Sistem siap digunakan.</strong>';

    } catch (PDOException $e) {
        $errors[] = '❌ Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup – POS System Toko Fenny Grosir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg,#0d1f3c,#1e3a5f); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .setup-card { background:white; border-radius:20px; padding:40px; max-width:560px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.3); }
        .step-item { padding:10px 14px; border-radius:8px; margin-bottom:8px; font-size:13.5px; background:#f9fafb; border-left:3px solid #10b981; }
        .step-item.error { border-color:#ef4444; background:#fef2f2; color:#991b1b; }
        code { background:#f3f4f6; padding:2px 8px; border-radius:5px; font-size:12px; color:#2c5f9e; }
    </style>
</head>
<body>
<div class="setup-card">
    <div style="text-align:center;margin-bottom:32px;">
        <div style="width:72px;height:72px;background:linear-gradient(135deg,#2c5f9e,#06b6d4);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(44,95,158,0.3);">
            <i class="fas fa-cash-register" style="font-size:30px;color:white;"></i>
        </div>
        <h3 style="font-weight:800;color:#0d1f3c;margin-bottom:6px;">Instalasi POS System</h3>
        <p style="color:#6b7280;font-size:13.5px;">Toko Fenny Grosir &bull; Setup Database & Data Awal</p>
    </div>

    <?php if (!empty($messages) || !empty($errors)): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="step-item"><?= $msg ?></div>
        <?php endforeach; ?>
        <?php foreach ($errors as $err): ?>
            <div class="step-item error"><?= $err ?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
        <div style="background:#d1fae5;border-radius:12px;padding:20px;margin-top:20px;text-align:center;">
            <div style="font-weight:700;color:#065f46;margin-bottom:12px;">Login Credentials:</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="background:white;border-radius:8px;padding:12px;">
                    <div style="font-weight:700;color:#1e40af;margin-bottom:6px;"><i class="fas fa-crown"></i> Admin</div>
                    <div style="font-size:12px;">User: <code>admin</code></div>
                    <div style="font-size:12px;">Pass: <code>password</code></div>
                </div>
                <div style="background:white;border-radius:8px;padding:12px;">
                    <div style="font-weight:700;color:#065f46;margin-bottom:6px;"><i class="fas fa-user"></i> Kasir</div>
                    <div style="font-size:12px;">User: <code>kasir</code></div>
                    <div style="font-size:12px;">Pass: <code>kasir123</code></div>
                </div>
            </div>
        </div>
        <a href="index.php" style="display:block;background:linear-gradient(135deg,#2c5f9e,#3a74b8);color:white;text-align:center;padding:14px;border-radius:12px;margin-top:20px;text-decoration:none;font-weight:700;font-size:15px;">
            <i class="fas fa-arrow-right" style="margin-right:8px;"></i>Masuk ke Sistem POS
        </a>
        <?php endif; ?>

    <?php else: ?>
    <div style="background:#f0f9ff;border-radius:12px;padding:18px;margin-bottom:24px;font-size:13.5px;color:#0369a1;">
        <i class="fas fa-info-circle" style="margin-right:8px;"></i>
        Script ini akan membuat database <code>pos_fenny</code>, membuat semua tabel, dan mengisi data contoh secara otomatis.
        <strong>Jalankan hanya sekali!</strong>
    </div>

    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Yang akan dilakukan:</div>
        <div class="step-item">🗄️ Membuat database <code>pos_fenny</code></div>
        <div class="step-item">📋 Membuat 5 tabel (users, kategori, produk, transaksi, detail_transaksi)</div>
        <div class="step-item">👤 Membuat user Admin & Kasir</div>
        <div class="step-item">📦 Mengisi 5 kategori & 20 produk contoh</div>
        <div class="step-item">🧾 Mengisi data transaksi contoh untuk demo</div>
    </div>

    <form method="POST">
        <button type="submit" name="install" value="1"
                style="width:100%;background:linear-gradient(135deg,#059669,#10b981);color:white;border:none;border-radius:12px;padding:16px;font-size:16px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;">
            <i class="fas fa-rocket" style="margin-right:10px;"></i>Mulai Instalasi Sekarang
        </button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
