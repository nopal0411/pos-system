<?php
/**
 * HEADER.PHP - Global Layout: Sidebar + Topbar
 * POS System Toko Fenny Grosir
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
requireLogin();

// Tentukan menu aktif berdasarkan URL
$currentPage = basename(dirname($_SERVER['PHP_SELF']));
$currentFile = basename($_SERVER['PHP_SELF']);

function isActive($page) {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}

$flash = getFlash();
$userName = $_SESSION['nama'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'kasir';
$userInitial = strtoupper(substr($userName, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Point of Sales Toko Fenny Grosir - Manajemen Penjualan Modern">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' – ' : '' ?><?= APP_NAME ?> POS</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">

    <?= isset($extraHead) ? $extraHead : '' ?>
</head>
<body>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="wrapper">
    <!-- ====================================================
         SIDEBAR NAVIGATION
         ==================================================== -->
    <nav class="sidebar" id="sidebar">
        <!-- Brand Logo -->
        <div class="sidebar-brand">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="brand-logo">
                <div class="brand-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div class="brand-text">
                    <h5>Toko Fenny Grosir</h5>
                    <span>POS System v1.0</span>
                </div>
            </a>
        </div>

        <!-- User Info -->
        <div class="sidebar-user">
            <div class="user-avatar"><?= $userInitial ?></div>
            <div class="user-info">
                <span class="user-name"><?= e($userName) ?></span>
                <span class="user-role"><?= ucfirst($userRole) ?></span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <ul class="nav flex-column" style="list-style:none;padding:0;">

                <li class="nav-item">
                    <a class="nav-link <?= isActive('dashboard') ?>"
                       href="<?= BASE_URL ?>/modules/dashboard/index.php">
                        <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('transaksi') ?>"
                       href="<?= BASE_URL ?>/modules/transaksi/index.php">
                        <span class="nav-icon"><i class="fas fa-cash-register"></i></span>
                        Transaksi / Kasir
                    </a>
                </li>

                <div class="nav-section-title">Manajemen Data</div>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('produk') ?>"
                       href="<?= BASE_URL ?>/modules/produk/index.php">
                        <span class="nav-icon"><i class="fas fa-box-open"></i></span>
                        Produk
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('kategori') ?>"
                       href="<?= BASE_URL ?>/modules/kategori/index.php">
                        <span class="nav-icon"><i class="fas fa-tags"></i></span>
                        Kategori
                    </a>
                </li>

                <div class="nav-section-title">Laporan</div>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('riwayat') ?>"
                       href="<?= BASE_URL ?>/modules/riwayat/index.php">
                        <span class="nav-icon"><i class="fas fa-history"></i></span>
                        Riwayat Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('laporan') ?>"
                       href="<?= BASE_URL ?>/modules/laporan/index.php">
                        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                        Laporan Penjualan
                    </a>
                </li>

                <?php if (isAdmin()): ?>
                <div class="nav-section-title">Administrasi</div>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('user') ?>"
                       href="<?= BASE_URL ?>/modules/user/index.php">
                        <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                        Manajemen User
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActive('pengaturan') ?>"
                       href="<?= BASE_URL ?>/modules/pengaturan/index.php">
                        <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                        Pengaturan Fitur
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>/modules/auth/logout.php" class="logout-btn"
               onclick="return confirm('Yakin ingin keluar dari sistem?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </nav>

    <!-- ====================================================
         MAIN CONTENT
         ==================================================== -->
    <div class="main-content">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title"><?= isset($pageTitle) ? e($pageTitle) : 'Dashboard' ?></div>
                    <?php if (isset($pageSubtitle)): ?>
                    <span class="topbar-subtitle"><?= e($pageSubtitle) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-time" style="display:block;">
                    <span class="time" id="topbar-time">--:--:--</span>
                    <span class="date" id="topbar-date">Loading...</span>
                </div>
                <!-- Profile Dropdown -->
                <div class="profile-dropdown" id="profileDropdown">
                    <button class="topbar-avatar" id="profileToggle" title="<?= e($userName) ?>" aria-expanded="false">
                        <?= $userInitial ?>
                        <span class="profile-caret"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="profile-menu" id="profileMenu" role="menu">
                        <!-- Header info user -->
                        <div class="profile-menu-header">
                            <div class="profile-menu-avatar"><?= $userInitial ?></div>
                            <div class="profile-menu-info">
                                <div class="profile-menu-name"><?= e($userName) ?></div>
                                <div class="profile-menu-role"><?= ucfirst($userRole) ?></div>
                            </div>
                        </div>
                        <div class="profile-menu-divider"></div>
                        <!-- Edit Profil -->
                        <a href="<?= BASE_URL ?>/modules/profil/index.php" class="profile-menu-item" id="btnEditProfil" role="menuitem">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profil</span>
                        </a>
                        <!-- Logout -->
                        <a href="<?= BASE_URL ?>/modules/auth/logout.php" class="profile-menu-item danger" id="btnLogout" role="menuitem"
                           onclick="return confirm('Yakin ingin keluar dari sistem?')">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAGE CONTENT -->
        <div class="page-content">
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>" data-auto-dismiss>
                <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'danger' ? 'fa-times-circle' : 'fa-info-circle') ?>"></i>
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>
