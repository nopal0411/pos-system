<?php
/**
 * PRODUK/EDIT.PHP - Form Edit Produk
 */
$pageTitle = 'Edit Produk';
require_once __DIR__ . '/../../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$produk = getProdukById($id);
if (!$produk) {
    setFlash('danger', 'Produk tidak ditemukan.');
    redirect(BASE_URL . '/modules/produk/index.php');
}

$kategoriList = getAllKategori();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama_produk'] ?? '');
    $id_kat     = (int)($_POST['id_kategori'] ?? 0);
    $harga_beli = (float)($_POST['harga_beli'] ?? 0);
    $harga_jual = (float)($_POST['harga_jual'] ?? 0);
    $stok       = (int)($_POST['stok'] ?? 0);
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    if (empty($nama))     $errors[] = 'Nama produk wajib diisi.';
    if ($id_kat <= 0)     $errors[] = 'Kategori wajib dipilih.';
    if ($harga_beli <= 0) $errors[] = 'Harga beli harus lebih dari 0.';
    if ($harga_jual <= 0) $errors[] = 'Harga jual harus lebih dari 0.';

    $namaGambar = $produk['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $newGambar = uploadGambar($_FILES['gambar']);
        if ($newGambar) {
            // Hapus gambar lama
            if ($namaGambar && file_exists(UPLOAD_PATH . $namaGambar)) {
                unlink(UPLOAD_PATH . $namaGambar);
            }
            $namaGambar = $newGambar;
        } else {
            $errors[] = 'Format gambar tidak valid.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE produk SET id_kategori=?, nama_produk=?, harga_beli=?, harga_jual=?, stok=?, gambar=?, deskripsi=?
            WHERE id_produk=?
        ");
        $stmt->execute([$id_kat, $nama, $harga_beli, $harga_jual, $stok, $namaGambar, $deskripsi, $id]);
        setFlash('success', "Produk '$nama' berhasil diperbarui.");
        redirect(BASE_URL . '/modules/produk/index.php');
    }
}
?>

<div class="page-header">
    <h1 class="h4 mb-1"><i class="fas fa-edit text-primary me-2"></i>Edit Produk</h1>
    <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
        <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a> /
        <a href="index.php">Produk</a> / <span>Edit</span>
    </nav>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>
    <ul style="margin:0;padding-left:16px;"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><div class="card-title"><i class="fas fa-info-circle"></i> Informasi Produk</div></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Nama Produk *</label>
                                <input type="text" name="nama_produk" class="form-control"
                                       value="<?= e($produk['nama_produk']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori *</label>
                                <select name="id_kategori" class="form-control" required>
                                    <?php foreach ($kategoriList as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>" <?= $produk['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>>
                                        <?= e($k['nama_kategori']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" min="0" value="<?= e($produk['stok']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Harga Beli *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_beli" class="form-control" min="0" value="<?= e($produk['harga_beli']) ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Harga Jual *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_jual" class="form-control" min="0" value="<?= e($produk['harga_jual']) ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= e($produk['deskripsi']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><div class="card-title"><i class="fas fa-image"></i> Gambar Produk</div></div>
                <div class="card-body" style="text-align:center;">
                    <div style="width:100%;height:180px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;overflow:hidden;border:2px dashed #d1d5db;cursor:pointer;" onclick="document.getElementById('gambar').click()">
                        <img id="img-preview"
                             src="<?= $produk['gambar'] ? (strpos($produk['gambar'], 'http') === 0 ? e($produk['gambar']) : UPLOAD_URL . e($produk['gambar'])) : '' ?>"
                             alt="Preview"
                             style="max-width:100%;max-height:180px;object-fit:cover;<?= $produk['gambar'] ? '' : 'display:none;' ?>">
                        <div id="imgPlaceholder" <?= $produk['gambar'] ? 'style="display:none;"' : '' ?>>
                            <i class="fas fa-image" style="font-size:36px;color:#9ca3af;display:block;margin-bottom:8px;"></i>
                            <span style="font-size:12px;color:#9ca3af;">Klik untuk ganti gambar</span>
                        </div>
                    </div>
                    <input type="file" id="gambar" name="gambar" accept="image/*" style="display:none;" onchange="previewImg(this)">
                    <button type="button" class="btn btn-secondary w-100" onclick="document.getElementById('gambar').click()">
                        <i class="fas fa-upload"></i> Ganti Gambar
                    </button>
                    <p style="font-size:11px;color:#9ca3af;margin-top:8px;">Kosongkan jika tidak ingin ganti gambar.</p>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-warning w-100 mb-2"><i class="fas fa-save"></i> Update Produk</button>
                    <a href="index.php" class="btn btn-secondary w-100"><i class="fas fa-times"></i> Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$extraScript = '<script>
function previewImg(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("img-preview").src = e.target.result;
            document.getElementById("img-preview").style.display = "block";
            document.getElementById("imgPlaceholder").style.display = "none";
        };
        reader.readAsDataURL(file);
    }
}
</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
