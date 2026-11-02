<?php
/**
 * TRANSAKSI/SIMPAN.PHP - API Simpan Transaksi
 * Menerima JSON dari POS interface, menyimpan ke database.
 * Fitur: Cash & QRIS payment method
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

// Validasi metode bayar
$metodeBayar = in_array($input['metode_bayar'] ?? '', ['cash', 'qris']) 
    ? $input['metode_bayar'] 
    : 'cash';

try {
    $pdo->beginTransaction();

    // Simpan header transaksi (termasuk metode_bayar)
    $stmt = $pdo->prepare("
        INSERT INTO transaksi (no_transaksi, tanggal, total_harga, uang_bayar, kembalian, metode_bayar, id_user)
        VALUES (?, NOW(), ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $input['no_transaksi'],
        $input['total_harga'],
        $input['uang_bayar'],
        $input['kembalian'],
        $metodeBayar,
        $_SESSION['user_id']
    ]);
    $id_transaksi = $pdo->lastInsertId();

    // Simpan detail + kurangi stok
    $stmtDetail = $pdo->prepare("
        INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmtStok = $pdo->prepare("
        UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND stok >= ?
    ");

    foreach ($input['items'] as $item) {
        $stmtDetail->execute([
            $id_transaksi,
            $item['id_produk'],
            $item['jumlah'],
            $item['harga'],
            $item['subtotal']
        ]);

        // Kurangi stok, pastikan tidak minus
        $stmtStok->execute([$item['jumlah'], $item['id_produk'], $item['jumlah']]);
        if ($stmtStok->rowCount() === 0) {
            throw new Exception("Stok produk ID {$item['id_produk']} tidak mencukupi.");
        }
    }

    $pdo->commit();

    echo json_encode([
        'success'       => true,
        'id_transaksi'  => $id_transaksi,
        'no_transaksi'  => $input['no_transaksi'],
        'metode_bayar'  => $metodeBayar,
        'message'       => 'Transaksi berhasil disimpan'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
