<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$stmt = $pdo->query('SELECT id_produk, nama_produk, gambar FROM produk');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id_produk'] . " | " . $row['nama_produk'] . " | " . $row['gambar'] . "\n";
}
