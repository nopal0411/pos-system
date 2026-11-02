<?php
$query = "Tepung Terigu Segitiga Biru 1kg kemasan plastik";
$url = "https://www.google.com/search?tbm=isch&q=" . urlencode($query);

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
];
$context = stream_context_create($opts);
$html = file_get_contents($url, false, $context);

// Extract base64 image
if (preg_match('/data:image\/(jpeg|png|gif);base64,([A-Za-z0-9+\/]+={0,2})/', $html, $matches)) {
    $base64data = $matches[2];
    $imgData = base64_decode($base64data);
    
    // Save to file
    file_put_contents('c:/laragon/www/SkripsiFibril/pos_system/assets/img/produk/prod_4_v4.jpg', $imgData);
    
    // Update DB
    require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
    $pdo->query("UPDATE produk SET gambar = 'prod_4_v4.jpg' WHERE id_produk = 4");
    
    echo "Successfully downloaded Segitiga Biru from Google Base64!";
} else {
    echo "Failed to find base64 image in Google HTML.";
}
