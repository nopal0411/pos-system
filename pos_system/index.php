<?php
/**
 * INDEX.PHP - Entry Point POS System
 * Redirect ke dashboard jika sudah login, atau ke halaman login
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/modules/dashboard/index.php');
} else {
    redirect(BASE_URL . '/modules/auth/login.php');
}
