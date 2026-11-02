<?php
/**
 * TRANSAKSI/INDEX.PHP - Halaman Kasir / POS Interface
 * POS System Toko Fenny Grosir
 *
 * Fitur:
 * - Grid produk dengan pencarian real-time (Linear Search)
 * - Keranjang belanja interaktif (klik produk → masuk keranjang)
 * - Hitung kembalian otomatis
 * - Metode pembayaran: Cash & QRIS
 * - Cetak struk setelah transaksi berhasil
 */
$pageTitle    = 'Transaksi / Kasir';
$pageSubtitle = 'Proses penjualan produk';

// Override padding untuk full-height layout
$extraHead = '<style>
.page-content{padding:16px 28px;}
.pos-container{height:calc(100vh - 120px);}

/* ── Metode Bayar Toggle ── */
.metode-bayar-wrap{margin:10px 0 8px;}
.metode-bayar-label{font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;}
.metode-toggle{display:grid;grid-template-columns:1fr 1fr;gap:6px;}
.metode-btn{
  display:flex;align-items:center;justify-content:center;gap:6px;
  padding:9px 6px;border-radius:8px;border:2px solid #e5e7eb;
  background:#f9fafb;cursor:pointer;font-size:12.5px;font-weight:600;
  color:#6b7280;transition:all .2s;
}
.metode-btn:hover{border-color:#2c5f9e;color:#2c5f9e;background:#eff6ff;}
.metode-btn.active{border-color:#2c5f9e;background:#2c5f9e;color:#fff;}
.metode-btn.active-qris{border-color:#7c3aed;background:#7c3aed;color:#fff;}
.metode-btn i{font-size:15px;}
.qris-info{
  display:none;margin-top:6px;padding:8px 10px;
  background:#f5f3ff;border:1.5px dashed #7c3aed;border-radius:8px;
  font-size:12px;color:#5b21b6;text-align:center;
}
.qris-info.show{display:block;}

/* ── Kembalian highlight ── */
#kembalianDisplay{font-weight:700;font-size:15px;transition:all .3s;}
</style>';

require_once __DIR__ . '/../../includes/header.php';

$produkAktif   = getProdukAktif();
$kategoriList  = getAllKategori();
$noTransaksi   = generateNoTransaksi();
$namaPetugas   = $_SESSION['nama'] ?? 'Kasir';
?>

<!-- ============================================================
     TOPBAR POS
     ============================================================ -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <div>
        <div style="font-size:13px;color:#6b7280;">No. Transaksi</div>
        <div style="font-size:16px;font-weight:700;color:#1e3a5f;"><?= e($noTransaksi) ?></div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:13px;color:#6b7280;">Kasir: <strong><?= e($namaPetugas) ?></strong></span>
        <button class="btn btn-secondary btn-sm" onclick="clearCart()">
            <i class="fas fa-trash-alt"></i> Kosongkan
        </button>
    </div>
</div>

<!-- ============================================================
     POS CONTAINER (2 Panel: Produk + Keranjang)
     ============================================================ -->
<div class="pos-container">

    <!-- ====================================================
         PANEL KIRI: PRODUK
         ==================================================== -->
    <div class="pos-products-panel">
        <!-- Header + Filter Kategori -->
        <div class="pos-panel-header">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="fas fa-box-open" style="color:#2c5f9e;margin-right:8px;"></i>Pilih Produk</h6>
                <span style="font-size:12px;color:#6b7280;" id="produkAvailableCount"><?= count($produkAktif) ?> produk tersedia</span>
            </div>
            <!-- Filter Kategori -->
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;">
                <button class="btn btn-sm btn-primary" onclick="filterKategori(0)" id="katBtn-0">Semua</button>
                <?php foreach ($kategoriList as $k): ?>
                <button class="btn btn-sm btn-secondary" onclick="filterKategori(<?= $k['id_kategori'] ?>)"
                        id="katBtn-<?= $k['id_kategori'] ?>">
                    <?= e($k['nama_kategori']) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Search Bar (Linear Search) -->
        <div class="pos-search-bar">
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="posSearch" placeholder="Cari produk... (Linear Search)"
                       oninput="cariProdukPOS(this.value)">
            </div>
        </div>

        <!-- Grid Produk -->
        <div class="pos-products-grid" id="produkGrid">
            <?php if (empty($produkAktif)): ?>
            <div style="grid-column:1/-1;text-align:center;padding:40px;color:#9ca3af;">
                <i class="fas fa-box" style="font-size:40px;margin-bottom:12px;display:block;"></i>
                <p>Tidak ada produk tersedia</p>
            </div>
            <?php else: ?>
            <?php foreach ($produkAktif as $p): ?>
            <div class="product-card"
                 data-id="<?= $p['id_produk'] ?>"
                 data-nama="<?= e($p['nama_produk']) ?>"
                 data-harga="<?= $p['harga_jual'] ?>"
                 data-stok="<?= $p['stok'] ?>"
                 data-kategori="<?= $p['id_kategori'] ?>"
                 onclick="tambahKeKeranjang(<?= (int)$p['id_produk'] ?>, '<?= addslashes(e($p['nama_produk'])) ?>', <?= (float)$p['harga_jual'] ?>, <?= (int)$p['stok'] ?>)">

                <?php if ($p['gambar']): ?>
                <img src="<?= strpos($p['gambar'], 'http') === 0 ? e($p['gambar']) : UPLOAD_URL . e($p['gambar']) ?>" alt="<?= e($p['nama_produk']) ?>">
                <?php else: ?>
                <div class="product-img-placeholder"><i class="fas fa-box"></i></div>
                <?php endif; ?>

                <div class="product-name"><?= e($p['nama_produk']) ?></div>
                <div class="product-price"><?= formatRupiah($p['harga_jual']) ?></div>
                <div class="product-stock">Stok: <?= $p['stok'] ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ====================================================
         PANEL KANAN: KERANJANG BELANJA
         ==================================================== -->
    <div class="pos-cart-panel">
        <div class="pos-panel-header">
            <h6><i class="fas fa-shopping-cart" style="color:#2c5f9e;margin-right:8px;"></i>Keranjang Belanja</h6>
            <span class="badge badge-primary" id="cartCount">0 Item</span>
        </div>

        <!-- Item Keranjang -->
        <div class="pos-cart-items" id="cartItems">
            <div class="cart-empty" id="cartEmpty">
                <i class="fas fa-shopping-basket"></i>
                <p>Keranjang kosong<br><small>Klik produk untuk menambahkan</small></p>
            </div>
        </div>

        <!-- Ringkasan & Pembayaran -->
        <div class="pos-cart-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotalDisplay">Rp 0</span>
            </div>
            <div class="summary-row total">
                <span>TOTAL</span>
                <span id="totalDisplay">Rp 0</span>
            </div>

            <hr style="margin:10px 0;border-color:#e5e7eb;">

            <!-- ── METODE PEMBAYARAN ── -->
            <div class="metode-bayar-wrap">
                <div class="metode-bayar-label"><i class="fas fa-credit-card" style="margin-right:4px;"></i>Metode Pembayaran</div>
                <div class="metode-toggle">
                    <button class="metode-btn active" id="btnCash" onclick="setMetode('cash')">
                        <i class="fas fa-money-bill-wave"></i> Cash
                    </button>
                    <button class="metode-btn" id="btnQris" onclick="setMetode('qris')">
                        <i class="fas fa-qrcode"></i> QRIS
                    </button>
                </div>
                <!-- Info QRIS -->
                <div class="qris-info" id="qrisInfo">
                    <img src="../../assets/img/qris.png" alt="QRIS Barcode" style="width: 150px; height: 150px; margin: 8px auto; display: block; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <strong>Pembayaran DANA / QRIS</strong><br>
                    Scan QR Code atau transfer ke DANA:<br>
                    <strong>085712863116</strong> (a.n. <strong>Fibril Acyuta</strong>)<br>
                    Kembalian otomatis = Rp 0
                </div>
            </div>

            <!-- Uang Bayar (hanya tampil untuk Cash) -->
            <div id="uangBayarSection">
                <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Uang Bayar</div>
                <input type="number" id="uangBayar" class="bayar-input" placeholder="0"
                       oninput="hitungKembalian()" min="0">

                <!-- Tombol Cepat Nominal -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:10px;" id="nominalCepat">
                    <button class="btn btn-sm btn-secondary" onclick="setBayar(20000)">Rp 20.000</button>
                    <button class="btn btn-sm btn-secondary" onclick="setBayar(50000)">Rp 50.000</button>
                    <button class="btn btn-sm btn-secondary" onclick="setBayar(100000)">Rp 100.000</button>
                    <button class="btn btn-sm btn-secondary" onclick="setBayar(0, true)">Uang Pas</button>
                </div>
            </div>

            <div class="summary-row kembalian">
                <span><i class="fas fa-coins"></i> Kembalian</span>
                <span id="kembalianDisplay">Rp 0</span>
            </div>

            <button class="btn-bayar" id="btnBayar" onclick="prosesTransaksi()" disabled>
                <i class="fas fa-check-circle"></i>
                Proses Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL SUKSES TRANSAKSI + STRUK
     ============================================================ -->
<div class="modal fade" id="modalStruk" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#059669,#10b981);">
                <h5 class="modal-title" style="color:#fff;">
                    <i class="fas fa-check-circle me-2"></i>Transaksi Berhasil!
                </h5>
            </div>
            <div class="modal-body" id="modalStrukBody" style="padding:0 !important;">
                <!-- Struk dirender di sini -->
            </div>
            <div class="modal-footer" style="gap:8px;">
                <button class="btn btn-secondary" onclick="cetakStruk()">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
                <button class="btn btn-primary" onclick="transaksiBaruSelesai()">
                    <i class="fas fa-plus"></i> Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden struk for print -->
<div id="struk-print" style="display:none;"></div>

<?php
$produkJSON = json_encode($produkAktif);
$extraScript = <<<SCRIPT
<script>
// ============================================================
// DATA PRODUK & STATE
// ============================================================
const semuaProduk    = {$produkJSON};
let keranjang        = [];
let idTransaksiBaru  = null;
let noTransaksiBaru  = '{$noTransaksi}';
let metodeBayar      = 'cash'; // 'cash' | 'qris'

// ============================================================
// METODE PEMBAYARAN
// ============================================================
function setMetode(m) {
    metodeBayar = m;
    const btnCash  = document.getElementById('btnCash');
    const btnQris  = document.getElementById('btnQris');
    const qrisInfo = document.getElementById('qrisInfo');
    const uangSec  = document.getElementById('uangBayarSection');

    if (m === 'cash') {
        btnCash.className  = 'metode-btn active';
        btnQris.className  = 'metode-btn';
        qrisInfo.classList.remove('show');
        uangSec.style.display = '';
        hitungKembalian();
    } else {
        btnCash.className  = 'metode-btn';
        btnQris.className  = 'metode-btn active-qris';
        qrisInfo.classList.add('show');
        uangSec.style.display = 'none';
        // QRIS: kembalian selalu 0, uang bayar = total
        const total = keranjang.reduce((s, i) => s + i.subtotal, 0);
        document.getElementById('uangBayar').value = total;
        const el = document.getElementById('kembalianDisplay');
        el.textContent = 'Rp 0';
        el.style.color = '#10b981';
        // Aktifkan tombol bayar jika keranjang tidak kosong
        document.getElementById('btnBayar').disabled = keranjang.length === 0;
    }
}

// ============================================================
// TAMBAH KE KERANJANG
// ============================================================
function tambahKeKeranjang(id, nama, harga, stok) {
    if (stok <= 0) {
        showToast('Stok produk habis!', 'danger');
        return;
    }

    const existing = keranjang.find(item => item.id === id);

    if (existing) {
        if (existing.jumlah >= stok) {
            showToast('Jumlah melebihi stok tersedia!', 'warning');
            return;
        }
        existing.jumlah++;
        existing.subtotal = existing.jumlah * existing.harga;
    } else {
        keranjang.push({
            id: id,
            nama: nama,
            harga: parseFloat(harga),
            jumlah: 1,
            subtotal: parseFloat(harga),
            stok: parseInt(stok)
        });
    }

    renderKeranjang();
    showToast(nama + ' ditambahkan ke keranjang', 'success');
}

// ============================================================
// RENDER KERANJANG
// ============================================================
function renderKeranjang() {
    const container = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const btnBayar  = document.getElementById('btnBayar');

    if (keranjang.length === 0) {
        container.innerHTML = '<div class="cart-empty"><i class="fas fa-shopping-basket"></i><p>Keranjang kosong<br><small>Klik produk untuk menambahkan</small></p></div>';
        cartCount.textContent = '0 Item';
        btnBayar.disabled = true;
        updateTotal();
        return;
    }

    cartCount.textContent = keranjang.length + ' Item';
    // Aktifkan tombol bayar sesuai metode
    if (metodeBayar === 'qris') {
        btnBayar.disabled = false;
    } else {
        const total = keranjang.reduce((s, i) => s + i.subtotal, 0);
        const bayar = parseFloat(document.getElementById('uangBayar').value) || 0;
        btnBayar.disabled = bayar < total;
    }

    let html = '';
    keranjang.forEach((item, idx) => {
        html += `
        <div class="cart-item">
            <div class="cart-item-info">
                <div class="cart-item-name">\${item.nama}</div>
                <div class="cart-item-price">Rp \${item.harga.toLocaleString('id-ID')} / pcs</div>
            </div>
            <div class="cart-item-qty">
                <button class="qty-btn" onclick="ubahJumlah(\${idx}, -1)">−</button>
                <input class="qty-input" type="number" value="\${item.jumlah}" min="1" max="\${item.stok}"
                       onchange="setJumlah(\${idx}, this.value)">
                <button class="qty-btn" onclick="ubahJumlah(\${idx}, 1)">+</button>
            </div>
            <div class="cart-item-subtotal">Rp \${item.subtotal.toLocaleString('id-ID')}</div>
            <button class="cart-item-remove" onclick="hapusDariKeranjang(\${idx})">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    });

    container.innerHTML = html;
    updateTotal();

    // Jika QRIS, update uang bayar = total baru
    if (metodeBayar === 'qris') {
        const total = keranjang.reduce((s, i) => s + i.subtotal, 0);
        document.getElementById('uangBayar').value = total;
    }
}

function ubahJumlah(idx, delta) {
    const item = keranjang[idx];
    const newQty = item.jumlah + delta;
    if (newQty < 1) { hapusDariKeranjang(idx); return; }
    if (newQty > item.stok) { showToast('Melebihi stok!', 'warning'); return; }
    item.jumlah = newQty;
    item.subtotal = newQty * item.harga;
    renderKeranjang();
}

function setJumlah(idx, val) {
    const item = keranjang[idx];
    val = Math.max(1, Math.min(parseInt(val) || 1, item.stok));
    item.jumlah = val;
    item.subtotal = val * item.harga;
    renderKeranjang();
}

function hapusDariKeranjang(idx) {
    keranjang.splice(idx, 1);
    renderKeranjang();
}

function clearCart() {
    if (keranjang.length === 0) return;
    if (!confirm('Kosongkan semua item keranjang?')) return;
    keranjang = [];
    renderKeranjang();
}

// ============================================================
// HITUNG TOTAL & KEMBALIAN OTOMATIS
// ============================================================
function updateTotal() {
    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    document.getElementById('subtotalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalDisplay').textContent    = 'Rp ' + total.toLocaleString('id-ID');
    hitungKembalian();
}

function hitungKembalian() {
    if (metodeBayar === 'qris') return; // QRIS = tidak perlu hitung kembalian

    const total  = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    const bayar  = parseFloat(document.getElementById('uangBayar').value) || 0;
    const kembal = bayar - total;
    const el     = document.getElementById('kembalianDisplay');
    const btnBayar = document.getElementById('btnBayar');

    if (kembal < 0) {
        el.textContent = '– Kurang: Rp ' + Math.abs(kembal).toLocaleString('id-ID');
        el.style.color = '#ef4444';
        btnBayar.disabled = true;
    } else {
        el.textContent = 'Rp ' + kembal.toLocaleString('id-ID');
        el.style.color = '#10b981';
        btnBayar.disabled = keranjang.length === 0;
    }
}

function setBayar(nominal, uangPas = false) {
    if (uangPas) {
        const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
        document.getElementById('uangBayar').value = total;
    } else {
        document.getElementById('uangBayar').value = nominal;
    }
    hitungKembalian();
}

// ============================================================
// FILTER KATEGORI
// ============================================================
function filterKategori(idKat) {
    document.querySelectorAll('[id^="katBtn-"]').forEach(btn => {
        btn.className = 'btn btn-sm btn-secondary';
    });
    document.getElementById('katBtn-' + idKat).className = 'btn btn-sm btn-primary';

    const cards = document.querySelectorAll('.product-card');
    let count = 0;
    cards.forEach(card => {
        const kat = card.getAttribute('data-kategori');
        if (idKat == 0 || kat == idKat) {
            card.style.display = '';
            count++;
        } else {
            card.style.display = 'none';
        }
    });
    document.getElementById('produkAvailableCount').textContent = count + ' produk tersedia';
}

// ============================================================
// LINEAR SEARCH
// ============================================================
function cariProdukPOS(keyword) {
    const kw = keyword.toLowerCase().trim();
    const cards = document.querySelectorAll('.product-card');
    let count = 0;

    cards.forEach(card => {
        const nama = card.getAttribute('data-nama').toLowerCase();
        if (kw === '' || nama.includes(kw)) {
            card.style.display = '';
            count++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('produkAvailableCount').textContent = count + ' produk ditemukan';
}

// ============================================================
// PROSES TRANSAKSI (AJAX ke simpan.php)
// ============================================================
function prosesTransaksi() {
    if (keranjang.length === 0) {
        showToast('Keranjang masih kosong!', 'danger');
        return;
    }

    const total  = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    let bayar    = parseFloat(document.getElementById('uangBayar').value) || 0;
    let kembalian;

    if (metodeBayar === 'qris') {
        // QRIS: bayar tepat total, kembalian 0
        bayar     = total;
        kembalian = 0;
    } else {
        kembalian = bayar - total;
        if (bayar < total) {
            showToast('Uang bayar kurang dari total!', 'warning');
            document.getElementById('uangBayar').focus();
            return;
        }
    }

    const btnBayar = document.getElementById('btnBayar');
    btnBayar.disabled = true;
    btnBayar.innerHTML = '<span class="spinner"></span> Memproses...';

    const payload = {
        no_transaksi:  noTransaksiBaru,
        total_harga:   total,
        uang_bayar:    bayar,
        kembalian:     kembalian,
        metode_bayar:  metodeBayar,
        items: keranjang.map(i => ({
            id_produk: i.id,
            jumlah:    i.jumlah,
            harga:     i.harga,
            subtotal:  i.subtotal
        }))
    };

    fetch('simpan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            idTransaksiBaru = data.id_transaksi;
            tampilkanStruk(data, payload);
        } else {
            showToast('Gagal menyimpan: ' + data.message, 'danger');
            btnBayar.disabled = false;
            btnBayar.innerHTML = '<i class="fas fa-check-circle"></i> Proses Pembayaran';
        }
    })
    .catch(err => {
        showToast('Terjadi kesalahan koneksi!', 'danger');
        btnBayar.disabled = false;
        btnBayar.innerHTML = '<i class="fas fa-check-circle"></i> Proses Pembayaran';
    });
}

// ============================================================
// TAMPILKAN STRUK
// ============================================================
function tampilkanStruk(data, payload) {
    const now = new Date();
    const tgl = now.toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'});
    const jam = now.toLocaleTimeString('id-ID');
    const metodeLabel = payload.metode_bayar === 'qris' ? '🔲 QRIS' : '💵 Cash';

    let itemsHtml = '';
    keranjang.forEach(item => {
        itemsHtml += `
        <tr>
            <td style="padding:4px 0;">\${item.nama}</td>
            <td style="padding:4px 8px;text-align:center;">\${item.jumlah}</td>
            <td style="padding:4px 0;text-align:right;">Rp \${item.subtotal.toLocaleString('id-ID')}</td>
        </tr>`;
    });

    const struk = `
    <div style="font-family:'Courier New',monospace;font-size:13px;padding:24px;max-width:380px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:16px;">
            <div style="font-size:18px;font-weight:800;color:#1e3a5f;">TOKO FENNY GROSIR</div>
            <div style="font-size:12px;color:#6b7280;">Jl. Contoh No. 1, Kota &bull; Telp: 08xx-xxxx-xxxx</div>
            <div style="border-top:2px dashed #d1d5db;margin:12px 0;"></div>
            <div style="font-size:12px;">No: <strong>\${data.no_transaksi}</strong></div>
            <div style="font-size:12px;">\${tgl} &bull; \${jam}</div>
            <div style="font-size:12px;">Kasir: <strong>{$namaPetugas}</strong></div>
            <div style="border-top:1px dashed #d1d5db;margin:12px 0;"></div>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="font-size:12px;color:#6b7280;">
                    <th style="text-align:left;padding:4px 0;">Produk</th>
                    <th style="text-align:center;padding:4px 8px;">Qty</th>
                    <th style="text-align:right;padding:4px 0;">Subtotal</th>
                </tr>
            </thead>
            <tbody>\${itemsHtml}</tbody>
        </table>
        <div style="border-top:2px dashed #d1d5db;margin:12px 0;"></div>
        <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;">
            <span>TOTAL</span><span>Rp \${payload.total_harga.toLocaleString('id-ID')}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:#6b7280;margin-top:4px;">
            <span>Metode Bayar</span><span>\${metodeLabel}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:#6b7280;margin-top:4px;">
            <span>Uang Bayar</span><span>Rp \${payload.uang_bayar.toLocaleString('id-ID')}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;color:#10b981;margin-top:4px;">
            <span>Kembalian</span><span>Rp \${payload.kembalian.toLocaleString('id-ID')}</span>
        </div>
        <div style="border-top:2px dashed #d1d5db;margin:16px 0;"></div>
        <div style="text-align:center;font-size:12px;color:#6b7280;">
            <div>Terima kasih telah berbelanja!</div>
            <div>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
        </div>
    </div>`;

    document.getElementById('modalStrukBody').innerHTML = struk;
    document.getElementById('struk-print').innerHTML   = struk;
    new bootstrap.Modal(document.getElementById('modalStruk')).show();
}

// ============================================================
// CETAK STRUK
// ============================================================
function cetakStruk() {
    const struk = document.getElementById('struk-print').innerHTML;
    const w = window.open('', '_blank', 'width=420,height=650');
    w.document.write(`<!DOCTYPE html>
<html>
<head>
<title>Struk - \${noTransaksiBaru}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Courier New',monospace;font-size:12px;background:#fff;color:#000;width:80mm;}
@media print{body{width:80mm;} .no-print{display:none;}}
</style>
</head>
<body>
<div class="no-print" style="padding:10px;text-align:center;background:#f3f4f6;margin-bottom:10px;">
  <button onclick="window.print()" style="padding:8px 20px;background:#2c5f9e;color:white;border:none;border-radius:6px;cursor:pointer;font-weight:bold;margin-right:6px;">
    🖨️ Cetak Struk
  </button>
  <button onclick="window.close()" style="padding:8px 16px;background:#6b7280;color:white;border:none;border-radius:6px;cursor:pointer;">
    ✕ Tutup
  </button>
</div>
\${struk}
</body>
</html>`);
    w.document.close();
    setTimeout(() => w.print(), 600);
}

function transaksiBaruSelesai() {
    bootstrap.Modal.getInstance(document.getElementById('modalStruk')).hide();
    keranjang = [];
    metodeBayar = 'cash';
    setMetode('cash');
    renderKeranjang();
    document.getElementById('uangBayar').value = '';
    document.getElementById('kembalianDisplay').textContent = 'Rp 0';
    document.getElementById('btnBayar').innerHTML = '<i class="fas fa-check-circle"></i> Proses Pembayaran';
    // Generate no transaksi baru
    fetch('get_no_transaksi.php')
        .then(r => r.json())
        .then(d => { noTransaksiBaru = d.no; });
}
</script>
SCRIPT;

require_once __DIR__ . '/../../includes/footer.php';
?>
