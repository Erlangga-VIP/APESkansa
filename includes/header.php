<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

// Halaman aktif untuk navigasi
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Data avatar user (hanya jika login)
$current_user_foto = null;
$current_user_initial = '';
if (isset($_SESSION['user_id'])) {
    $stmt = mysqli_prepare($conn, "SELECT foto_profil, nama FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $current_user_foto = $row['foto_profil'] ? 'uploads/' . htmlspecialchars($row['foto_profil']) : null;
        $current_user_initial = substr($row['nama'], 0, 1);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APEskansa - Marketplace Siswa SMKN 1 Bawang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/img/LOGOAPE.png" alt="APEskansa Logo">
                </a>
            </div>
            <nav class="nav" id="mainNav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo $current_page == 'index' ? 'active' : ''; ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a href="produk.php" class="nav-link <?php echo $current_page == 'produk' ? 'active' : ''; ?>">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a href="penjual.php" class="nav-link <?php echo $current_page == 'penjual' ? 'active' : ''; ?>">Penjual</a>
                    </li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])):
                    $dashboard_link = 'dashboard/pembeli/profil.php';
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'penjual') $dashboard_link = 'dashboard/penjual/profil.php';
                        elseif ($_SESSION['role'] == 'admin') $dashboard_link = 'dashboard/admin/dashboard.php';
                    }
                ?>
                    <a href="<?php echo $dashboard_link; ?>" class="profile-icon" title="Profil Saya">
                        <?php if ($current_user_foto): ?>
                            <img src="<?php echo $current_user_foto; ?>" alt="Foto Profil" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div class="avatar-circle"><?php echo strtoupper(htmlspecialchars($current_user_initial)); ?></div>
                        <?php endif; ?>
                    </a>
                    <a href="process/logout.php" class="btn btn-outline btn-sm">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline btn-sm">Masuk</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Daftar</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </header>