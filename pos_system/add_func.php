<?php
$file = 'c:/laragon/www/SkripsiFibril/pos_system/includes/functions.php';
$content = file_get_contents($file);

$func = '
/**
 * Mendapatkan nilai pengaturan dari database
 */
function get_pengaturan($kunci, $default = "nonaktif") {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT nilai FROM pengaturan WHERE kunci = ?");
        $stmt->execute([$kunci]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    } catch(PDOException $e) {
        return $default;
    }
}
';

if (strpos($content, 'function get_pengaturan') === false) {
    file_put_contents($file, $func, FILE_APPEND);
    echo "Function added!";
} else {
    echo "Function already exists.";
}
