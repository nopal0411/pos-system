<?php
/**
 * PRODUK/TAMBAH.PHP - Form Tambah Produk
 * POS System Toko Fenny Grosir
 */
$pageTitle    = 'Tambah Produk';
$pageSubtitle = 'Tambah produk baru ke katalog';
require_once __DIR__ . '/../../includes/header.php';

$kategoriList = getAllKategori();
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $nama       = trim($_POST['nama_produk'] ?? '');
    $id_kat     = (int)($_POST['id_kategori'] ?? 0);
    $harga_beli = (float)str_replace(['Rp ', '.', ','], ['', '', '.'], $_POST['harga_beli'] ?? 0);
    $harga_jual = (float)str_replace(['Rp ', '.', ','], ['', '', '.'], $_POST['harga_jual'] ?? 0);
    $stok       = (int)($_POST['stok'] ?? 0);
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    if (empty($nama))    $errors[] = 'Nama produk wajib diisi.';
    if ($id_kat <= 0)    $errors[] = 'Kategori wajib dipilih.';
    if ($harga_beli <= 0) $errors[] = 'Harga beli harus lebih dari 0.';
    if ($harga_jual <= 0) $errors[] = 'Harga jual harus lebih dari 0.';

    // Upload gambar
    $namaGambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $namaGambar = uploadGambar($_FILES['gambar']);
        if (!$namaGambar) $errors[] = 'Format gambar tidak valid (JPEG/PNG/GIF/WebP, maks 2MB).';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO produk (id_kategori, nama_produk, harga_beli, harga_jual, stok, gambar, deskripsi)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_kat, $nama, $harga_beli, $harga_jual, $stok, $namaGambar, $deskripsi]);
        setFlash('success', "Produk '$nama' berhasil ditambahkan.");
        redirect(BASE_URL . '/modules/produk/index.php');
    }
}
?>

<div class="page-header">
    <h1 class="h4 mb-1"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Produk Baru</h1>
    <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
        <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a>
        <span style="margin:0 6px;">/</span>
        <a href="index.php">Produk</a>
        <span style="margin:0 6px;">/</span><span>Tambah</span>
    </nav>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <ul style="margin:0;padding-left:16px;">
        <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="formTambahProduk">
    <div class="row g-4">
        <!-- Kolom Kiri: Info Produk -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-info-circle"></i> Informasi Produk</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Nama Produk <span style="color:red;">*</span></label>
                                <input type="text" name="nama_produk" class="form-control"
                                       placeholder="Contoh: Beras Premium 5kg"
                                       value="<?= e($old['nama_produk'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori <span style="color:red;">*</span></label>
                                <select name="id_kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($kategoriList as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"
                                        <?= ($old['id_kategori'] ?? '') == $k['id_kategori'] ? 'selected' : '' ?>>
                                        <?= e($k['nama_kategori']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Stok Awal <span style="color:red;">*</span></label>
                                <input type="number" name="stok" class="form-control"
                                       placeholder="0" min="0"
                                       value="<?= e($old['stok'] ?? '0') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Harga Beli <span style="color:red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_beli" class="form-control"
                                           placeholder="0" min="0"
                                           value="<?= e($old['harga_beli'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Harga Jual <span style="color:red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_jual" class="form-control"
                                           placeholder="0" min="0"
                                           value="<?= e($old['harga_jual'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Deskripsi Produk</label>
                                <textarea name="deskripsi" class="form-control" rows="3"
                                          placeholder="Deskripsi singkat produk..."><?= e($old['deskripsi'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Upload Gambar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-image"></i> Gambar Produk</div>
                </div>
                <div class="card-body" style="text-align:center;">
                    <div id="imgPreviewBox" style="width:100%;height:180px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;overflow:hidden;border:2px dashed #d1d5db;cursor:pointer;" onclick="document.getElementById('gambar').click()">
                        <img id="img-preview" src="" alt="Preview" style="max-width:100%;max-height:180px;display:none;object-fit:cover;">
                        <div id="imgPlaceholder">
                            <i class="fas fa-cloud-upload-alt" style="font-size:36px;color:#9ca3af;margin-bottom:8px;display:block;"></i>
                            <span style="font-size:12px;color:#9ca3af;">Klik untuk upload gambar</span>
                        </div>
                    </div>
                    <input type="file" id="gambar" name="gambar" accept="image/*" style="display:none;"
                           onchange="previewImg(this)">
                    <button type="button" class="btn btn-secondary w-100" onclick="document.getElementById('gambar').click()">
                        <i class="fas fa-upload"></i> Pilih Gambar
                    </button>
                    <p style="font-size:11px;color:#9ca3af;margin-top:8px;">JPEG, PNG, GIF, WebP. Maks 2MB.</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                    <a href="index.php" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$extraScript = '
<script>
function previewImg(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById("img-preview");
            img.src = e.target.result;
            img.style.display = "block";
            document.getElementById("imgPlaceholder").style.display = "none";
        };
        reader.readAsDataURL(file);
    }
}
</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
