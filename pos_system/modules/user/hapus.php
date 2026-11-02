<?php
/**
 * USER/HAPUS.PHP - Proses Hapus User
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

// Tidak bisa hapus diri sendiri
if ($id > 0 && $id !== (int)$_SESSION['user_id']) {
    $user = getUserById($id);
    if ($user) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->execute([$id]);
        setFlash('success', "User '{$user['nama']}' berhasil dihapus.");
    } else {
        setFlash('danger', 'User tidak ditemukan.');
    }
} else {
    setFlash('danger', 'Tidak dapat menghapus akun sendiri.');
}
redirect(BASE_URL . '/modules/user/index.php');
