<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$stmt = $pdo->query("SELECT id_produk, nama_produk, gambar FROM produk");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
