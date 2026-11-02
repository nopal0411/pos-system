<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

// Fix ID 17 (Shampo) back to its original image (prod_17.jpg)
$pdo->query("UPDATE produk SET gambar = 'prod_17.jpg' WHERE id_produk = 17");

// Update ID 14 (Supermi Ayam) to use the Supermi picture we generated (prod_17_v2.png)
// Let's copy it to a proper name just to be clean
$supermi_img = 'c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_17_v2.png';
$new_supermi_img = 'c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_14_v2.png';

if (file_exists($supermi_img)) {
    copy($supermi_img, $new_supermi_img);
    $pdo->query("UPDATE produk SET gambar = 'prod_14_v2.png' WHERE id_produk = 14");
    echo "Fixed! Supermi is now on ID 14, and Shampo is reverted on ID 17.";
} else {
    echo "Supermi image not found at $supermi_img";
}
