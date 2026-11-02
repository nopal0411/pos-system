<?php
$query = "Pocari Sweat";
$url = "https://images.search.yahoo.com/search/images?p=" . urlencode($query);
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
];
$context = stream_context_create($opts);
$html = file_get_contents($url, false, $context);
file_put_contents('yahoo_output.txt', $html);
