<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$pdo->query("UPDATE produk SET gambar = 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Pocari_Sweat.jpg/320px-Pocari_Sweat.jpg' WHERE id_produk = 10");
echo 'Done';
