<?php
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: SkripsiPOSBot/1.0 (contact@example.com)\r\n"
    ]
];
$context = stream_context_create($opts);
$json = file_get_contents('https://en.wikipedia.org/w/api.php?action=query&titles=Pocari_Sweat&prop=pageimages&format=json&pithumbsize=300', false, $context);
echo $json;
