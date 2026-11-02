<?php
/**
 * DASHBOARD/INDEX.PHP - Halaman Utama Dashboard
 * POS System Toko Fenny Grosir
 * Menampilkan statistik, grafik penjualan, dan ringkasan aktivitas
 */
$pageTitle    = 'Dashboard';
$pageSubtitle = 'Selamat datang, ' . ($_SESSION['nama'] ?? 'User');
require_once __DIR__ . '/../../includes/header.php';

// Ambil data statistik
$totalProduk    = countProduk();
$totalKategori  = countKategori();
$transaksiHariIni = countTransaksiHariIni();
$pendapatanHariIni = getTotalPendapatanHariIni();
$pendapatanBulanIni = getTotalPendapatanBulanIni();
$produkTerlaris = getProdukTerlaris(5);
$transaksiTerbaru = getTransaksiTerbaru(5);

// Data grafik 7 hari terakhir
$grafikData = getGrafikPenjualan7Hari();
$grafikLabels = [];
$grafikValues = [];

// Buat array lengkap 7 hari (isi 0 jika tidak ada transaksi)
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $grafikLabels[] = date('d/m', strtotime($tgl));
    $found = 0;
    foreach ($grafikData as $row) {
        if ($row['tgl'] === $tgl) {
            $found = (float)$row['total'];
            break;
        }
    }
    $grafikValues[] = $found;
}

// Produk dengan stok menipis (stok < 10)
$stmt = $pdo->query("SELECT nama_produk, stok FROM produk WHERE stok < 10 ORDER BY stok ASC LIMIT 5");
$stokMenipis = $stmt->fetchAll();
?>

<!-- ============================================================
     STAT CARDS
     ============================================================ -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-box-open"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Produk</div>
                <div class="stat-value"><?= number_format($totalProduk) ?></div>
                <div class="stat-sub">Item terdaftar di sistem</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-tags"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Kategori</div>
                <div class="stat-value"><?= number_format($totalKategori) ?></div>
                <div class="stat-sub">Kategori produk aktif</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card yellow">
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <div class="stat-label">Transaksi Hari Ini</div>
                <div class="stat-value"><?= number_format($transaksiHariIni) ?></div>
                <div class="stat-sub"><?= date('d M Y') ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <div class="stat-label">Pendapatan Hari Ini</div>
                <div class="stat-value" style="font-size:16px;"><?= formatRupiah($pendapatanHariIni) ?></div>
                <div class="stat-sub">Bulan ini: <?= formatRupiah($pendapatanBulanIni) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     GRAFIK PENJUALAN + PRODUK TERLARIS
     ============================================================ -->
<div class="row g-3 mb-4">
    <!-- Grafik 7 Hari -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-chart-area"></i>
                    Grafik Penjualan 7 Hari Terakhir
                </div>
                <a href="<?= BASE_URL ?>/modules/laporan/index.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-external-link-alt"></i> Laporan Lengkap
                </a>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-fire"></i>
                    Produk Terlaris
                </div>
            </div>
            <div class="card-body" style="padding-top:12px;">
                <?php if (empty($produkTerlaris)): ?>
                <div class="empty-state" style="padding:30px;">
                    <i class="fas fa-box" style="font-size:30px;"></i>
                    <p>Belum ada data penjualan</p>
                </div>
                <?php else: ?>
                <?php foreach ($produkTerlaris as $i => $p): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <div style="width:28px;height:28px;background:<?= ['#dbeafe','#d1fae5','#fef3c7','#ede9fe','#fee2e2'][$i % 5] ?>;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:<?= ['#1e40af','#065f46','#92400e','#5b21b6','#991b1b'][$i % 5] ?>;flex-shrink:0;"><?= $i+1 ?></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12.5px;font-weight:600;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($p['nama_produk']) ?></div>
                        <div style="font-size:11.5px;color:#6b7280;"><?= number_format($p['total_terjual']) ?> terjual</div>
                    </div>
                    <div style="font-size:12px;font-weight:700;color:#1e3a5f;white-space:nowrap;"><?= formatRupiah($p['total_pendapatan']) ?></div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     TRANSAKSI TERBARU + STOK MENIPIS
     ============================================================ -->
<div class="row g-3">
    <!-- Transaksi Terbaru -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-clock"></i>
                    Transaksi Terbaru
                </div>
                <a href="<?= BASE_URL ?>/modules/riwayat/index.php" class="btn btn-sm btn-secondary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body" style="padding:0;">
                <?php if (empty($transaksiTerbaru)): ?>
                <div class="empty-state"><i class="fas fa-receipt"></i><p>Belum ada transaksi</p></div>
                <?php else: ?>
                <div class="table-wrapper">
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transaksiTerbaru as $t): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>/modules/riwayat/detail.php?id=<?= $t['id_transaksi'] ?>"
                                       style="color:#2c5f9e;font-weight:600;font-size:12.5px;text-decoration:none;">
                                        <?= e($t['no_transaksi']) ?>
                                    </a>
                                </td>
                                <td style="font-size:12.5px;"><?= e($t['nama_kasir']) ?></td>
                                <td style="font-weight:700;color:#1e3a5f;font-size:12.5px;"><?= formatRupiah($t['total_harga']) ?></td>
                                <td style="font-size:11.5px;color:#6b7280;">
                                    <?= date('d/m H:i', strtotime($t['tanggal'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="color:#ef4444;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Stok Menipis
                </div>
                <a href="<?= BASE_URL ?>/modules/produk/index.php" class="btn btn-sm btn-secondary">
                    Kelola Produk
                </a>
            </div>
            <div class="card-body" style="padding:0;">
                <?php if (empty($stokMenipis)): ?>
                <div class="empty-state" style="padding:30px;">
                    <i class="fas fa-check-circle" style="color:#10b981;font-size:30px;"></i>
                    <p style="color:#10b981;font-weight:600;">Semua stok aman</p>
                </div>
                <?php else: ?>
                <?php foreach ($stokMenipis as $s): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 22px;border-bottom:1px solid #f3f4f6;">
                    <div style="font-size:13px;font-weight:500;color:#374151;"><?= e($s['nama_produk']) ?></div>
                    <span class="badge <?= $s['stok'] == 0 ? 'badge-danger' : 'badge-warning' ?>">
                        <?= $s['stok'] == 0 ? 'Habis' : 'Sisa ' . $s['stok'] ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$extraScript = '
<script>
// ============================================================
// GRAFIK PENJUALAN - Chart.js
// ============================================================
const ctx = document.getElementById("salesChart").getContext("2d");
const salesChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: ' . json_encode($grafikLabels) . ',
        datasets: [{
            label: "Penjualan (Rp)",
            data: ' . json_encode($grafikValues) . ',
            backgroundColor: "rgba(44, 95, 158, 0.15)",
            borderColor: "#2c5f9e",
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
            fill: true,
            tension: 0.4,
            type: "bar"
        }, {
            label: "Trend",
            data: ' . json_encode($grafikValues) . ',
            borderColor: "#06b6d4",
            borderWidth: 2.5,
            pointBackgroundColor: "#06b6d4",
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: false,
            type: "line",
            tension: 0.4,
            yAxisID: "y"
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: "index", intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return " Rp " + ctx.parsed.y.toLocaleString("id-ID");
                    }
                },
                backgroundColor: "#1e3a5f",
                titleColor: "#fff",
                bodyColor: "#93c5fd",
                padding: 12,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: "rgba(0,0,0,0.04)" },
                ticks: {
                    font: { family: "Inter", size: 11 },
                    color: "#9ca3af",
                    callback: function(val) {
                        if (val >= 1000000) return "Rp " + (val/1000000).toFixed(1) + "jt";
                        if (val >= 1000) return "Rp " + (val/1000).toFixed(0) + "rb";
                        return "Rp " + val;
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: "Inter", size: 12 }, color: "#6b7280" }
            }
        }
    }
});
</script>';
require_once __DIR__ . '/../../includes/footer.php';
?>
