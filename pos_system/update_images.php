<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$updates = [
    1 => 'prod_1.png',
    2 => 'prod_2.png',
    3 => 'prod_3.png',
    6 => 'prod_6.png',
    7 => 'prod_7.png',
    13 => 'prod_13.png'
];
foreach($updates as $id => $img) {
    $pdo->query("UPDATE produk SET gambar = '$img' WHERE id_produk = $id");
}
echo "Images updated in DB.";
