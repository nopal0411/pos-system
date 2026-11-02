<?php
/**
 * ============================================================
 * FUNCTIONS.PHP - Helper Functions & Algoritma Linear Search
 * POS System Toko Fenny Grosir
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

// ============================================================
// FUNGSI AUTENTIKASI & SESSION
// ============================================================

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect ke halaman lain
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Cek login - jika belum login, redirect ke halaman login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/modules/auth/login.php');
    }
}

/**
 * Cek apakah user adalah admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Hanya admin yang bisa akses
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect(BASE_URL . '/modules/dashboard/index.php');
    }
}

// ============================================================
// FUNGSI FORMAT
// ============================================================

/**
 * Format angka menjadi format Rupiah Indonesia
 * Contoh: 25000 -> Rp 25.000
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Format tanggal dari MySQL ke format Indonesia
 * Contoh: 2026-05-31 -> 31 Mei 2026
 */
function formatTanggal($tanggal) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober', '11' => 'November',  '12' => 'Desember'
    ];
    $date = new DateTime($tanggal);
    return $date->format('d') . ' ' . $bulan[$date->format('m')] . ' ' . $date->format('Y');
}

/**
 * Generate nomor transaksi unik
 * Format: TRX-YYYYMMDD-XXX
 */
function generateNoTransaksi() {
    global $pdo;
    $tanggal = date('Ymd');
    $prefix = 'TRX-' . $tanggal . '-';
    
    // Cari nomor urut terakhir untuk hari ini
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()");
    $stmt->execute();
    $row = $stmt->fetch();
    $urut = str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);
    
    return $prefix . $urut;
}

/**
 * Escape output untuk mencegah XSS
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message (pesan sementara)
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Tampilkan flash message dan hapus dari session
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ============================================================
// ============================================================
// ALGORITMA LINEAR SEARCH
// ============================================================
// ============================================================
/**
 * Algoritma Linear Search adalah algoritma pencarian paling
 * sederhana yang bekerja dengan cara memeriksa setiap elemen
 * dalam array/list satu per satu dari awal hingga akhir,
 * sampai elemen yang dicari ditemukan atau seluruh data
 * telah diperiksa.
 *
 * Kompleksitas Waktu:
 *   - Best Case  : O(1) - elemen ditemukan di posisi pertama
 *   - Average    : O(n/2) - rata-rata setengah dari data
 *   - Worst Case : O(n) - elemen di posisi terakhir atau tidak ada
 *
 * Kompleksitas Ruang: O(1) - tidak memerlukan memori tambahan
 *
 * Keunggulan Linear Search:
 *   - Sederhana dan mudah diimplementasikan
 *   - Tidak memerlukan data yang sudah terurut (unsorted)
 *   - Cocok untuk data dalam jumlah kecil hingga menengah
 *   - Bisa mencari berdasarkan kriteria yang fleksibel
 * ============================================================
 */

/**
 * FUNGSI UTAMA: Linear Search pada Array Data Produk
 *
 * Cara Kerja:
 * 1. Terima array data produk dan kata kunci pencarian
 * 2. Iterasi setiap elemen dalam array dari index 0 hingga n-1
 * 3. Pada setiap iterasi, periksa apakah kata kunci ada dalam
 *    nama produk (case-insensitive)
 * 4. Jika ditemukan, tambahkan ke array hasil
 * 5. Kembalikan semua produk yang cocok
 *
 * @param array  $dataProduk  Array berisi semua data produk
 * @param string $keyword     Kata kunci yang dicari
 * @return array              Array produk yang sesuai dengan keyword
 */
function linearSearch(array $dataProduk, string $keyword): array {
    // Inisialisasi array untuk menyimpan hasil pencarian
    $hasilPencarian = [];
    
    // Normalisasi keyword: ubah ke huruf kecil untuk
    // pencarian yang tidak case-sensitive
    $keywordLower = strtolower(trim($keyword));
    
    // Jika keyword kosong, kembalikan semua data
    if (empty($keywordLower)) {
        return $dataProduk;
    }
    
    // =====================================================
    // PROSES LINEAR SEARCH
    // Iterasi setiap elemen array satu per satu (O(n))
    // =====================================================
    for ($i = 0; $i < count($dataProduk); $i++) {
        // Ambil produk pada posisi ke-i
        $produk = $dataProduk[$i];
        
        // Normalisasi nama produk ke huruf kecil
        $namaProdukLower = strtolower($produk['nama_produk']);
        
        // PERBANDINGAN: Cek apakah keyword terdapat
        // dalam nama produk menggunakan strpos()
        // strpos() mengembalikan posisi (int) jika ditemukan,
        // atau false jika tidak ditemukan
        if (strpos($namaProdukLower, $keywordLower) !== false) {
            // ✅ DITEMUKAN: Tambahkan produk ke hasil pencarian
            $hasilPencarian[] = $produk;
        }
        // ❌ TIDAK DITEMUKAN: Lanjut ke elemen berikutnya
    }
    
    // Kembalikan array berisi semua produk yang cocok
    return $hasilPencarian;
}

/**
 * FUNGSI: Ambil semua produk dari database lalu lakukan
 * Linear Search berdasarkan keyword
 *
 * Langkah-langkah:
 * 1. Query semua produk dari database
 * 2. Simpan ke dalam array PHP
 * 3. Panggil fungsi linearSearch() untuk pencarian
 * 4. Kembalikan hasil pencarian
 *
 * @param string $keyword  Kata kunci pencarian
 * @return array           Hasil pencarian produk
 */
function cariProdukLinearSearch(string $keyword): array {
    global $pdo;
    
    // Langkah 1: Ambil SEMUA data produk dari database
    // (Linear Search dilakukan di sisi PHP, bukan SQL LIKE)
    $stmt = $pdo->query("
        SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        ORDER BY p.nama_produk ASC
    ");
    
    // Langkah 2: Simpan semua data ke dalam array PHP
    $semuaProduk = $stmt->fetchAll();
    
    // Langkah 3: Jalankan Algoritma Linear Search
    // pada array yang sudah diambil dari database
    $hasilPencarian = linearSearch($semuaProduk, $keyword);
    
    // Langkah 4: Kembalikan hasil
    return $hasilPencarian;
}

// ============================================================
// FUNGSI DATABASE - USERS
// ============================================================

function getAllUsers() {
    global $pdo;
    return $pdo->query("SELECT * FROM users ORDER BY id_user")->fetchAll();
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================================
// FUNGSI DATABASE - KATEGORI
// ============================================================

function getAllKategori() {
    global $pdo;
    return $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
}

function getKategoriById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function countKategori() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
}

// ============================================================
// FUNGSI DATABASE - PRODUK
// ============================================================

function getAllProduk() {
    global $pdo;
    return $pdo->query("
        SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        ORDER BY p.nama_produk ASC
    ")->fetchAll();
}

function getProdukById($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_produk = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function countProduk() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
}

function getProdukAktif() {
    global $pdo;
    return $pdo->query("
        SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.stok > 0
        ORDER BY p.nama_produk ASC
    ")->fetchAll();
}

// ============================================================
// FUNGSI DATABASE - TRANSAKSI & DASHBOARD
// ============================================================

function countTransaksiHariIni() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM transaksi WHERE DATE(tanggal) = CURDATE()")->fetchColumn();
}

function getTotalPendapatanHariIni() {
    global $pdo;
    return $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM transaksi WHERE DATE(tanggal) = CURDATE()")->fetchColumn();
}

function getTotalPendapatanBulanIni() {
    global $pdo;
    return $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM transaksi WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())")->fetchColumn();
}

function getGrafikPenjualan7Hari() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT DATE(tanggal) as tgl, COALESCE(SUM(total_harga), 0) as total
        FROM transaksi
        WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(tanggal)
        ORDER BY tgl ASC
    ");
    return $stmt->fetchAll();
}

function getGrafikPenjualan30Hari() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT DATE(tanggal) as tgl, COALESCE(SUM(total_harga), 0) as total
        FROM transaksi
        WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
        GROUP BY DATE(tanggal)
        ORDER BY tgl ASC
    ");
    return $stmt->fetchAll();
}

function getProdukTerlaris($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.nama_produk, SUM(dt.jumlah) as total_terjual, SUM(dt.subtotal) as total_pendapatan
        FROM detail_transaksi dt
        JOIN produk p ON dt.id_produk = p.id_produk
        GROUP BY dt.id_produk
        ORDER BY total_terjual DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getTransaksiTerbaru($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir
        FROM transaksi t
        JOIN users u ON t.id_user = u.id_user
        ORDER BY t.tanggal DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getDetailTransaksi($id_transaksi) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT dt.*, p.nama_produk, p.gambar
        FROM detail_transaksi dt
        JOIN produk p ON dt.id_produk = p.id_produk
        WHERE dt.id_transaksi = ?
    ");
    $stmt->execute([$id_transaksi]);
    return $stmt->fetchAll();
}

function getTransaksiById($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir
        FROM transaksi t
        JOIN users u ON t.id_user = u.id_user
        WHERE t.id_transaksi = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================================
// FUNGSI UPLOAD GAMBAR
// ============================================================

/**
 * Upload gambar produk ke server
 * @param array $file  Array $_FILES['gambar']
 * @return string|false  Nama file jika sukses, false jika gagal
 */
function uploadGambar($file) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $namaFile = 'produk_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $targetPath = UPLOAD_PATH . $namaFile;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $namaFile;
    }
    
    return false;
}

/**
 * Mendapatkan nilai pengaturan dari database
 */
function get_pengaturan($kunci, $default = "nonaktif") {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT nilai FROM pengaturan WHERE kunci = ?");
        $stmt->execute([$kunci]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    } catch(PDOException $e) {
        return $default;
    }
}
