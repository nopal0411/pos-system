<?php
$pageTitle = 'Pengaturan Sistem';
require_once __DIR__ . '/../../includes/header.php';

// Pastikan hanya admin yang bisa mengakses
requireAdmin();

// Ambil semua pengaturan
$stmt = $pdo->query("SELECT * FROM pengaturan ORDER BY id ASC");
$pengaturan = $stmt->fetchAll();

// Fungsi helper untuk mencari nilai berdasarkan kunci
function getSettingValue($kunci, $pengaturan) {
    foreach ($pengaturan as $p) {
        if ($p['kunci'] === $kunci) return $p['nilai'];
    }
    return 'nonaktif';
}
?>

<div class="card mb-4 shadow-sm border-0" style="border-radius:12px;overflow:hidden;">
    <div class="card-header bg-white" style="border-bottom:2px solid #f3f4f6;padding:1.25rem 1.5rem;">
        <h5 class="mb-0" style="font-weight:600;color:#1f2937;">
            <i class="fas fa-cogs text-primary me-2"></i> Konfigurasi Fitur
        </h5>
        <small class="text-muted">Aktifkan atau nonaktifkan fitur-fitur di dalam aplikasi.</small>
    </div>
    <div class="card-body" style="padding:1.5rem;">
        
        <div class="list-group list-group-flush">
            <?php foreach ($pengaturan as $p): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center" style="padding:1rem 0;border-bottom:1px solid #f3f4f6;">
                <div>
                    <h6 class="mb-1" style="font-weight:600;color:#374151;">
                        <?php
                            // Format judul agar lebih rapi (misal: fitur_pencarian_linear -> Fitur Pencarian Linear)
                            echo ucwords(str_replace('_', ' ', $p['kunci']));
                        ?>
                    </h6>
                    <small class="text-muted"><?= e($p['deskripsi']) ?></small>
                </div>
                <div>
                    <div class="form-check form-switch" style="font-size:1.5rem;margin:0;">
                        <input class="form-check-input setting-toggle" type="checkbox" role="switch" 
                               data-kunci="<?= e($p['kunci']) ?>"
                               <?= $p['nilai'] === 'aktif' ? 'checked' : '' ?>
                               style="cursor:pointer;">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.setting-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const kunci = this.dataset.kunci;
            const nilai = this.checked ? 'aktif' : 'nonaktif';
            
            // Tampilkan state loading (disable sementara)
            this.disabled = true;
            const originalColor = this.style.backgroundColor;
            
            fetch('update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `kunci=${encodeURIComponent(kunci)}&nilai=${encodeURIComponent(nilai)}`
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                if(data.success) {
                    // Berikan feedback visual kecil
                    const row = this.closest('.list-group-item');
                    row.style.backgroundColor = '#ecfdf5';
                    setTimeout(() => row.style.backgroundColor = 'transparent', 800);
                } else {
                    alert('Gagal menyimpan pengaturan: ' + (data.error || 'Unknown error'));
                    this.checked = !this.checked; // Revert
                }
            })
            .catch(err => {
                this.disabled = false;
                alert('Terjadi kesalahan koneksi.');
                this.checked = !this.checked; // Revert
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
