<?php
$url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Wheat_flour.jpg/320px-Wheat_flour.jpg';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'SkripsiFibrilPOS/1.0 (fibril@example.com)');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($httpCode == 200 && strlen($data) > 1000) {
    echo "Success! Size: " . strlen($data);
} else {
    echo "Failed with code: $httpCode";
}
