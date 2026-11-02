-- ============================================================
-- DATABASE: pos_fenny
-- Sistem Point of Sales - Toko Fenny Grosir
-- Dibuat untuk Skripsi: Implementasi POS System menggunakan
-- Algoritma Linear Search
-- ============================================================

CREATE DATABASE IF NOT EXISTS pos_fenny CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pos_fenny;

-- ============================================================
-- TABEL USERS
-- Menyimpan data pengguna sistem (Admin & Kasir)
-- ============================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id_user     INT(11) NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100) NOT NULL,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL KATEGORI
-- Menyimpan kategori produk
-- ============================================================
DROP TABLE IF EXISTS kategori;
CREATE TABLE kategori (
    id_kategori     INT(11) NOT NULL AUTO_INCREMENT,
    nama_kategori   VARCHAR(100) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL PRODUK
-- Menyimpan data produk yang dijual di toko
-- ============================================================
DROP TABLE IF EXISTS produk;
CREATE TABLE produk (
    id_produk       INT(11) NOT NULL AUTO_INCREMENT,
    id_kategori     INT(11) NOT NULL,
    nama_produk     VARCHAR(150) NOT NULL,
    harga_beli      DECIMAL(15,2) NOT NULL DEFAULT 0,
    harga_jual      DECIMAL(15,2) NOT NULL DEFAULT 0,
    stok            INT(11) NOT NULL DEFAULT 0,
    gambar          VARCHAR(255) DEFAULT NULL,
    deskripsi       TEXT DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_produk),
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL TRANSAKSI
-- Menyimpan header transaksi penjualan
-- ============================================================
DROP TABLE IF EXISTS transaksi;
CREATE TABLE transaksi (
    id_transaksi    INT(11) NOT NULL AUTO_INCREMENT,
    no_transaksi    VARCHAR(30) NOT NULL UNIQUE,
    tanggal         DATETIME NOT NULL,
    total_harga     DECIMAL(15,2) NOT NULL DEFAULT 0,
    uang_bayar      DECIMAL(15,2) NOT NULL DEFAULT 0,
    kembalian       DECIMAL(15,2) NOT NULL DEFAULT 0,
    id_user         INT(11) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_transaksi),
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL DETAIL TRANSAKSI
-- Menyimpan detail item dalam setiap transaksi
-- ============================================================
DROP TABLE IF EXISTS detail_transaksi;
CREATE TABLE detail_transaksi (
    id_detail       INT(11) NOT NULL AUTO_INCREMENT,
    id_transaksi    INT(11) NOT NULL,
    id_produk       INT(11) NOT NULL,
    jumlah          INT(11) NOT NULL DEFAULT 1,
    harga           DECIMAL(15,2) NOT NULL DEFAULT 0,
    subtotal        DECIMAL(15,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_detail),
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA SEED: USERS
-- Password: admin123 (admin), kasir123 (kasir) - hashed dengan password_hash()
-- ============================================================
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Kasir Fenny', 'kasir', '$2y$10$TKh8H1.PfYm2T1Y5dCb9.OHwBIcHXlJhEIlflFUv7BeFZMUCibI4K', 'kasir');

-- Note: Password untuk admin = "password", kasir = "kasir123"
-- Gunakan password_hash() di PHP untuk generate password baru

-- ============================================================
-- DATA SEED: KATEGORI
-- ============================================================
INSERT INTO kategori (nama_kategori) VALUES
('Sembako'),
('Minuman'),
('Makanan Ringan'),
('Perlengkapan Rumah'),
('Alat Tulis');

-- ============================================================
-- DATA SEED: PRODUK
-- ============================================================
INSERT INTO produk (id_kategori, nama_produk, harga_beli, harga_jual, stok, deskripsi) VALUES
(1, 'Beras Premium 5kg', 62000, 68000, 50, 'Beras putih kualitas premium, pulen dan wangi'),
(1, 'Minyak Goreng 2L', 28000, 32000, 30, 'Minyak goreng kelapa sawit murni 2 liter'),
(1, 'Gula Pasir 1kg', 12500, 14000, 40, 'Gula pasir putih bersih 1 kilogram'),
(1, 'Tepung Terigu 1kg', 9000, 11000, 35, 'Tepung terigu serbaguna'),
(1, 'Garam Dapur 500gr', 3000, 4000, 60, 'Garam dapur beryodium'),
(2, 'Aqua Galon', 18000, 22000, 20, 'Air mineral dalam kemasan galon 19L'),
(2, 'Teh Botol Sosro 450ml', 4000, 5500, 48, 'Minuman teh dalam kemasan botol plastik'),
(2, 'Kopi Kapal Api Sachet', 1500, 2000, 100, 'Kopi kapal api spesial sachet 25gr'),
(2, 'Susu Ultra 200ml', 4500, 6000, 60, 'Susu UHT ultra milk 200ml'),
(2, 'Pocari Sweat 500ml', 6000, 8000, 36, 'Minuman isotonik pocari sweat 500ml'),
(3, 'Chitato Rasa Sapi 68gr', 8500, 11000, 24, 'Keripik kentang chitato rasa sapi panggang'),
(3, 'Oreo Original 119gr', 10000, 13000, 30, 'Biskuit sandwich oreo original'),
(3, 'Indomie Goreng', 2500, 3500, 120, 'Mie instan goreng rasa soto'),
(3, 'Supermi Ayam', 2000, 3000, 80, 'Mie instan supermi rasa ayam bawang'),
(3, 'Pilus Garuda 90gr', 5000, 7000, 40, 'Snack pilus garuda rasa pedas'),
(4, 'Sabun Lifebuoy 85gr', 3500, 5000, 45, 'Sabun mandi antibakteri lifebuoy'),
(4, 'Shampo Pantene 170ml', 18000, 22000, 20, 'Shampo pantene total damage care'),
(4, 'Detergen Rinso 800gr', 18000, 22000, 25, 'Detergen bubuk rinso anti noda'),
(5, 'Pulpen Ballpoint Standard', 2500, 4000, 50, 'Pulpen ballpoint standard 0.5mm'),
(5, 'Buku Tulis Sidu 38 Lembar', 4000, 6000, 30, 'Buku tulis sidu isi 38 lembar bergaris');

-- ============================================================
-- TRANSAKSI CONTOH (untuk demo dashboard & laporan)
-- ============================================================
INSERT INTO transaksi (no_transaksi, tanggal, total_harga, uang_bayar, kembalian, id_user) VALUES
('TRX-20260501-001', '2026-05-01 09:15:00', 85500, 100000, 14500, 2),
('TRX-20260501-002', '2026-05-01 11:30:00', 46000, 50000, 4000, 2),
('TRX-20260502-001', '2026-05-02 10:00:00', 112000, 120000, 8000, 2),
('TRX-20260510-001', '2026-05-10 14:20:00', 68000, 70000, 2000, 2),
('TRX-20260515-001', '2026-05-15 09:45:00', 155000, 200000, 45000, 2),
('TRX-20260520-001', '2026-05-20 16:00:00', 37500, 40000, 2500, 2),
('TRX-20260525-001', '2026-05-25 10:30:00', 93000, 100000, 7000, 2),
('TRX-20260531-001', '2026-05-31 08:45:00', 78000, 80000, 2000, 2),
('TRX-20260531-002', '2026-05-31 10:15:00', 45500, 50000, 4500, 2),
('TRX-20260531-003', '2026-05-31 13:00:00', 132000, 150000, 18000, 2);

INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga, subtotal) VALUES
(1, 1, 1, 68000, 68000),
(1, 8, 5, 2000, 10000),
(1, 5, 2, 4000, 8000),
(2, 2, 1, 32000, 32000),
(2, 6, 1, 22000, 22000),
(3, 1, 1, 68000, 68000),
(3, 16, 2, 5000, 10000),
(3, 13, 10, 3500, 35000),
(4, 17, 1, 22000, 22000),
(4, 18, 1, 22000, 22000),
(4, 3, 1, 14000, 14000),
(5, 1, 1, 68000, 68000),
(5, 9, 3, 6000, 18000),
(5, 10, 2, 8000, 16000),
(5, 11, 2, 11000, 22000),
(5, 19, 5, 4000, 20000),
(5, 20, 2, 6000, 12000),
(6, 13, 5, 3500, 17500),
(6, 14, 5, 3000, 15000),
(7, 7, 3, 5500, 16500),
(7, 12, 2, 13000, 26000),
(7, 15, 2, 7000, 14000),
(7, 6, 2, 22000, 44000),
(8, 3, 2, 14000, 28000),
(8, 4, 1, 11000, 11000),
(8, 5, 2, 4000, 8000),
(8, 8, 4, 2000, 8000),
(9, 11, 1, 11000, 11000),
(9, 12, 1, 13000, 13000),
(9, 15, 1, 7000, 7000),
(9, 19, 2, 4000, 8000),
(9, 13, 2, 3500, 7000),
(10, 17, 1, 22000, 22000),
(10, 18, 1, 22000, 22000),
(10, 2, 1, 32000, 32000),
(10, 9, 3, 6000, 18000),
(10, 16, 2, 5000, 10000),
(10, 8, 4, 2000, 8000);
