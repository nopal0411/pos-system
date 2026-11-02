<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$pdo->query("UPDATE produk SET gambar = 'prod_4_final.jpg' WHERE id_produk = 4");
echo "Updated to prod_4_final.jpg!";
