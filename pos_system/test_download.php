<?php
$url = 'https://images.tokopedia.net/img/cache/700/product-1/2020/1/10/72791781/72791781_6612803b-bd48-4e89-a299-ffcf7149f1db_700_700'; // Garam
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.9',
    'Referer: https://www.tokopedia.com/'
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($code == 200 && strlen($data) > 100) {
    file_put_contents('test_garam.jpg', $data);
    echo "Success! Size: " . strlen($data);
} else {
    echo "Failed! Code: $code";
}
