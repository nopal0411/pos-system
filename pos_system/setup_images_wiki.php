<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$dir = UPLOAD_PATH;
if (!is_dir($dir)) mkdir($dir, 0777, true);

$products = [
    4 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Wheat_flour.jpg/320px-Wheat_flour.jpg', // Terigu
    5 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Sea_salt-e-kuwa.jpg/320px-Sea_salt-e-kuwa.jpg', // Garam
    8 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/A_small_cup_of_coffee.JPG/320px-A_small_cup_of_coffee.JPG', // Kopi
    9 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0e/Milk_glass.jpg/320px-Milk_glass.jpg', // Susu
    10 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Pocari_Sweat.jpg/320px-Pocari_Sweat.jpg', // Pocari
    11 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/69/Potato-Chips.jpg/320px-Potato-Chips.jpg', // Chitato
    12 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/Oreo-Two-Cookies.jpg/320px-Oreo-Two-Cookies.jpg', // Oreo
    14 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/Indomie_Mi_Goreng_Rendang.jpg/320px-Indomie_Mi_Goreng_Rendang.jpg', // Supermi (Mie)
    15 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Peanut_9417.jpg/320px-Peanut_9417.jpg', // Pilus (Kacang)
    16 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Lifebuoy_soap.jpg/320px-Lifebuoy_soap.jpg', // Sabun
    17 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Shampoo.jpg/320px-Shampoo.jpg', // Shampo
    18 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Laundry_detergent.jpg/320px-Laundry_detergent.jpg', // Detergen
    19 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Bic_Cristal.jpg/320px-Bic_Cristal.jpg', // Pulpen
    20 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/A_notebook.jpg/320px-A_notebook.jpg', // Buku Tulis
];

function downloadImage($url, $savePath) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
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

foreach($products as $id => $url) {
    $filename = 'prod_' . $id . '.jpg';
    $path = $dir . $filename;
    
    echo "Downloading ID $id... ";
    if (downloadImage($url, $path)) {
        $pdo->query("UPDATE produk SET gambar = '$filename' WHERE id_produk = $id");
        echo "Success!\n";
    } else {
        // Fallback to loremflickr
        $fallback = 'https://loremflickr.com/320/240/grocery,item';
        if (downloadImage($fallback, $path)) {
            $pdo->query("UPDATE produk SET gambar = '$filename' WHERE id_produk = $id");
            echo "Fallback Success!\n";
        } else {
            echo "Failed!\n";
        }
    }
}
echo "Selesai.\n";
