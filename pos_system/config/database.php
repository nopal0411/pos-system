<?php
/**
 * ============================================================
 * KONFIGURASI DATABASE - POS System Toko Fenny Grosir
 * ============================================================
 * File ini berisi konfigurasi koneksi ke database MySQL
 * menggunakan PDO (PHP Data Objects) untuk keamanan dan
 * kemudahan penggunaan prepared statements.
 * ============================================================
 */

// Konfigurasi koneksi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pos_fenny');
define('DB_CHARSET', 'utf8mb4');

/**
 * Membuat koneksi PDO ke database MySQL
 * PDO digunakan untuk mencegah SQL Injection melalui
 * prepared statements.
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Tampilkan pesan error yang informatif
            die('<div style="font-family:Arial;background:#fff3cd;color:#856404;padding:20px;margin:20px;border-radius:8px;border:1px solid #ffc107;">
                <h3>⚠️ Koneksi Database Gagal</h3>
                <p>Pastikan Laragon sudah berjalan dan database <strong>pos_fenny</strong> sudah diimport.</p>
                <p><small>Error: ' . htmlspecialchars($e->getMessage()) . '</small></p>
                <p><a href="../database/pos_fenny.sql" style="color:#0066cc;">Lihat file SQL untuk diimport</a></p>
            </div>');
        }
    }
    
    return $pdo;
}

// Buat koneksi global
$pdo = getDB();

/**
 * Konfigurasi Aplikasi
 */
define('APP_NAME', 'Toko Fenny Grosir');
define('APP_VERSION', '1.0.0');

// Deteksi BASE_URL otomatis berdasarkan environment
// Mendukung: Laragon (localhost:80), PHP Built-in Server (localhost:8080), dll.
$_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_script   = $_SERVER['SCRIPT_NAME'] ?? '';

// Cari root pos_system/ dari path saat ini
$_pos = strpos($_script, '/pos_system/');
if ($_pos !== false) {
    $_basePath = substr($_script, 0, $_pos) . '/pos_system';
} elseif (strpos($_script, '/pos_system') !== false) {
    $_basePath = '/pos_system';
} else {
    // PHP built-in server: root langsung di pos_system/
    $_basePath = '';
}

define('BASE_URL', $_protocol . '://' . $_host . $_basePath);
unset($_protocol, $_host, $_script, $_pos, $_basePath);

define('UPLOAD_PATH', __DIR__ . '/../assets/img/produk/');
define('UPLOAD_URL', BASE_URL . '/assets/img/produk/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
