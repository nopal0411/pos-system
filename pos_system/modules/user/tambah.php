<?php
/**
 * USER/TAMBAH.PHP - Proses Tambah User
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = in_array($_POST['role'], ['admin','kasir']) ? $_POST['role'] : 'kasir';

    if (empty($nama) || empty($username) || strlen($password) < 6) {
        setFlash('danger', 'Data tidak valid. Pastikan semua field terisi dan password minimal 6 karakter.');
    } else {
        // Cek username duplikat
        $check = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            setFlash('danger', "Username '$username' sudah digunakan.");
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?,?,?,?)");
            $stmt->execute([$nama, $username, $hash, $role]);
            setFlash('success', "User '$nama' berhasil ditambahkan.");
        }
    }
}
redirect(BASE_URL . '/modules/user/index.php');
