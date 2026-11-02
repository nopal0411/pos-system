<?php
/**
 * LAPORAN/INDEX.PHP - Laporan Penjualan
 * Harian, Bulanan, Tahunan + Export Print
 */
$pageTitle    = 'Laporan Penjualan';
$pageSubtitle = 'Analisis data penjualan toko';
require_once __DIR__ . '/../../includes/header.php';

// Parameter filter
$jenis  = $_GET['jenis'] ?? 'harian';
$dari   = $_GET['dari']  ?? date('Y-m-d');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$bulan  = $_GET['bulan'] ?? date('Y-m');
$tahun  = $_GET['tahun'] ?? date('Y');

// Query berdasarkan jenis laporan
$transaksiList = [];
$periodeLabel  = '';

if ($jenis === 'harian') {
    $periodeLabel = 'Harian: ' . date('d M Y', strtotime($dari)) . ' s/d ' . date('d M Y', strtotime($sampai));
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir,
               (SELECT COUNT(*) FROM detail_transaksi dt WHERE dt.id_transaksi = t.id_transaksi) as jumlah_item
        FROM transaksi t JOIN users u ON t.id_user = u.id_user
        WHERE DATE(t.tanggal) BETWEEN ? AND ?
        ORDER BY t.tanggal DESC
    ");
    $stmt->execute([$dari, $sampai]);

} elseif ($jenis === 'bulanan') {
    $tglAwal = $bulan . '-01';
    $tglAkhir = date('Y-m-t', strtotime($tglAwal));
    $periodeLabel = 'Bulanan: ' . date('F Y', strtotime($tglAwal));
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir,
               (SELECT COUNT(*) FROM detail_transaksi dt WHERE dt.id_transaksi = t.id_transaksi) as jumlah_item
        FROM transaksi t JOIN users u ON t.id_user = u.id_user
        WHERE DATE(t.tanggal) BETWEEN ? AND ?
        ORDER BY t.tanggal DESC
    ");
    $stmt->execute([$tglAwal, $tglAkhir]);

} elseif ($jenis === 'tahunan') {
    $periodeLabel = 'Tahunan: ' . $tahun;
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as nama_kasir,
               (SELECT COUNT(*) FROM detail_transaksi dt WHERE dt.id_transaksi = t.id_transaksi) as jumlah_item
        FROM transaksi t JOIN users u ON t.id_user = u.id_user
        WHERE YEAR(t.tanggal) = ?
        ORDER BY t.tanggal DESC
    ");
    $stmt->execute([$tahun]);
}

$transaksiList   = $stmt->fetchAll();
$totalPendapatan = array_sum(array_column($transaksiList, 'total_harga'));
$totalTransaksi  = count($transaksiList);
$rataRata        = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

// Grafik per hari/bulan untuk tahunan
$grafikBulanan = [];
if ($jenis === 'tahunan') {
    $stmtGrafik = $pdo->prepare("
        SELECT MONTH(tanggal) as bln, SUM(total_harga) as total
        FROM transaksi WHERE YEAR(tanggal) = ?
        GROUP BY MONTH(tanggal) ORDER BY bln
    ");
    $stmtGrafik->execute([$tahun]);
    $rows = $stmtGrafik->fetchAll();
    $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    for ($m = 1; $m <= 12; $m++) {
        $found = 0;
        foreach ($rows as $r) { if ((int)$r['bln'] === $m) { $found = $r['total']; break; } }
        $grafikBulanan[] = ['label' => $namaBulan[$m-1], 'total' => $found];
    }
}
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-1"><i class="fas fa-chart-bar text-primary me-2"></i>Laporan Penjualan</h1>
        <nav class="breadcrumb" style="margin:0;padding:0;background:none;">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a> / <span>Laporan</span>
        </nav>
    </div>
    <button class="btn btn-primary" onclick="bukaCetak()">
        <i class="fas fa-print"></i> Cetak / Export
    </button>
</div>

<!-- Filter Laporan -->
<div class="card mb-4">
    <div class="card-body" style="padding:20px;">
        <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label">Jenis Laporan</label>
                <select name="jenis" class="form-control" onchange="toggleFilter(this.value)" id="jenisSelect">
                    <option value="harian"  <?= $jenis==='harian'  ? 'selected':'' ?>>Harian</option>
                    <option value="bulanan" <?= $jenis==='bulanan' ? 'selected':'' ?>>Bulanan</option>
                    <option value="tahunan" <?= $jenis==='tahunan' ? 'selected':'' ?>>Tahunan</option>
                </select>
            </div>

            <!-- Filter Harian -->
            <div id="filterHarian" <?= $jenis!=='harian' ? 'style="display:none;"':'' ?>>
                <label class="form-label">Dari</label>
                <input type="date" name="dari" class="form-control" value="<?= e($dari) ?>">
            </div>
            <div id="filterHarian2" <?= $jenis!=='harian' ? 'style="display:none;"':'' ?>>
                <label class="form-label">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="<?= e($sampai) ?>">
            </div>

            <!-- Filter Bulanan -->
            <div id="filterBulanan" <?= $jenis!=='bulanan' ? 'style="display:none;"':'' ?>>
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="<?= e($bulan) ?>">
            </div>

            <!-- Filter Tahunan -->
            <div id="filterTahunan" <?= $jenis!=='tahunan' ? 'style="display:none;"':'' ?>>
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-control">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $tahun==$y?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>
</div>

<!-- Stat Cards Laporan -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value"><?= number_format($totalTransaksi) ?></div>
                <div class="stat-sub"><?= $periodeLabel ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-coins"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value" style="font-size:18px;"><?= formatRupiah($totalPendapatan) ?></div>
                <div class="stat-sub">Seluruh transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card yellow">
            <div class="stat-icon"><i class="fas fa-calculator"></i></div>
            <div class="stat-info">
                <div class="stat-label">Rata-rata / Transaksi</div>
                <div class="stat-value" style="font-size:18px;"><?= formatRupiah($rataRata) ?></div>
                <div class="stat-sub">Per transaksi</div>
            </div>
        </div>
    </div>
</div>

<?php if ($jenis === 'tahunan' && !empty($grafikBulanan)): ?>
<!-- Grafik Tahunan -->
<div class="card mb-4">
    <div class="card-header"><div class="card-title"><i class="fas fa-chart-line"></i> Grafik Penjualan Tahun <?= $tahun ?></div></div>
    <div class="card-body"><canvas id="laporanChart" height="80"></canvas></div>
</div>
<?php endif; ?>

<!-- Tabel Laporan -->
<div class="card" id="tabelLaporan">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-table"></i> Data Laporan – <?= $periodeLabel ?></div>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($transaksiList)): ?>
        <div class="empty-state" style="padding:50px;">
            <i class="fas fa-chart-bar"></i>
            <h5>Tidak Ada Data</h5>
            <p>Tidak ada transaksi pada periode ini.</p>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabelLaporanDT" style="width:100%">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Item</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($transaksiList as $i => $t): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td style="font-weight:600;color:#1e3a5f;"><?= e($t['no_transaksi']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($t['tanggal'])) ?></td>
                        <td><?= e($t['nama_kasir']) ?></td>
                        <td><span class="badge badge-gray"><?= $t['jumlah_item'] ?> item</span></td>
                        <td style="font-weight:700;color:#1e3a5f;"><?= formatRupiah($t['total_harga']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f9fafb;font-weight:800;">
                        <td colspan="5" style="text-align:right;padding:14px 16px;">TOTAL KESELURUHAN</td>
                        <td style="color:#1e3a5f;font-size:15px;padding:14px 16px;"><?= formatRupiah($totalPendapatan) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$grafikJSON = json_encode($grafikBulanan);
$extraScript = <<<SCRIPT
<script>
// Filter toggle
function toggleFilter(val) {
    ['filterHarian','filterHarian2'].forEach(id => document.getElementById(id).style.display = val === 'harian' ? '' : 'none');
    document.getElementById('filterBulanan').style.display = val === 'bulanan' ? '' : 'none';
    document.getElementById('filterTahunan').style.display = val === 'tahunan' ? '' : 'none';
}

// Buka halaman cetak laporan
function bukaCetak() {
    const jenis  = document.getElementById('jenisSelect').value;
    const dari   = document.querySelector('[name="dari"]')?.value || '';
    const sampai = document.querySelector('[name="sampai"]')?.value || '';
    const bulan  = document.querySelector('[name="bulan"]')?.value || '';
    const tahun  = document.querySelector('[name="tahun"]')?.value || '';
    const url = `cetak.php?jenis=${jenis}&dari=${dari}&sampai=${sampai}&bulan=${bulan}&tahun=${tahun}`;
    window.open(url, '_blank', 'width=900,height=700');
}

$(document).ready(function() {
    if ($('#tabelLaporanDT').length) initDataTable('#tabelLaporanDT');
});

// Grafik tahunan
const grafikData = {$grafikJSON};
if (grafikData.length > 0 && document.getElementById('laporanChart')) {
    new Chart(document.getElementById('laporanChart'), {
        type: 'bar',
        data: {
            labels: grafikData.map(d => d.label),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: grafikData.map(d => d.total),
                backgroundColor: 'rgba(44,95,158,0.15)',
                borderColor: '#2c5f9e',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID') } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => v >= 1000000 ? 'Rp ' + (v/1000000).toFixed(1) + 'jt' : 'Rp ' + (v/1000).toFixed(0) + 'rb' } },
                x: { grid: { display: false } }
            }
        }
    });
}
</script>

<style>
@media print {
    .sidebar, .topbar, .page-header .btn, form, #tabelLaporanDT_wrapper .dataTables_filter,
    #tabelLaporanDT_wrapper .dataTables_length, #tabelLaporanDT_wrapper .dataTables_info,
    #tabelLaporanDT_wrapper .dataTables_paginate, .card-header .btn { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { font-size: 12px; }
    .stat-card { border: 1px solid #ddd !important; }
}
</style>
SCRIPT;
require_once __DIR__ . '/../../includes/footer.php';
?>
