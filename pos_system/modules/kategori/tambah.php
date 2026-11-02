<?php
/**
 * KATEGORI/TAMBAH.PHP - Proses Tambah Kategori
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kategori'] ?? '');
    if (!empty($nama)) {
        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        $stmt->execute([$nama]);
        setFlash('success', "Kategori '$nama' berhasil ditambahkan.");
    } else {
        setFlash('danger', 'Nama kategori tidak boleh kosong.');
    }
}
redirect(BASE_URL . '/modules/kategori/index.php');
