<?php
/**
 * LOGOUT.PHP - Proses Keluar dari Sistem
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Hapus semua data session
$_SESSION = [];
session_destroy();

// Redirect ke halaman login
redirect(BASE_URL . '/modules/auth/login.php');
