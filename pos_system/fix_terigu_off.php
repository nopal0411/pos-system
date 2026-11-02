<?php
$url = "https://world.openfoodfacts.org/cgi/search.pl?search_terms=segitiga+biru&search_simple=1&action=process&json=1";
$opts = ["http" => ["method" => "GET","header" => "User-Agent: Mozilla/5.0\r\n"]];
$context = stream_context_create($opts);
$json = file_get_contents($url, false, $context);
$data = json_decode($json, true);

if (!empty($data['products'])) {
    $imgUrl = null;
    foreach ($data['products'] as $p) {
        if (!empty($p['image_front_url'])) {
            $imgUrl = $p['image_front_url'];
            break;
        }
    }
    
    if ($imgUrl) {
        $imgData = file_get_contents($imgUrl);
        if ($imgData) {
            file_put_contents('c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_4_v5.jpg', $imgData);
            require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
            $pdo->query("UPDATE produk SET gambar = 'prod_4_v5.jpg' WHERE id_produk = 4");
            echo "Successfully downloaded Segitiga Biru from OpenFoodFacts!";
        } else {
            echo "Failed to download image from OFF: $imgUrl";
        }
    } else {
        echo "No image found in OFF products.";
    }
} else {
    echo "No products found on OFF.";
}
