<?php
/**
 * PRODUK/INDEX.PHP - Daftar Produk + Linear Search
 * POS System Toko Fenny Grosir
 *
 * Halaman ini mengimplementasikan Algoritma Linear Search
 * untuk pencarian produk secara real-time menggunakan AJAX.
 */
$pageTitle    = 'Manajemen Produk';
$pageSubtitle = 'Kelola data produk toko';
require_once __DIR__ . '/../../includes/header.php';

$produkList = getAllProduk();
$kategoriList = getAllKategori();
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-1"><i class="fas fa-box-open text-primary me-2"></i>Manajemen Produk</h1>
        <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a>
            <span style="margin:0 6px;color:#9ca3af;">/</span><span>Produk</span>
        </nav>
    </div>
    <a href="tambah.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>

<!-- ============================================================
     KOTAK PENCARIAN LINEAR SEARCH
     ============================================================ -->
<div class="card mb-4" style="border-left:4px solid #2c5f9e;">
    <div class="card-body" style="padding:20px;">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:42px;height:42px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-search" style="color:#2c5f9e;font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e3a5f;">Pencarian Produk – Algoritma Linear Search</div>
                <div style="font-size:12px;color:#6b7280;">Cari produk secara real-time. Sistem akan menelusuri setiap data produk satu per satu (O(n)).</div>
            </div>
        </div>
        <?php if(get_pengaturan('fitur_pencarian_linear') === 'aktif'): ?>
        <div class="d-flex gap-2" id="linearSearchContainer">
            <div style="flex:1;position:relative;">
                <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" id="linearSearchInput" class="form-control"
                       style="padding-left:40px;font-size:14px;"
                       placeholder="Ketik nama produk untuk mencari... (contoh: beras, minyak, teh)"
                       oninput="jalankanLinearSearch(this.value)">
            </div>
            <button class="btn btn-secondary" onclick="resetSearch()">
                <i class="fas fa-times"></i> Reset
            </button>
        </div>
        <!-- Info Hasil Pencarian -->
        <div id="searchInfo" style="margin-top:10px;font-size:12.5px;color:#6b7280;display:none;">
            <i class="fas fa-info-circle"></i>
            <span id="searchInfoText"></span>
            <span id="searchSteps" style="margin-left:8px;padding:2px 8px;background:#f3f4f6;border-radius:10px;font-size:11px;"></span>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     TABEL PRODUK
     ============================================================ -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-table"></i> Daftar Produk</div>
        <span class="badge badge-primary" id="produkCount"><?= count($produkList) ?> Produk</span>
    </div>
    <div class="card-body">
        <!-- Tabel Default (semua produk via DataTables) -->
        <div id="tabelDefault">
            <div class="table-wrapper">
                <table id="tabelProduk" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produkList as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if ($p['gambar']): ?>
                                <img src="<?= strpos($p['gambar'], 'http') === 0 ? e($p['gambar']) : UPLOAD_URL . e($p['gambar']) ?>"
                                     class="product-img-sm" alt="<?= e($p['nama_produk']) ?>">
                                <?php else: ?>
                                <div style="width:42px;height:42px;background:#dbeafe;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#2c5f9e;">
                                    <i class="fas fa-image"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:600;color:#1f2937;"><?= e($p['nama_produk']) ?></div>
                                <?php if ($p['deskripsi']): ?>
                                <div style="font-size:11.5px;color:#9ca3af;margin-top:2px;"><?= e(substr($p['deskripsi'], 0, 40)) ?>...</div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-primary"><?= e($p['nama_kategori']) ?></span></td>
                            <td style="color:#6b7280;"><?= formatRupiah($p['harga_beli']) ?></td>
                            <td style="font-weight:700;color:#1e3a5f;"><?= formatRupiah($p['harga_jual']) ?></td>
                            <td>
                                <span class="badge <?= $p['stok'] == 0 ? 'badge-danger' : ($p['stok'] < 10 ? 'badge-warning' : 'badge-success') ?>">
                                    <?= $p['stok'] == 0 ? 'Habis' : $p['stok'] . ' pcs' ?>
                                </span>
                            </td>
                            <td style="text-align:center;white-space:nowrap;">
                                <a href="edit.php?id=<?= $p['id_produk'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="hapus.php?id=<?= $p['id_produk'] ?>"
                                   class="btn btn-sm btn-danger"
                                   data-confirm="Hapus produk '<?= e($p['nama_produk']) ?>'?">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Hasil Linear Search (tersembunyi saat awal) -->
        <div id="tabelHasilSearch" style="display:none;">
            <div id="hasilSearchContainer"></div>
        </div>
    </div>
</div>

<?php
// Encode data produk ke JSON untuk digunakan oleh JavaScript Linear Search
$produkJSON = json_encode($produkList);

$extraScript = <<<SCRIPT
<script>
// ============================================================
// DATA PRODUK (dimuat dari PHP ke JavaScript)
// ============================================================
const dataProdukJS = {$produkJSON};

// Simpan referensi DataTable
let dtProduk = null;

$(document).ready(function() {
    dtProduk = initDataTable("#tabelProduk");
});

// ============================================================
// IMPLEMENTASI ALGORITMA LINEAR SEARCH (JavaScript)
// ============================================================
// Fungsi ini adalah implementasi Linear Search di sisi client
// yang akan menelusuri setiap elemen array dataProdukJS
// dari index 0 hingga n-1 untuk menemukan kata kunci.
//
// Namun untuk keakuratan penelitian, fungsi ini juga
// memanggil PHP API (search.php) yang menggunakan
// Algoritma Linear Search di sisi server (PHP).
// ============================================================

/**
 * Fungsi utama Linear Search
 * Dipanggil setiap kali user mengetik di input pencarian
 */
function jalankanLinearSearch(keyword) {
    const searchInfo = document.getElementById('searchInfo');
    const searchInfoText = document.getElementById('searchInfoText');
    const searchSteps = document.getElementById('searchSteps');

    keyword = keyword.trim();

    // Jika keyword kosong, tampilkan tabel normal
    if (keyword === '') {
        resetSearch();
        return;
    }

    // Tampilkan loader
    searchInfoText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menjalankan Linear Search...';
    searchInfo.style.display = 'block';

    // Panggil API Linear Search (PHP)
    fetch('search.php?q=' + encodeURIComponent(keyword))
        .then(response => response.json())
        .then(data => {
            tampilkanHasilSearch(data.hasil, data.keyword, data.total_data, data.ditemukan, data.langkah);
        })
        .catch(() => {
            // Fallback: Linear Search di JavaScript jika API gagal
            const hasil = linearSearchJS(dataProdukJS, keyword);
            tampilkanHasilSearch(hasil, keyword, dataProdukJS.length, hasil.length, dataProdukJS.length);
        });
}

/**
 * Linear Search JavaScript (Fallback)
 * Algoritma: Cek setiap elemen dari awal hingga akhir
 */
function linearSearchJS(data, keyword) {
    const hasil = [];
    const kw = keyword.toLowerCase();

    // Iterasi setiap elemen (O(n))
    for (let i = 0; i < data.length; i++) {
        if (data[i].nama_produk.toLowerCase().includes(kw)) {
            hasil.push(data[i]);
        }
    }
    return hasil;
}

/**
 * Render hasil pencarian ke tabel HTML
 */
function tampilkanHasilSearch(hasil, keyword, totalData, ditemukan, langkah) {
    const tabelDefault = document.getElementById('tabelDefault');
    const tabelHasil   = document.getElementById('tabelHasilSearch');
    const container    = document.getElementById('hasilSearchContainer');
    const searchInfo   = document.getElementById('searchInfo');
    const searchInfoText = document.getElementById('searchInfoText');
    const searchSteps  = document.getElementById('searchSteps');
    const produkCount  = document.getElementById('produkCount');

    // Sembunyikan DataTable, tampilkan hasil search
    tabelDefault.style.display = 'none';
    tabelHasil.style.display = 'block';

    // Update info
    searchInfo.style.display = 'block';
    if (ditemukan > 0) {
        searchInfoText.innerHTML = \`<strong style="color:#10b981;">\${ditemukan} produk ditemukan</strong> untuk kata kunci "<strong>\${keyword}</strong>"\`;
    } else {
        searchInfoText.innerHTML = \`<strong style="color:#ef4444;">Tidak ada produk</strong> yang cocok dengan kata kunci "<strong>\${keyword}</strong>"\`;
    }
    searchSteps.textContent = \`\${langkah} langkah dari \${totalData} data\`;
    produkCount.textContent = ditemukan + ' Ditemukan';

    // Render tabel hasil
    if (hasil.length === 0) {
        container.innerHTML = \`
            <div class="empty-state" style="padding:50px;">
                <i class="fas fa-search-minus" style="font-size:48px;color:#d1d5db;"></i>
                <h5 style="margin-top:16px;color:#6b7280;">Produk Tidak Ditemukan</h5>
                <p style="color:#9ca3af;">Tidak ada produk dengan nama "<strong>\${keyword}</strong>"</p>
                <button class="btn btn-secondary" onclick="resetSearch()" style="margin-top:12px;">
                    <i class="fas fa-times"></i> Reset Pencarian
                </button>
            </div>\`;
        return;
    }

    let rows = '';
    hasil.forEach((p, i) => {
        const stokBadge = p.stok == 0
            ? '<span class="badge badge-danger">Habis</span>'
            : (p.stok < 10
                ? \`<span class="badge badge-warning">\${p.stok} pcs</span>\`
                : \`<span class="badge badge-success">\${p.stok} pcs</span>\`);

        const gambar = p.gambar
            ? \`<img src="<?= BASE_URL ?>/assets/img/produk/\${p.gambar}" class="product-img-sm">\`
            : \`<div style="width:42px;height:42px;background:#dbeafe;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#2c5f9e;"><i class="fas fa-image"></i></div>\`;

        // Highlight keyword dalam nama produk
        const namaHighlight = p.nama_produk.replace(
            new RegExp(keyword, 'gi'),
            match => \`<mark style="background:#fef08a;padding:0 2px;border-radius:3px;">\${match}</mark>\`
        );

        rows += \`<tr>
            <td>\${i+1}</td>
            <td>\${gambar}</td>
            <td>
                <div style="font-weight:600;">\${namaHighlight}</div>
                <div style="font-size:11.5px;color:#9ca3af;">\${p.nama_kategori || ''}</div>
            </td>
            <td><span class="badge badge-primary">\${p.nama_kategori || '-'}</span></td>
            <td style="color:#6b7280;">Rp \${parseInt(p.harga_beli).toLocaleString('id-ID')}</td>
            <td style="font-weight:700;color:#1e3a5f;">Rp \${parseInt(p.harga_jual).toLocaleString('id-ID')}</td>
            <td>\${stokBadge}</td>
            <td style="text-align:center;">
                <a href="edit.php?id=\${p.id_produk}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                <a href="hapus.php?id=\${p.id_produk}" class="btn btn-sm btn-danger"
                   onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>\`;
    });

    container.innerHTML = \`
        <div class="table-wrapper">
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th>No.</th><th>Gambar</th><th>Nama Produk</th>
                        <th>Kategori</th><th>Harga Beli</th><th>Harga Jual</th>
                        <th>Stok</th><th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>\${rows}</tbody>
            </table>
        </div>\`;
}

/**
 * Reset pencarian - tampilkan kembali tabel DataTables
 */
function resetSearch() {
    document.getElementById('linearSearchInput').value = '';
    document.getElementById('tabelDefault').style.display = 'block';
    document.getElementById('tabelHasilSearch').style.display = 'none';
    document.getElementById('searchInfo').style.display = 'none';
    document.getElementById('produkCount').textContent = '<?= count($produkList) ?> Produk';
}
</script>
SCRIPT;

require_once __DIR__ . '/../../includes/footer.php';
?>
