<?php
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: SkripsiPOSBot/1.0 (contact@example.com)\r\n"
    ]
];
$context = stream_context_create($opts);
$data = file_get_contents('https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Pocari_Sweat_logo.png/330px-Pocari_Sweat_logo.png', false, $context);
if ($data && strlen($data) > 1000) {
    file_put_contents('test_pocari.png', $data);
    echo "Image downloaded! Size: " . strlen($data);
} else {
    echo "Download failed!";
}
