<?php
/**
 * PRODUK/HAPUS.PHP - Proses Hapus Produk
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $produk = getProdukById($id);
    if ($produk) {
        // Hapus gambar dari server jika ada
        if ($produk['gambar'] && file_exists(UPLOAD_PATH . $produk['gambar'])) {
            unlink(UPLOAD_PATH . $produk['gambar']);
        }
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->execute([$id]);
        setFlash('success', "Produk '{$produk['nama_produk']}' berhasil dihapus.");
    } else {
        setFlash('danger', 'Produk tidak ditemukan.');
    }
}
redirect(BASE_URL . '/modules/produk/index.php');
