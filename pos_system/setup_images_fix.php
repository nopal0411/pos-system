<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$dir = UPLOAD_PATH;
$products = [
    10 => "Pocari Sweat botol plastik",
    14 => "Mie Instan Supermi kemasan"
];

function downloadImage($url, $savePath) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
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

foreach($products as $id => $query) {
    $filename = 'prod_' . $id . '.jpg';
    $path = $dir . $filename;
    $searchUrl = "https://www.bing.com/images/search?q=" . urlencode($query);
    $opts = ["http" => ["method" => "GET","header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"]];
    $context = stream_context_create($opts);
    $html = @file_get_contents($searchUrl, false, $context);
    
    $imgUrl = null;
    if ($html && preg_match('/class="mimg"[^>]*src="([^"]+)"/i', $html, $matches)) {
        $imgUrl = html_entity_decode($matches[1]);
    } elseif ($html && preg_match('/src="(https:\/\/tse[0-9]\.mm\.bing\.net\/th\?id=[^"]+)"/i', $html, $matches)) {
        $imgUrl = html_entity_decode($matches[1]);
    }
    if ($imgUrl && downloadImage($imgUrl, $path)) {
        $pdo->query("UPDATE produk SET gambar = '$filename' WHERE id_produk = $id");
        echo "ID $id Updated!\n";
    } else {
        echo "ID $id Failed!\n";
    }
}
