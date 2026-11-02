<?php
/**
 * RIWAYAT/INDEX.PHP - Daftar Semua Riwayat Transaksi
 */
$pageTitle    = 'Riwayat Transaksi';
$pageSubtitle = 'Seluruh riwayat penjualan';
require_once __DIR__ . '/../../includes/header.php';

// Filter tanggal
$dari  = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT t.*, u.nama as nama_kasir,
           COUNT(dt.id_detail) as jumlah_item
    FROM transaksi t
    JOIN users u ON t.id_user = u.id_user
    LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
    WHERE DATE(t.tanggal) BETWEEN ? AND ?
    GROUP BY t.id_transaksi
    ORDER BY t.tanggal DESC
");
$stmt->execute([$dari, $sampai]);
$transaksiList = $stmt->fetchAll();

$totalPendapatan = array_sum(array_column($transaksiList, 'total_harga'));
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-1"><i class="fas fa-history text-primary me-2"></i>Riwayat Transaksi</h1>
        <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a> / <span>Riwayat</span>
        </nav>
    </div>
</div>

<!-- Filter Tanggal -->
<div class="card mb-4">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label" style="margin-bottom:4px;">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= e($dari) ?>">
            </div>
            <div>
                <label class="form-label" style="margin-bottom:4px;">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= e($sampai) ?>">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
            </div>
            <div style="margin-left:auto;text-align:right;">
                <div style="font-size:12px;color:#6b7280;"><?= count($transaksiList) ?> transaksi</div>
                <div style="font-size:16px;font-weight:800;color:#1e3a5f;"><?= formatRupiah($totalPendapatan) ?></div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-list"></i> Daftar Transaksi</div>
        <span class="badge badge-primary"><?= count($transaksiList) ?> Transaksi</span>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tabelRiwayat" class="table" style="width:100%">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal & Waktu</th>
                        <th>Kasir</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Uang Bayar</th>
                        <th>Kembalian</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($transaksiList as $i => $t): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <span style="font-weight:700;color:#1e3a5f;font-size:12.5px;"><?= e($t['no_transaksi']) ?></span>
                        </td>
                        <td style="font-size:12.5px;">
                            <?= date('d M Y', strtotime($t['tanggal'])) ?>
                            <br><small style="color:#9ca3af;"><?= date('H:i', strtotime($t['tanggal'])) ?></small>
                        </td>
                        <td><?= e($t['nama_kasir']) ?></td>
                        <td><span class="badge badge-gray"><?= $t['jumlah_item'] ?> item</span></td>
                        <td style="font-weight:700;color:#1e3a5f;"><?= formatRupiah($t['total_harga']) ?></td>
                        <td><?= formatRupiah($t['uang_bayar']) ?></td>
                        <td style="color:#10b981;font-weight:600;"><?= formatRupiah($t['kembalian']) ?></td>
                        <td style="text-align:center;white-space:nowrap;">
                            <a href="detail.php?id=<?= $t['id_transaksi'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$extraScript = '<script>$(document).ready(function(){ initDataTable("#tabelRiwayat", {order:[[2,"desc"]]}); });</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
