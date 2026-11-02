# POS System – Toko Fenny Grosir

> **Skripsi:** Implementasi Point of Sales (POS) System Sebagai Strategi Pengembangan Usaha pada UMKM Toko Fenny Grosir Menggunakan Algoritma Linear Search

---

## 🚀 Cara Instalasi (Laragon)

### Langkah 1 – Pastikan Laragon Berjalan
- Buka Laragon, klik **Start All**
- Pastikan Apache dan MySQL aktif (hijau)

### Langkah 2 – Jalankan Setup Otomatis
Buka browser dan akses:
```
http://localhost/SkripsiFibril/pos_system/setup.php
```
Klik tombol **"Mulai Instalasi Sekarang"** → sistem akan membuat database, tabel, dan data contoh otomatis.

### Langkah 3 – Akses Sistem
```
http://localhost/SkripsiFibril/pos_system/
```

---

## 🔐 Akun Login

| Role  | Username | Password   |
|-------|----------|------------|
| Admin | `admin`  | `password` |
| Kasir | `kasir`  | `kasir123` |

---

## 📁 Struktur Folder

```
pos_system/
├── assets/
│   ├── css/style.css          ← Design System & UI
│   ├── js/main.js             ← JavaScript Utilities
│   └── img/produk/            ← Upload Gambar Produk
├── config/
│   └── database.php           ← Koneksi PDO MySQL
├── database/
│   └── pos_fenny.sql          ← Script SQL (alternatif manual)
├── includes/
│   ├── header.php             ← Sidebar + Topbar Layout
│   ├── footer.php             ← JS Scripts Footer
│   └── functions.php          ← Helper + ALGORITMA LINEAR SEARCH
├── modules/
│   ├── auth/
│   │   ├── login.php          ← Halaman Login
│   │   └── logout.php         ← Proses Logout
│   ├── dashboard/
│   │   └── index.php          ← Dashboard + Grafik
│   ├── kategori/
│   │   ├── index.php          ← Daftar Kategori
│   │   ├── tambah.php         ← Proses Tambah
│   │   ├── edit.php           ← Proses Edit
│   │   └── hapus.php          ← Proses Hapus
│   ├── produk/
│   │   ├── index.php          ← Daftar Produk + Linear Search UI
│   │   ├── tambah.php         ← Form Tambah Produk
│   │   ├── edit.php           ← Form Edit Produk
│   │   ├── hapus.php          ← Proses Hapus + Hapus Gambar
│   │   └── search.php         ← API Linear Search (JSON)
│   ├── transaksi/
│   │   ├── index.php          ← Kasir / POS Interface
│   │   ├── simpan.php         ← API Simpan Transaksi (JSON)
│   │   ├── cetak_struk.php    ← Struk Thermal (80mm)
│   │   └── get_no_transaksi.php ← Generate No. Transaksi
│   ├── riwayat/
│   │   ├── index.php          ← Daftar Riwayat + Filter Tanggal
│   │   └── detail.php         ← Detail Transaksi
│   ├── laporan/
│   │   ├── index.php          ← Laporan Harian/Bulanan/Tahunan
│   │   └── cetak.php          ← Print-Friendly Report
│   └── user/
│       ├── index.php          ← Manajemen User (Admin Only)
│       ├── tambah.php         ← Proses Tambah User
│       ├── edit.php           ← Proses Edit User
│       └── hapus.php          ← Proses Hapus User
├── .htaccess                  ← Security & Access Control
├── index.php                  ← Entry Point (Redirect)
└── setup.php                  ← One-Click Installer
```

---

## 🔍 Implementasi Algoritma Linear Search

Algoritma Linear Search diimplementasikan di dua tempat:

### 1. PHP (Server-Side) — `includes/functions.php`
```php
function linearSearch(array $dataProduk, string $keyword): array {
    $hasilPencarian = [];
    $keywordLower = strtolower(trim($keyword));

    // Iterasi setiap elemen dari index 0 hingga n-1 → O(n)
    for ($i = 0; $i < count($dataProduk); $i++) {
        $namaProdukLower = strtolower($dataProduk[$i]['nama_produk']);

        // Perbandingan: apakah keyword ada dalam nama produk?
        if (strpos($namaProdukLower, $keywordLower) !== false) {
            $hasilPencarian[] = $dataProduk[$i]; // ✅ Ditemukan
        }
        // ❌ Tidak ditemukan: lanjut ke elemen berikutnya
    }
    return $hasilPencarian;
}
```

### 2. JavaScript (Client-Side) — `modules/produk/index.php`
```javascript
function linearSearchJS(data, keyword) {
    const hasil = [];
    const kw = keyword.toLowerCase();

    for (let i = 0; i < data.length; i++) {      // O(n)
        if (data[i].nama_produk.toLowerCase().includes(kw)) {
            hasil.push(data[i]);
        }
    }
    return hasil;
}
```

### Kompleksitas Algoritma

| Kasus       | Kompleksitas Waktu | Keterangan                          |
|-------------|-------------------|-------------------------------------|
| Best Case   | O(1)              | Ditemukan di posisi pertama         |
| Average Case| O(n/2)            | Rata-rata setengah dari total data  |
| Worst Case  | O(n)              | Ditemukan di akhir atau tidak ada   |
| Ruang       | O(1)              | Tidak memerlukan memori tambahan    |

---

## 🗄️ Relasi Database (ERD)

```
users (1) ──────────────── (N) transaksi
                                    │
                                    │ (1)
                                    │
                               (N) detail_transaksi (N) ─── (1) produk
                                                                   │
                                                                   │ (N)
                                                                   │
                                                              (1) kategori
```

---

## 🛠️ Teknologi yang Digunakan

| Teknologi     | Versi   | Kegunaan                      |
|---------------|---------|-------------------------------|
| PHP Native    | 8.x     | Backend & logika bisnis       |
| MySQL         | 8.x     | Database relasional           |
| Bootstrap     | 5.3     | Responsive UI framework       |
| Font Awesome  | 6.5     | Icon library                  |
| DataTables    | 1.13    | Tabel interaktif              |
| Chart.js      | 4.4     | Grafik penjualan              |
| jQuery        | 3.7     | DOM manipulation & AJAX       |
| Google Fonts  | Inter   | Typography premium            |

---

## 📋 Fitur Lengkap

- ✅ Login & Logout dengan Session
- ✅ Dashboard dengan statistik real-time
- ✅ Grafik penjualan 7 hari & bulanan (Chart.js)
- ✅ Manajemen Kategori (CRUD)
- ✅ Manajemen Produk (CRUD + Upload Gambar)
- ✅ **Algoritma Linear Search** pada pencarian produk
- ✅ Transaksi POS dengan keranjang belanja
- ✅ Hitung kembalian otomatis
- ✅ Cetak struk thermal (80mm)
- ✅ Riwayat transaksi dengan filter tanggal
- ✅ Laporan Harian, Bulanan, Tahunan
- ✅ Export/Cetak laporan
- ✅ Manajemen User dengan Role (Admin & Kasir)
- ✅ Stok otomatis berkurang saat transaksi
- ✅ Peringatan stok menipis di dashboard
- ✅ Responsive (Desktop, Tablet, Mobile)
- ✅ Keamanan: PDO Prepared Statements, Password Hash, XSS Prevention

---

## 👨‍💻 Dibuat untuk Skripsi

**Judul:** Implementasi Point of Sales (POS) System Sebagai Strategi Pengembangan Usaha pada UMKM Toko Fenny Grosir Menggunakan Algoritma Linear Search

**Program Studi:** Teknik Informatika  
**Tahun:** 2026
