<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$source = 'C:/Users/Acer/.gemini/antigravity-ide/brain/8efb6b76-f50b-4d06-a2e0-5bf5907c09dd/kapal_api_logo_1784294954454.png';
$destination = 'c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_8_v3.png';

if (copy($source, $destination)) {
    $pdo->query("UPDATE produk SET gambar = 'prod_8_v3.png' WHERE id_produk = 8");
    echo "Kapal Api DB Updated to prod_8_v3.png";
} else {
    echo "Failed to copy image.";
}
