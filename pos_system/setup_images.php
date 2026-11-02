<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$dir = UPLOAD_PATH;
if (!is_dir($dir)) mkdir($dir, 0777, true);

$products = [
    1 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/7/8/bcba058e-7dd5-48b6-9bb2-ab8cf05b95cb.jpg', // Beras
    2 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/4/19/2fde3713-d49e-4a6c-9407-1b033b0ec719.jpg', // Minyak
    3 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/1/14/6a82a0b1-12c1-4606-ba7e-7901da42a12b.jpg', // Gula
    4 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/7/1/2275704/2275704_a7a33ed9-5c4d-44a5-92db-a51ed2f0e0ab_2000_2000', // Terigu
    5 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/1/10/72791781/72791781_6612803b-bd48-4e89-a299-ffcf7149f1db_700_700', // Garam
    6 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/6/15/d7dc7b72-2bb1-41dd-933e-e67cff8fecf9.jpg', // Aqua
    7 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2020/10/19/271777b7-bd3c-41c3-bde3-64041b65e751.jpg', // Teh Botol
    8 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/11/13/21f87966-2ce9-408a-b8cb-40243d5500e5.jpg', // Kopi Kapal Api
    9 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/10/5/30a9e88b-b83c-4bc3-8f0a-1132a243d63b.jpg', // Susu Ultra
    10 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/9/30/23d0c2e9-467f-442b-9ca3-1a7bc851610e.jpg', // Pocari
    11 => 'https://images.tokopedia.net/img/cache/700/hDjmkQ/2022/9/30/aeb924ab-efec-4c6e-aba4-62cce6bb7d9d.jpg', // Chitato
    12 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/1/25/6770cece-69df-416b-95bb-50eefc5db617.jpg', // Oreo
    13 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/8/20/d1e2e171-8bc6-4dfc-acfa-7df09cc7a6d8.jpg', // Indomie
    14 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/11/4/cfdbcbdf-2da5-4674-8b65-98cd7286358c.jpg', // Supermi
    15 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/9/12/32332616/32332616_cc079b7b-2321-4f81-9b16-5bc7c689e436_500_500', // Pilus
    16 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/10/7/cf522cf7-f831-41b1-a6ce-dc44ec94d8d1.jpg', // Lifebuoy
    17 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/4/4/6c8ff540-34a8-44e2-8956-621f2bb590b5.jpg', // Pantene
    18 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/9/2/c8bd94e1-eb17-478d-a416-56dd09995c73.jpg', // Rinso
    19 => 'https://images.tokopedia.net/img/cache/700/product-1/2019/12/22/3358965/3358965_d80d4bdc-52ed-4786-81cf-1e8dc44f2d59_500_500', // Pulpen
    20 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/7/20/7311311/7311311_dd970e7a-fb80-498c-8fdf-3d60d3d5271a_500_500', // Sidu
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

function generatePlaceholder($name, $path) {
    $colors = ['#f87171', '#fb923c', '#facc15', '#4ade80', '#2dd4bf', '#38bdf8', '#818cf8', '#c084fc', '#f472b6'];
    $bg = $colors[array_rand($colors)];
    $initials = substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 2);
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
        <rect width="300" height="300" fill="' . $bg . '"/>
        <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="80" font-weight="bold" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars(strtoupper($initials)) . '</text>
        <text x="50%" y="80%" font-family="Arial, sans-serif" font-size="20" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($name) . '</text>
    </svg>';
    file_put_contents($path, $svg);
}

$stmt = $pdo->query('SELECT id_produk, nama_produk FROM produk');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id_produk'];
    $name = $row['nama_produk'];
    $filename = 'prod_' . $id . '.jpg';
    $path = $dir . $filename;
    
    $success = false;
    if (isset($products[$id])) {
        $success = downloadImage($products[$id], $path);
    }
    
    if (!$success) {
        $filename = 'prod_' . $id . '.svg';
        $path = $dir . $filename;
        generatePlaceholder($name, $path);
    }
    
    $pdo->query("UPDATE produk SET gambar = '$filename' WHERE id_produk = $id");
    echo "Done: $name -> $filename\n";
}
echo "Semua gambar berhasil diset!\n";
