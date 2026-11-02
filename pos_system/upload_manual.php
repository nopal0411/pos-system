<?php
require_once 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

// Ambil daftar produk
$stmt = $pdo->query("SELECT id_produk, nama_produk FROM produk ORDER BY nama_produk ASC");
$produk_list = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar']) && isset($_POST['id_produk'])) {
    $id = (int)$_POST['id_produk'];
    $file = $_FILES['gambar'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'prod_' . $id . '_manual.' . $ext;
        $dest = UPLOAD_PATH . $newName;
        
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $pdo->query("UPDATE produk SET gambar = '$newName' WHERE id_produk = $id");
            $success = "Gambar produk berhasil diperbarui! Silakan kembali ke halaman produk.";
        } else {
            $error = "Gagal memindahkan file.";
        }
    } else {
        $error = "Terjadi kesalahan saat upload: " . $file['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Gambar Manual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width:600px;">
        <div class="card shadow-sm border-0" style="border-radius:15px;">
            <div class="card-body p-5">
                <h4 class="mb-2 text-center" style="font-weight:700;color:#1e3a5f;">Alat Bantu Ganti Gambar</h4>
                <p class="text-center text-muted mb-4">Pilih produk dan unggah gambar yang Anda inginkan dari komputer Anda.</p>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success border-0" style="background:#ecfdf5;color:#065f46;border-radius:10px;">
                        <strong>Berhasil!</strong> <?= $success ?>
                    </div>
                    <div class="text-center">
                        <a href="modules/produk/index.php" class="btn btn-primary px-4 py-2" style="border-radius:10px;">Kembali ke Halaman Produk</a>
                    </div>
                <?php else: ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label" style="font-weight:600;">1. Pilih Produk</label>
                            <select name="id_produk" class="form-select" required style="border-radius:10px;padding:10px;">
                                <option value="">-- Pilih Produk yang ingin diganti gambarnya --</option>
                                <?php foreach($produk_list as $p): ?>
                                    <option value="<?= $p['id_produk'] ?>" <?= (isset($_GET['id']) && $_GET['id'] == $p['id_produk']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nama_produk']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" style="font-weight:600;">2. Pilih File Gambar</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" required style="border-radius:10px;padding:10px;">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100" style="border-radius:10px;padding:12px;font-weight:600;">
                            Upload & Terapkan Gambar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
