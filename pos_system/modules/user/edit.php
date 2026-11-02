<?php
/**
 * USER/EDIT.PHP - Proses Edit User
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id_user'] ?? 0);
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = in_array($_POST['role'], ['admin','kasir']) ? $_POST['role'] : 'kasir';

    if ($id > 0 && !empty($nama) && !empty($username)) {
        if (!empty($password)) {
            // Update dengan password baru
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET nama=?, username=?, password=?, role=? WHERE id_user=?");
            $stmt->execute([$nama, $username, $hash, $role, $id]);
        } else {
            // Update tanpa ganti password
            $stmt = $pdo->prepare("UPDATE users SET nama=?, username=?, role=? WHERE id_user=?");
            $stmt->execute([$nama, $username, $role, $id]);
        }
        setFlash('success', "User '$nama' berhasil diperbarui.");
    } else {
        setFlash('danger', 'Data tidak valid.');
    }
}
redirect(BASE_URL . '/modules/user/index.php');
