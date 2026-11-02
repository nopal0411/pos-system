<?php
$query = "Kopi Kapal Api sachet";
$url = "https://www.bing.com/images/search?q=" . urlencode($query);
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
];
$context = stream_context_create($opts);
$html = file_get_contents($url, false, $context);

if (preg_match('/class="mimg"[^>]*src="([^"]+)"/i', $html, $matches)) {
    echo "Found URL: " . $matches[1] . "\n";
} elseif (preg_match('/src="(https:\/\/tse[0-9]\.mm\.bing\.net\/th\?id=[^"]+)"/i', $html, $matches)) {
    echo "Found URL (fallback): " . $matches[1] . "\n";
} else {
    echo "No image found.\n";
}
