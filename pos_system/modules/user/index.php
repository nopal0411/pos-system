<?php
/**
 * USER/INDEX.PHP - Manajemen User (Admin Only)
 */
$pageTitle    = 'Manajemen User';
$pageSubtitle = 'Kelola akun pengguna sistem';
require_once __DIR__ . '/../../includes/header.php';
requireAdmin();

$userList = getAllUsers();
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-1"><i class="fas fa-users-cog text-primary me-2"></i>Manajemen User</h1>
        <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a> / <span>User</span>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="fas fa-user-plus"></i> Tambah User
    </button>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-users"></i> Daftar User</div>
        <span class="badge badge-primary"><?= count($userList) ?> User</span>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tabelUser" class="table" style="width:100%">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($userList as $i => $u): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;background:<?= $u['role']==='admin' ? '#dbeafe' : '#d1fae5' ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:<?= $u['role']==='admin' ? '#1e40af' : '#065f46' ?>;">
                                    <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                </div>
                                <span style="font-weight:600;"><?= e($u['nama']) ?></span>
                            </div>
                        </td>
                        <td><code style="background:#f3f4f6;padding:3px 8px;border-radius:5px;"><?= e($u['username']) ?></code></td>
                        <td>
                            <span class="badge <?= $u['role']==='admin' ? 'badge-primary' : 'badge-success' ?>">
                                <i class="fas <?= $u['role']==='admin' ? 'fa-crown' : 'fa-user' ?> me-1"></i>
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td style="font-size:12.5px;color:#6b7280;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td style="text-align:center;white-space:nowrap;">
                            <?php if ($u['id_user'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-sm btn-warning"
                                    onclick="editUser(<?= $u['id_user'] ?>, '<?= e($u['nama']) ?>', '<?= e($u['username']) ?>', '<?= $u['role'] ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="hapus.php?id=<?= $u['id_user'] ?>"
                               class="btn btn-sm btn-danger"
                               data-confirm="Hapus user '<?= e($u['nama']) ?>'?">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php else: ?>
                            <span class="badge badge-gray">Akun Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="tambah.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama lengkap..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" placeholder="Username unik..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter..." required minlength="6">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-control" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="edit.php">
                <input type="hidden" name="id_user" id="edit_user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" id="edit_user_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" id="edit_user_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru <small style="color:#9ca3af;">(kosongkan jika tidak diubah)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Password baru...">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Role *</label>
                        <select name="role" id="edit_user_role" class="form-control" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraScript = '
<script>
$(document).ready(function(){ initDataTable("#tabelUser"); });

function editUser(id, nama, username, role) {
    document.getElementById("edit_user_id").value = id;
    document.getElementById("edit_user_nama").value = nama;
    document.getElementById("edit_user_username").value = username;
    document.getElementById("edit_user_role").value = role;
    new bootstrap.Modal(document.getElementById("modalEditUser")).show();
}
</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
