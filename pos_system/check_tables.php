<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';
$stmt = $pdo->query('SHOW TABLES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
