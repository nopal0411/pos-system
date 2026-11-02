<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$pdo->query("UPDATE produk SET gambar = 'prod_4_v7.png' WHERE id_produk = 4");
echo "DB Updated to prod_4_v7.png";
