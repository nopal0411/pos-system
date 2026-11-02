<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

try {
    // Buat tabel pengaturan
    $sql = "CREATE TABLE IF NOT EXISTS pengaturan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kunci VARCHAR(50) NOT NULL UNIQUE,
        nilai VARCHAR(255) NOT NULL,
        deskripsi TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "Tabel pengaturan berhasil dibuat.\n";
    
    // Insert data default
    $defaults = [
        ['fitur_pencarian_linear', 'aktif', 'Menggunakan algoritma Linear Search untuk pencarian produk'],
        ['fitur_diskon', 'aktif', 'Mengaktifkan fitur diskon pada halaman transaksi'],
        ['cetak_struk_otomatis', 'aktif', 'Mencetak struk secara otomatis setelah transaksi berhasil']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO pengaturan (kunci, nilai, deskripsi) VALUES (?, ?, ?)");
    foreach ($defaults as $d) {
        $stmt->execute($d);
    }
    echo "Data default pengaturan berhasil ditambahkan.\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
