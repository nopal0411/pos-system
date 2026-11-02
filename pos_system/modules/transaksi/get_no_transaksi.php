<?php
/**
 * TRANSAKSI/GET_NO_TRANSAKSI.PHP
 * Generate nomor transaksi baru via AJAX
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['no' => 'TRX-ERROR']);
    exit;
}

echo json_encode(['no' => generateNoTransaksi()]);
