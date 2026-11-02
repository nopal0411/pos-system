<?php
/**
 * KATEGORI/INDEX.PHP - Daftar Kategori Produk
 * POS System Toko Fenny Grosir
 */
$pageTitle    = 'Manajemen Kategori';
$pageSubtitle = 'Kelola kategori produk';
require_once __DIR__ . '/../../includes/header.php';

$kategoriList = getAllKategori();
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-1"><i class="fas fa-tags text-primary me-2"></i>Manajemen Kategori</h1>
        <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a>
            <span style="margin:0 6px;color:#9ca3af;">/</span>
            <span>Kategori</span>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus"></i> Tambah Kategori
    </button>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-list"></i> Daftar Kategori</div>
        <span class="badge badge-primary"><?= count($kategoriList) ?> Kategori</span>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tabelKategori" class="table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px;">No.</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        <th>Dibuat</th>
                        <th style="width:150px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($kategoriList as $i => $kat): ?>
                    <?php
                    // Hitung jumlah produk per kategori
                    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_kategori = ?");
                    $stmtCount->execute([$kat['id_kategori']]);
                    $jumlahProduk = $stmtCount->fetchColumn();
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;background:var(--primary-50);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--primary-500);">
                                    <i class="fas fa-tag" style="font-size:14px;"></i>
                                </div>
                                <span style="font-weight:600;"><?= e($kat['nama_kategori']) ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?= $jumlahProduk > 0 ? 'primary' : 'gray' ?>">
                                <?= $jumlahProduk ?> Produk
                            </span>
                        </td>
                        <td style="color:#6b7280;font-size:12.5px;"><?= date('d M Y', strtotime($kat['created_at'])) ?></td>
                        <td style="text-align:center;">
                            <button class="btn btn-sm btn-warning"
                                    onclick="editKategori(<?= $kat['id_kategori'] ?>, '<?= e($kat['nama_kategori']) ?>')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if ($jumlahProduk == 0): ?>
                            <a href="hapus.php?id=<?= $kat['id_kategori'] ?>"
                               class="btn btn-sm btn-danger"
                               data-confirm="Hapus kategori '<?= e($kat['nama_kategori']) ?>'?">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled title="Masih ada produk di kategori ini">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL TAMBAH KATEGORI
     ============================================================ -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="tambah.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="nama_kategori_tambah">Nama Kategori <span style="color:red;">*</span></label>
                        <input type="text" id="nama_kategori_tambah" name="nama_kategori"
                               class="form-control" placeholder="Contoh: Sembako, Minuman..."
                               required autofocus>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL EDIT KATEGORI
     ============================================================ -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="edit.php">
                <input type="hidden" name="id_kategori" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_nama">Nama Kategori <span style="color:red;">*</span></label>
                        <input type="text" id="edit_nama" name="nama_kategori"
                               class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraScript = '
<script>
$(document).ready(function() {
    initDataTable("#tabelKategori");
});

function editKategori(id, nama) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_nama").value = nama;
    new bootstrap.Modal(document.getElementById("modalEdit")).show();
}
</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
