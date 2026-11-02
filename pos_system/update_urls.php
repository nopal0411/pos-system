<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$updates = [
    4 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/7/1/2275704/2275704_a7a33ed9-5c4d-44a5-92db-a51ed2f0e0ab_2000_2000', // Terigu
    5 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/1/10/72791781/72791781_6612803b-bd48-4e89-a299-ffcf7149f1db_700_700', // Garam
    8 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/11/13/21f87966-2ce9-408a-b8cb-40243d5500e5.jpg', // Kopi Kapal Api
    9 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/10/5/30a9e88b-b83c-4bc3-8f0a-1132a243d63b.jpg', // Susu Ultra
    10 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/9/30/23d0c2e9-467f-442b-9ca3-1a7bc851610e.jpg', // Pocari Sweat
    11 => 'https://images.tokopedia.net/img/cache/700/hDjmkQ/2022/9/30/aeb924ab-efec-4c6e-aba4-62cce6bb7d9d.jpg', // Chitato
    12 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/1/25/6770cece-69df-416b-95bb-50eefc5db617.jpg', // Oreo
    14 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/11/4/cfdbcbdf-2da5-4674-8b65-98cd7286358c.jpg', // Supermi
    15 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/9/12/32332616/32332616_cc079b7b-2321-4f81-9b16-5bc7c689e436_500_500', // Pilus Garuda
    16 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/10/7/cf522cf7-f831-41b1-a6ce-dc44ec94d8d1.jpg', // Sabun Lifebuoy
    17 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/4/4/6c8ff540-34a8-44e2-8956-621f2bb590b5.jpg', // Shampo Pantene
    18 => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/9/2/c8bd94e1-eb17-478d-a416-56dd09995c73.jpg', // Rinso Detergen
    19 => 'https://images.tokopedia.net/img/cache/700/product-1/2019/12/22/3358965/3358965_d80d4bdc-52ed-4786-81cf-1e8dc44f2d59_500_500', // Pulpen Standard
    20 => 'https://images.tokopedia.net/img/cache/700/product-1/2020/7/20/7311311/7311311_dd970e7a-fb80-498c-8fdf-3d60d3d5271a_500_500' // Buku Sidu
];

foreach($updates as $id => $url) {
    $stmt = $pdo->prepare("UPDATE produk SET gambar = ? WHERE id_produk = ?");
    $stmt->execute([$url, $id]);
    echo "Updated ID $id\n";
}
echo "All 14 external URLs updated successfully.";
