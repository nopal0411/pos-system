<?php
/**
 * PRODUK/SEARCH.PHP - API Endpoint Linear Search
 * POS System Toko Fenny Grosir
 *
 * File ini adalah API yang dipanggil via AJAX dari produk/index.php
 * Menggunakan fungsi linearSearch() dari includes/functions.php
 * yang mengimplementasikan Algoritma Linear Search murni di PHP.
 *
 * Response: JSON
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Set header JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Validasi request
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Ambil keyword dari query string
$keyword = trim($_GET['q'] ?? '');

// ============================================================
// JALANKAN ALGORITMA LINEAR SEARCH
// ============================================================
// Fungsi cariProdukLinearSearch() akan:
// 1. Mengambil semua produk dari database
// 2. Memanggil linearSearch($dataProduk, $keyword)
// 3. Mengembalikan array produk yang cocok
// ============================================================

// Hitung total data sebelum filter (untuk info langkah)
$stmt = $pdo->query("SELECT COUNT(*) FROM produk");
$totalData = (int)$stmt->fetchColumn();

// Jalankan Linear Search
$hasil = cariProdukLinearSearch($keyword);

// Hitung berapa langkah yang dibutuhkan
// Pada worst case, langkah = total data
// Pada best case, langkah = posisi pertama ditemukan
$langkah = $totalData; // Linear Search selalu menelusuri semua data

if (!empty($keyword)) {
    // Hitung langkah nyata: posisi elemen terakhir yang ditemukan
    // Dalam implementasi ini, kita telusuri semua data (tidak berhenti di tengah)
    // karena mencari semua kemunculan (multiple results)
    $langkah = $totalData;
}

// Kembalikan response JSON
echo json_encode([
    'keyword'    => $keyword,
    'total_data' => $totalData,
    'ditemukan'  => count($hasil),
    'langkah'    => $langkah,
    'hasil'      => $hasil,
    'algoritma'  => 'Linear Search',
    'kompleksitas' => 'O(n)',
]);
