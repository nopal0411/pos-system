<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$dir = UPLOAD_PATH;
if (!is_dir($dir)) mkdir($dir, 0777, true);

$products = [
    4 => 'flour,bag',
    5 => 'salt,shaker',
    8 => 'coffee,beans',
    9 => 'milk,carton',
    10 => 'sportsdrink,bottle',
    11 => 'potatochips,snack',
    12 => 'oreo,cookies',
    14 => 'instantnoodles,ramen',
    15 => 'peanuts,snack',
    16 => 'soap,bar',
    17 => 'shampoo,bottle',
    18 => 'detergent,laundry',
    19 => 'ballpoint,pen',
    20 => 'notebook,school'
];

function downloadImage($url, $savePath) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && $data !== false && strlen($data) > 1000) {
        file_put_contents($savePath, $data);
        return true;
    }
    return false;
}

foreach($products as $id => $keywords) {
    $filename = 'prod_' . $id . '.jpg';
    $path = $dir . $filename;
    $url = "https://loremflickr.com/400/400/" . $keywords . "/all";
    
    echo "Downloading ID $id ($keywords)... ";
    if (downloadImage($url, $path)) {
        $pdo->query("UPDATE produk SET gambar = '$filename' WHERE id_produk = $id");
        echo "Success!\n";
    } else {
        echo "Failed!\n";
    }
}
echo "Selesai.\n";
