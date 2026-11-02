<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$source = 'C:/Users/Acer/.gemini/antigravity-ide/brain/8efb6b76-f50b-4d06-a2e0-5bf5907c09dd/garam_cap_kapal_1784295670976.png';
$destination = 'c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_5_v2.png';

if (copy($source, $destination)) {
    $pdo->query("UPDATE produk SET gambar = 'prod_5_v2.png' WHERE id_produk = 5");
    echo "Garam Cap Kapal DB Updated to prod_5_v2.png";
} else {
    echo "Failed to copy image.";
}
