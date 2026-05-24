<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_user_foto = null;
$current_user_initial = '';
$dashboard_link = page_url('dashboard/pembeli/profil.php');

if (isset($_SESSION['user_id'])) {
    $stmt = mysqli_prepare($conn, 'SELECT foto_profil, nama FROM users WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $current_user_foto = $row['foto_profil'] ? upload_url($row['foto_profil']) : null;
        $current_user_initial = mb_substr($row['nama'], 0, 1);
    }
    mysqli_stmt_close($stmt);

    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'penjual') {
            $dashboard_link = page_url('dashboard/penjual/index.php');
        } elseif ($_SESSION['role'] === 'admin') {
            $dashboard_link = page_url('dashboard/admin/dashboard.php');
        }
    }
}

$page_title = $page_title ?? 'APEskansa – Marketplace Siswa SMKN 1 Bawang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php require_once __DIR__ . '/head-common.php'; ?>
</head>
<body class="app">
    <header class="header" id="siteHeader">
        <div class="container header-content">
            <div class="logo">
                <a href="<?= page_url('index.php') ?>">
                    <img src="<?= page_url('assets/img/LOGOAPE.png') ?>" alt="APEskansa">
                </a>
            </div>

            <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button"
                    aria-label="Buka menu" aria-expanded="false" aria-controls="headerCollapse">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <div class="header-collapse" id="headerCollapse">
                <nav class="nav" id="mainNav" aria-label="Navigasi utama">
                    <ul class="nav-list">
                        <li><a href="<?= page_url('index.php') ?>" class="nav-link <?= $current_page === 'index' ? 'active' : '' ?>">Beranda</a></li>
                        <li><a href="<?= page_url('produk.php') ?>" class="nav-link <?= $current_page === 'produk' ? 'active' : '' ?>">Produk</a></li>
                        <li><a href="<?= page_url('penjual.php') ?>" class="nav-link <?= $current_page === 'penjual' ? 'active' : '' ?>">Penjual</a></li>
                    </ul>
                </nav>

                <div class="auth-buttons" id="authButtons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= $dashboard_link ?>" class="profile-icon" title="Profil Saya">
                            <?php if ($current_user_foto): ?>
                                <img src="<?= $current_user_foto ?>" alt="Foto Profil" class="profile-icon-img">
                            <?php else: ?>
                                <div class="avatar-circle"><?= strtoupper(htmlspecialchars($current_user_initial, ENT_QUOTES, 'UTF-8')) ?></div>
                            <?php endif; ?>
                        </a>
                        <a href="<?= page_url('process/logout.php') ?>" class="btn btn-outline btn-sm">Keluar</a>
                    <?php else: ?>
                        <a href="<?= page_url('login.php') ?>" class="btn btn-outline btn-sm">Masuk</a>
                        <a href="<?= page_url('register.php') ?>" class="btn btn-primary btn-sm">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <?php require_once __DIR__ . '/flash.php'; ?>
