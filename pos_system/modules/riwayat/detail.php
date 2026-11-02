<?php
/**
 * RIWAYAT/DETAIL.PHP - Detail Transaksi
 */
$pageTitle = 'Detail Transaksi';
require_once __DIR__ . '/../../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$transaksi = getTransaksiById($id);
if (!$transaksi) {
    setFlash('danger', 'Transaksi tidak ditemukan.');
    redirect(BASE_URL . '/modules/riwayat/index.php');
}
$detail = getDetailTransaksi($id);
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-1"><i class="fas fa-receipt text-primary me-2"></i>Detail Transaksi</h1>
        <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
            <a href="index.php">Riwayat</a> / <span><?= e($transaksi['no_transaksi']) ?></span>
        </nav>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="<?= BASE_URL ?>/modules/transaksi/cetak_struk.php?id=<?= $transaksi['id_transaksi'] ?>"
           target="_blank" class="btn btn-success">
            <i class="fas fa-receipt"></i> Cetak Struk
        </a>
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Detail
        </button>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row g-4">
    <!-- Info Transaksi -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><div class="card-title"><i class="fas fa-info-circle"></i> Info Transaksi</div></div>
            <div class="card-body">
                <table style="width:100%;font-size:13.5px;">
                    <tr><td style="color:#6b7280;padding:6px 0;">No. Transaksi</td><td style="font-weight:700;color:#1e3a5f;"><?= e($transaksi['no_transaksi']) ?></td></tr>
                    <tr><td style="color:#6b7280;padding:6px 0;">Tanggal</td><td><?= date('d M Y', strtotime($transaksi['tanggal'])) ?></td></tr>
                    <tr><td style="color:#6b7280;padding:6px 0;">Waktu</td><td><?= date('H:i:s', strtotime($transaksi['tanggal'])) ?></td></tr>
                    <tr><td style="color:#6b7280;padding:6px 0;">Kasir</td><td><?= e($transaksi['nama_kasir']) ?></td></tr>
                    <tr><td colspan="2"><hr style="margin:8px 0;border-color:#e5e7eb;"></td></tr>
                    <tr><td style="color:#6b7280;padding:6px 0;">Total</td><td style="font-weight:800;font-size:15px;color:#1e3a5f;"><?= formatRupiah($transaksi['total_harga']) ?></td></tr>
                    <tr><td style="color:#6b7280;padding:6px 0;">Uang Bayar</td><td><?= formatRupiah($transaksi['uang_bayar']) ?></td></tr>
                    <tr><td style="color:#6b7280;padding:6px 0;">Kembalian</td><td style="font-weight:700;color:#10b981;"><?= formatRupiah($transaksi['kembalian']) ?></td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Item -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-list"></i> Item Produk</div>
                <span class="badge badge-primary"><?= count($detail) ?> Item</span>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrapper">
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Produk</th>
                                <th style="text-align:center;">Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($detail as $i => $d): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <?php if ($d['gambar']): ?>
                                        <img src="<?= strpos($d['gambar'], 'http') === 0 ? e($d['gambar']) : UPLOAD_URL . e($d['gambar']) ?>" class="product-img-sm">
                                        <?php else: ?>
                                        <div style="width:36px;height:36px;background:#dbeafe;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#2c5f9e;"><i class="fas fa-box"></i></div>
                                        <?php endif; ?>
                                        <span style="font-weight:600;"><?= e($d['nama_produk']) ?></span>
                                    </div>
                                </td>
                                <td style="text-align:center;font-weight:700;"><?= $d['jumlah'] ?></td>
                                <td><?= formatRupiah($d['harga']) ?></td>
                                <td style="font-weight:700;color:#1e3a5f;"><?= formatRupiah($d['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f9fafb;">
                                <td colspan="4" style="font-weight:700;text-align:right;padding:14px 16px;">TOTAL</td>
                                <td style="font-weight:800;font-size:15px;color:#1e3a5f;padding:14px 16px;"><?= formatRupiah($transaksi['total_harga']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
