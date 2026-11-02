<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$source = 'C:/Users/Acer/.gemini/antigravity-ide/brain/8efb6b76-f50b-4d06-a2e0-5bf5907c09dd/sabun_lifebuoy_1784295891706.png';
$destination = 'c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_16_v4.png';

if (copy($source, $destination)) {
    $pdo->query("UPDATE produk SET gambar = 'prod_16_v4.png' WHERE id_produk = 16");
    echo "Sabun Lifebuoy DB Updated to prod_16_v4.png";
} else {
    echo "Failed to copy image.";
}
