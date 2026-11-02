<?php
/**
 * PROFIL/SIMPAN.PHP - Handler simpan perubahan profil
 * POS System Toko Fenny Grosir
 * Dapat diakses oleh semua user yang sudah login
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/modules/profil/index.php');
}

$userId   = (int)$_SESSION['user_id'];
$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$passBaru = $_POST['password_baru'] ?? '';
$konfirm  = $_POST['konfirmasi_password'] ?? '';

// Validasi input dasar
if (empty($nama) || empty($username)) {
    setFlash('danger', 'Nama dan Username tidak boleh kosong.');
    redirect(BASE_URL . '/modules/profil/index.php');
}

// Validasi panjang password jika diisi
if (!empty($passBaru)) {
    if (strlen($passBaru) < 6) {
        setFlash('danger', 'Password minimal 6 karakter.');
        redirect(BASE_URL . '/modules/profil/index.php');
    }
    if ($passBaru !== $konfirm) {
        setFlash('danger', 'Konfirmasi password tidak cocok.');
        redirect(BASE_URL . '/modules/profil/index.php');
    }
}

// Cek apakah username sudah dipakai user lain
$stmtCek = $pdo->prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ?");
$stmtCek->execute([$username, $userId]);
if ($stmtCek->fetch()) {
    setFlash('danger', "Username '$username' sudah digunakan oleh akun lain.");
    redirect(BASE_URL . '/modules/profil/index.php');
}

// Simpan ke database
if (!empty($passBaru)) {
    $hash = password_hash($passBaru, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET nama=?, username=?, password=? WHERE id_user=?");
    $stmt->execute([$nama, $username, $hash, $userId]);
} else {
    $stmt = $pdo->prepare("UPDATE users SET nama=?, username=? WHERE id_user=?");
    $stmt->execute([$nama, $username, $userId]);
}

// Update session supaya nama di header langsung berubah
$_SESSION['nama']     = $nama;
$_SESSION['username'] = $username;

setFlash('success', 'Profil berhasil diperbarui.');
redirect(BASE_URL . '/modules/profil/index.php');
?>
