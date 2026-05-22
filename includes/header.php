<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

// Halaman aktif untuk navigasi
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Data user yang sedang login
$current_user_foto = null;
$current_user_initial = '';
$dashboard_link = BASE_URL . 'dashboard/pembeli/profil.php';

if (isset($_SESSION['user_id'])) {
    $stmt = mysqli_prepare($conn, 'SELECT foto_profil, nama FROM users WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $current_user_foto = $row['foto_profil']
            ? 'uploads/' . htmlspecialchars($row['foto_profil'], ENT_QUOTES, 'UTF-8')
            : null;
        $current_user_initial = mb_substr($row['nama'], 0, 1);
    }
    mysqli_stmt_close($stmt);

    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'penjual') {
            $dashboard_link = BASE_URL . 'dashboard/penjual/profil.php';
        } elseif ($_SESSION['role'] === 'admin') {
            $dashboard_link = BASE_URL . 'dashboard/admin/dashboard.php';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APEskansa – Marketplace Siswa SMKN 1 Bawang</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">
                <a href="<?= BASE_URL ?>index.php">
                    <img src="<?= BASE_URL ?>assets/img/LOGOAPE.png" alt="APEskansa Logo">
                </a>
            </div>

            <nav class="nav" id="mainNav">
                <ul class="nav-list">
                    <li>
                        <a href="<?= BASE_URL ?>index.php" class="nav-link <?= $current_page === 'index' ? 'active' : '' ?>">Beranda</a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>produk.php" class="nav-link <?= $current_page === 'produk' ? 'active' : '' ?>">Produk</a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>penjual.php" class="nav-link <?= $current_page === 'penjual' ? 'active' : '' ?>">Penjual</a>
                    </li>
                </ul>
            </nav>

            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $dashboard_link ?>" class="profile-icon" title="Profil Saya">
                        <?php if ($current_user_foto): ?>
                            <img src="<?= $current_user_foto ?>" alt="Foto Profil" style="width:38px; height:38px; border-radius:50%; object-fit:cover;">
                        <?php else: ?>
                            <div class="avatar-circle"><?= strtoupper(htmlspecialchars($current_user_initial, ENT_QUOTES, 'UTF-8')) ?></div>
                        <?php endif; ?>
                    </a>
                    <a href="process/logout.php" class="btn btn-outline btn-sm">Keluar</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login.php" class="btn btn-outline btn-sm">Masuk</a>
                    <a href="<?= BASE_URL ?>register.php" class="btn btn-primary btn-sm">Daftar</a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </header>