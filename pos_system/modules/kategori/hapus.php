<?php
/**
 * KATEGORI/HAPUS.PHP - Proses Hapus Kategori
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // Cek apakah kategori masih dipakai produk
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_kategori = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        setFlash('danger', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
    } else {
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Kategori berhasil dihapus.');
    }
}
redirect(BASE_URL . '/modules/kategori/index.php');
