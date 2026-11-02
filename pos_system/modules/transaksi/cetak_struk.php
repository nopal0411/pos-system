<?php
/**
 * TRANSAKSI/CETAK_STRUK.PHP
 * Halaman cetak struk standalone (versi printable)
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect(BASE_URL . '/modules/riwayat/index.php'); }

$transaksi = getTransaksiById($id);
$detail    = getDetailTransaksi($id);
if (!$transaksi) { redirect(BASE_URL . '/modules/riwayat/index.php'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk <?= e($transaksi['no_transaksi']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm;
            padding: 10px;
            background: white;
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .separator { border-top: 1px dashed #000; margin: 8px 0; }
        .separator-solid { border-top: 2px solid #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 3px 0; vertical-align: top; }
        .item-name { max-width: 130px; word-wrap: break-word; }
        .amount { text-align: right; }
        .total-row td { font-weight: bold; border-top: 1px dashed #000; padding-top: 5px; }
        @media print {
            body { width: 80mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<!-- Tombol cetak (tersembunyi saat print) -->
<div class="no-print" style="padding:10px;text-align:center;background:#f3f4f6;margin-bottom:10px;border-radius:6px;">
    <button onclick="window.print()" style="padding:8px 20px;background:#2c5f9e;color:white;border:none;border-radius:6px;cursor:pointer;font-weight:bold;">
        🖨️ Cetak Struk
    </button>
    <button onclick="window.close()" style="padding:8px 16px;background:#6b7280;color:white;border:none;border-radius:6px;cursor:pointer;margin-left:8px;">
        ✕ Tutup
    </button>
</div>

<!-- ======= STRUK ======= -->
<div class="center bold" style="font-size:14px;">TOKO FENNY GROSIR</div>
<div class="center" style="font-size:10px;">Jl. Contoh No. 1, Kota</div>
<div class="center" style="font-size:10px;">Telp: 08xx-xxxx-xxxx</div>

<div class="separator-solid"></div>

<table>
    <tr><td>No. Transaksi</td><td>:</td><td class="bold"><?= e($transaksi['no_transaksi']) ?></td></tr>
    <tr><td>Tanggal</td><td>:</td><td><?= date('d/m/Y', strtotime($transaksi['tanggal'])) ?></td></tr>
    <tr><td>Waktu</td><td>:</td><td><?= date('H:i:s', strtotime($transaksi['tanggal'])) ?></td></tr>
    <tr><td>Kasir</td><td>:</td><td><?= e($transaksi['nama_kasir']) ?></td></tr>
</table>

<div class="separator"></div>
<div class="bold">ITEM BELANJA:</div>
<div class="separator"></div>

<table>
    <?php foreach ($detail as $d): ?>
    <tr>
        <td colspan="3" class="item-name"><?= e($d['nama_produk']) ?></td>
    </tr>
    <tr>
        <td style="padding-left:8px;"><?= $d['jumlah'] ?> x <?= number_format($d['harga'], 0, ',', '.') ?></td>
        <td></td>
        <td class="amount"><?= number_format($d['subtotal'], 0, ',', '.') ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td colspan="2" class="bold">TOTAL</td>
        <td class="amount bold">Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td colspan="2">Metode Bayar</td>
        <td class="amount"><?= strtoupper($transaksi['metode_bayar'] ?? 'CASH') ?></td>
    </tr>
    <tr>
        <td colspan="2">Uang Bayar</td>
        <td class="amount">Rp <?= number_format($transaksi['uang_bayar'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td colspan="2" class="bold">Kembalian</td>
        <td class="amount bold">Rp <?= number_format($transaksi['kembalian'], 0, ',', '.') ?></td>
    </tr>
</table>

<div class="separator-solid"></div>

<div class="center" style="font-size:11px;">Terima kasih telah berbelanja!</div>
<div class="center" style="font-size:10px;">Barang yang sudah dibeli</div>
<div class="center" style="font-size:10px;">tidak dapat dikembalikan.</div>
<div class="center" style="margin-top:8px;font-size:10px;">*** <?= APP_NAME ?> ***</div>

<script>
// Auto print saat halaman terbuka
window.onload = function() {
    setTimeout(function() { window.print(); }, 500);
};
</script>
</body>
</html>
