<?php
/**
 * PROFIL/INDEX.PHP - Halaman Edit Profil User
 * POS System Toko Fenny Grosir
 * Dapat diakses oleh semua user yang sudah login (admin maupun kasir)
 */
$pageTitle    = 'Edit Profil';
$pageSubtitle = 'Kelola informasi akun Anda';
require_once __DIR__ . '/../../includes/header.php';

// Ambil data user yang sedang login dari database
$userId  = $_SESSION['user_id'];
$user    = getUserById($userId);
?>

<div class="page-header">
    <h1><i class="fas fa-user-circle" style="color:var(--primary-500);margin-right:10px;"></i>Edit Profil</h1>
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a>
        <span style="margin:0 6px;color:var(--gray-300);">/</span>
        Edit Profil
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="card" style="border-radius:var(--radius-xl);overflow:hidden;">
            <!-- Card Header dengan avatar besar -->
            <div style="background:linear-gradient(135deg,var(--primary-700),var(--primary-500));padding:32px 28px;text-align:center;">
                <div style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:white;margin:0 auto 12px;border:3px solid rgba(255,255,255,0.35);box-shadow:0 4px 16px rgba(0,0,0,0.2);">
                    <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                </div>
                <div style="color:white;font-size:18px;font-weight:700;"><?= e($user['nama'] ?? '') ?></div>
                <div style="color:rgba(255,255,255,0.7);font-size:12px;margin-top:4px;text-transform:uppercase;letter-spacing:0.8px;">
                    <span style="background:rgba(255,255,255,0.15);padding:3px 12px;border-radius:20px;">
                        <?= ucfirst($user['role'] ?? 'kasir') ?>
                    </span>
                </div>
            </div>

            <!-- Form Edit Profil -->
            <div class="card-body" style="padding:28px;">
                <form action="<?= BASE_URL ?>/modules/profil/simpan.php" method="POST" id="formEditProfil">

                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label class="form-label" for="nama">
                            <i class="fas fa-user" style="color:var(--primary-400);margin-right:6px;"></i>
                            Nama Lengkap <span style="color:var(--accent-red);">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= e($user['nama'] ?? '') ?>"
                               placeholder="Masukkan nama lengkap" required autocomplete="name">
                    </div>

                    <!-- Username -->
                    <div class="form-group">
                        <label class="form-label" for="username">
                            <i class="fas fa-at" style="color:var(--primary-400);margin-right:6px;"></i>
                            Username <span style="color:var(--accent-red);">*</span>
                        </label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($user['username'] ?? '') ?>"
                               placeholder="Masukkan username" required autocomplete="username">
                    </div>

                    <!-- Divider -->
                    <div style="border-top:1px dashed var(--gray-200);margin:24px 0 20px;position:relative;">
                        <span style="position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:white;padding:0 12px;font-size:11.5px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Ganti Password (opsional)
                        </span>
                    </div>

                    <!-- Password Baru -->
                    <div class="form-group">
                        <label class="form-label" for="password_baru">
                            <i class="fas fa-lock" style="color:var(--primary-400);margin-right:6px;"></i>
                            Password Baru
                        </label>
                        <div style="position:relative;">
                            <input type="password" class="form-control" id="password_baru" name="password_baru"
                                   placeholder="Kosongkan jika tidak ingin ganti password"
                                   autocomplete="new-password" style="padding-right:44px;">
                            <button type="button" onclick="togglePassword('password_baru', this)"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:14px;padding:0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small style="color:var(--gray-400);font-size:11.5px;margin-top:4px;display:block;">
                            Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.
                        </small>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label class="form-label" for="konfirmasi_password">
                            <i class="fas fa-lock-open" style="color:var(--primary-400);margin-right:6px;"></i>
                            Konfirmasi Password Baru
                        </label>
                        <div style="position:relative;">
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password"
                                   placeholder="Ulangi password baru"
                                   autocomplete="new-password" style="padding-right:44px;">
                            <button type="button" onclick="togglePassword('konfirmasi_password', this)"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:14px;padding:0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div style="display:flex;gap:12px;margin-top:28px;">
                        <button type="submit" class="btn btn-primary" id="btnSimpan" style="flex:1;">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraScript = '
<script>
// Toggle show/hide password
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector("i");
    if (field.type === "password") {
        field.type = "text";
        icon.className = "fas fa-eye-slash";
    } else {
        field.type = "password";
        icon.className = "fas fa-eye";
    }
}

// Validasi form sebelum submit
document.getElementById("formEditProfil").addEventListener("submit", function(e) {
    const nama     = document.getElementById("nama").value.trim();
    const username = document.getElementById("username").value.trim();
    const pass     = document.getElementById("password_baru").value;
    const konfirm  = document.getElementById("konfirmasi_password").value;

    if (!nama || !username) {
        e.preventDefault();
        alert("Nama dan Username tidak boleh kosong.");
        return;
    }

    if (pass.length > 0 && pass.length < 6) {
        e.preventDefault();
        alert("Password minimal 6 karakter.");
        return;
    }

    if (pass !== konfirm) {
        e.preventDefault();
        alert("Konfirmasi password tidak cocok.");
        document.getElementById("konfirmasi_password").focus();
        return;
    }

    // Tampilkan loading state
    const btn = document.getElementById("btnSimpan");
    btn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> Menyimpan...\';
    btn.disabled = true;
});
</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
