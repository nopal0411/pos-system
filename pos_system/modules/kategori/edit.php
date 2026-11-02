<?php
/**
 * KATEGORI/EDIT.PHP - Proses Edit Kategori
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id_kategori'] ?? 0);
    $nama = trim($_POST['nama_kategori'] ?? '');
    if ($id > 0 && !empty($nama)) {
        $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
        $stmt->execute([$nama, $id]);
        setFlash('success', "Kategori berhasil diperbarui.");
    } else {
        setFlash('danger', 'Data tidak valid.');
    }
}
redirect(BASE_URL . '/modules/kategori/index.php');
