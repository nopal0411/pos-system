<?php
$query = "Tepung terigu Segitiga Biru penguin Segiris";
$searchUrl = "https://www.bing.com/images/search?q=" . urlencode($query);
$opts = ["http" => ["method" => "GET","header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"]];
$context = stream_context_create($opts);
$html = @file_get_contents($searchUrl, false, $context);

$imgUrls = [];
if ($html && preg_match_all('/class="mimg"[^>]*src="([^"]+)"/i', $html, $matches)) {
    $imgUrls = $matches[1];
} elseif ($html && preg_match_all('/src="(https:\/\/tse[0-9]\.mm\.bing\.net\/th\?id=[^"]+)"/i', $html, $matches)) {
    $imgUrls = $matches[1];
}
echo "Found " . count($imgUrls) . " images\n";
if(isset($imgUrls[0])) {
    echo "First image: " . html_entity_decode($imgUrls[0]);
}
