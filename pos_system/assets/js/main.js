/**
 * ============================================================
 * MAIN.JS - POS System Toko Fenny Grosir
 * Global JavaScript Functions
 * ============================================================
 */

// ============================================================
// SIDEBAR TOGGLE (Mobile Responsive)
// ============================================================
document.addEventListener('DOMContentLoaded', function () {

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // ============================================================
    // PROFILE DROPDOWN TOGGLE
    // ============================================================
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu   = document.getElementById('profileMenu');

    if (profileToggle && profileMenu) {
        // Buka/tutup saat klik tombol avatar
        profileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = profileMenu.classList.contains('show');
            profileMenu.classList.toggle('show');
            profileToggle.setAttribute('aria-expanded', !isOpen);
        });

        // Tutup dropdown saat klik di luar area
        document.addEventListener('click', function (e) {
            if (!profileToggle.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('show');
                profileToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Tutup dropdown saat tekan Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                profileMenu.classList.remove('show');
                profileToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }


    // ============================================================
    // LIVE CLOCK pada Topbar
    // ============================================================
    function updateClock() {
        const now = new Date();
        const timeEl = document.getElementById('topbar-time');
        const dateEl = document.getElementById('topbar-date');

        if (timeEl) {
            timeEl.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ============================================================
    // AUTO DISMISS ALERT setelah 4 detik
    // ============================================================
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // ============================================================
    // CONFIRM DELETE
    // ============================================================
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = this.getAttribute('data-confirm') || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // FORMAT INPUT ANGKA (Rupiah) secara real-time
    // ============================================================
    document.querySelectorAll('[data-rupiah]').forEach(function (input) {
        input.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '');
            this.value = formatRupiah(val);
        });
    });

    // ============================================================
    // PREVIEW GAMBAR sebelum Upload
    // ============================================================
    const imgInput = document.getElementById('gambar');
    const imgPreview = document.getElementById('img-preview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// ============================================================
// FUNGSI FORMAT RUPIAH (JavaScript)
// ============================================================
function formatRupiah(angka) {
    const num = parseInt(String(angka).replace(/\D/g, '')) || 0;
    return 'Rp ' + num.toLocaleString('id-ID');
}

function parseRupiah(str) {
    return parseInt(String(str).replace(/\D/g, '')) || 0;
}

// ============================================================
// FUNGSI TOAST NOTIFICATION
// ============================================================
function showToast(message, type = 'success') {
    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    const colors = {
        success: '#10b981',
        danger: '#ef4444',
        warning: '#f59e0b',
        info: '#3a74b8'
    };

    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 24px; right: 24px; z-index: 9999;
        background: white; color: #1f2937; padding: 14px 18px;
        border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: flex; align-items: center; gap: 10px;
        font-family: 'Inter', sans-serif; font-size: 13.5px; font-weight: 500;
        border-left: 4px solid ${colors[type]};
        animation: slideInToast 0.3s ease; max-width: 320px;
    `;

    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInToast {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    toast.innerHTML = `
        <i class="fas ${icons[type]}" style="color:${colors[type]};font-size:18px;flex-shrink:0;"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'all 0.3s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ============================================================
// DATATABLES DEFAULT CONFIG
// ============================================================
function initDataTable(selector, options = {}) {
    const defaults = {
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(disaring dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            paginate: {
                first: '«', last: '»', next: '›', previous: '‹'
            }
        },
        pageLength: 10,
        responsive: true
    };
    return $(selector).DataTable({ ...defaults, ...options });
}
