<?php
require_once 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar'])) {
    $file = $_FILES['gambar'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'prod_4_final.' . $ext;
        $dest = UPLOAD_PATH . $newName;
        
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $pdo->query("UPDATE produk SET gambar = '$newName' WHERE id_produk = 4");
            $success = "Gambar Tepung Terigu berhasil diperbarui! Silakan kembali ke halaman produk.";
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
    <title>Upload Gambar Tepung Terigu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width:500px;">
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <h4 class="mb-4">Upload Gambar Tepung Terigu</h4>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                    <a href="modules/produk/index.php" class="btn btn-primary mt-2">Lihat Hasilnya</a>
                <?php else: ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <p class="text-muted mb-4">Pilih gambar penguin Segiris yang Anda punya, lalu klik Upload.</p>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" name="gambar" class="form-control mb-3" accept="image/*" required>
                        <button type="submit" class="btn btn-primary w-100">Upload & Ganti Gambar</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
