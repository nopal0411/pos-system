<?php
/**
 * LAPORAN/CETAK.PHP - Versi Cetak Laporan (Print-Friendly)
 * POS System Toko Fenny Grosir
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$jenis  = $_GET['jenis']  ?? 'harian';
$dari   = $_GET['dari']   ?? date('Y-m-d');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$bulan  = $_GET['bulan']  ?? date('Y-m');
$tahun  = $_GET['tahun']  ?? date('Y');

// Query sama dengan laporan/index.php
if ($jenis === 'harian') {
    $periodeLabel = 'Laporan Harian: ' . date('d M Y', strtotime($dari)) . ' s/d ' . date('d M Y', strtotime($sampai));
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir,
               (SELECT COUNT(*) FROM detail_transaksi dt WHERE dt.id_transaksi = t.id_transaksi) as jumlah_item
        FROM transaksi t JOIN users u ON t.id_user = u.id_user
        WHERE DATE(t.tanggal) BETWEEN ? AND ? ORDER BY t.tanggal
    ");
    $stmt->execute([$dari, $sampai]);
} elseif ($jenis === 'bulanan') {
    $tglAwal  = $bulan . '-01';
    $tglAkhir = date('Y-m-t', strtotime($tglAwal));
    $periodeLabel = 'Laporan Bulanan: ' . date('F Y', strtotime($tglAwal));
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir,
               (SELECT COUNT(*) FROM detail_transaksi dt WHERE dt.id_transaksi = t.id_transaksi) as jumlah_item
        FROM transaksi t JOIN users u ON t.id_user = u.id_user
        WHERE DATE(t.tanggal) BETWEEN ? AND ? ORDER BY t.tanggal
    ");
    $stmt->execute([$tglAwal, $tglAkhir]);
} else {
    $periodeLabel = 'Laporan Tahunan: ' . $tahun;
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir,
               (SELECT COUNT(*) FROM detail_transaksi dt WHERE dt.id_transaksi = t.id_transaksi) as jumlah_item
        FROM transaksi t JOIN users u ON t.id_user = u.id_user
        WHERE YEAR(t.tanggal) = ? ORDER BY t.tanggal
    ");
    $stmt->execute([$tahun]);
}

$transaksiList   = $stmt->fetchAll();
$totalPendapatan = array_sum(array_column($transaksiList, 'total_harga'));
$totalTransaksi  = count($transaksiList);
$rataRata        = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= e($periodeLabel) ?> – Toko Fenny Grosir</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; padding: 20px; }
        .header { text-align: center; border-bottom: 3px double #1e3a5f; padding-bottom: 12px; margin-bottom: 16px; }
        .header h2 { font-size: 18px; font-weight: 800; color: #1e3a5f; }
        .header h3 { font-size: 14px; font-weight: 600; margin-top: 4px; }
        .header p  { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 11.5px; }
        .meta div { background: #f3f4f6; padding: 8px 14px; border-radius: 6px; }
        .meta strong { display: block; font-size: 13px; color: #1e3a5f; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead th { background: #1e3a5f; color: white; padding: 8px 10px; font-size: 11.5px; text-align: left; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        tfoot td { padding: 10px; font-weight: 800; font-size: 13px; background: #eff6ff; border-top: 2px solid #1e3a5f; }
        .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 12px; }
        .ttd { display: flex; justify-content: flex-end; margin-top: 40px; }
        .ttd-box { text-align: center; }
        .ttd-box .line { border-bottom: 1px solid #000; width: 180px; margin-top: 50px; }
        .no-print { text-align: center; padding: 14px; background: #f0f9ff; margin-bottom: 20px; border-radius: 8px; }
        .no-print button { padding: 10px 24px; background: #1e3a5f; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 700; margin: 0 6px; }
        .no-print button.secondary { background: #6b7280; }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨️ Cetak Laporan</button>
    <button class="secondary" onclick="window.close()">✕ Tutup</button>
</div>

<!-- Header Laporan -->
<div class="header">
    <h2>TOKO FENNY GROSIR</h2>
    <h3><?= e($periodeLabel) ?></h3>
    <p>Dicetak pada: <?= date('d M Y H:i:s') ?> &bull; Oleh: <?= e($_SESSION['nama'] ?? 'Admin') ?></p>
</div>

<!-- Ringkasan -->
<div class="meta">
    <div>Total Transaksi<strong><?= number_format($totalTransaksi) ?> Transaksi</strong></div>
    <div>Total Pendapatan<strong>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></strong></div>
    <div>Rata-rata / Transaksi<strong>Rp <?= number_format($rataRata, 0, ',', '.') ?></strong></div>
</div>

<!-- Tabel Laporan -->
<?php if (empty($transaksiList)): ?>
<p style="text-align:center;padding:30px;color:#9ca3af;">Tidak ada data transaksi pada periode ini.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>No. Transaksi</th>
            <th>Tanggal & Waktu</th>
            <th>Kasir</th>
            <th>Item</th>
            <th style="text-align:right;">Total (Rp)</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($transaksiList as $i => $t): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><strong><?= e($t['no_transaksi']) ?></strong></td>
            <td><?= date('d/m/Y H:i', strtotime($t['tanggal'])) ?></td>
            <td><?= e($t['nama_kasir']) ?></td>
            <td style="text-align:center;"><?= $t['jumlah_item'] ?></td>
            <td style="text-align:right;font-weight:600;"><?= number_format($t['total_harga'], 0, ',', '.') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align:right;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

<!-- TTD -->
<div class="ttd">
    <div class="ttd-box">
        <div><?= date('d M Y') ?></div>
        <div>Penanggung Jawab,</div>
        <div class="line"></div>
        <div style="margin-top:4px;font-size:11px;">( __________________ )</div>
    </div>
</div>

<div class="footer">
    <p>Laporan ini dibuat secara otomatis oleh Sistem POS Toko Fenny Grosir</p>
    <p>Implementasi Algoritma Linear Search &bull; &copy; <?= date('Y') ?></p>
</div>

<script>
window.onload = function () { setTimeout(() => window.print(), 300); };
</script>
</body>
</html>
