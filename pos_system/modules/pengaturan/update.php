<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

// Pastikan hanya admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kunci = trim($_POST['kunci'] ?? '');
    $nilai = trim($_POST['nilai'] ?? '');
    
    if (empty($kunci) || empty($nilai)) {
        echo json_encode(['success' => false, 'error' => 'Parameter tidak lengkap']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE kunci = ?");
        $stmt->execute([$nilai, $kunci]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Metode tidak didukung']);
}
