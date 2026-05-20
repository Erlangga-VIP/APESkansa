<?php
session_start();
include 'js/php/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect penjual or admin to their specific dashboards
if ($_SESSION['role'] == 'penjual') {
    header("Location: penjual-profil.php");
    exit;
} elseif ($_SESSION['role'] == 'admin') {
    header("Location: admin-dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - APEskansa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
                    </a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li class="nav-item"><a href="index.php" class="nav-link">Beranda</a></li>
                        <li class="nav-item"><a href="produk.php" class="nav-link">Produk</a></li>
                        <li class="nav-item"><a href="penjual.php" class="nav-link">Penjual</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])):
                        $user_initial = isset($_SESSION['nama']) ? substr($_SESSION['nama'], 0, 1) : 'U';
                    ?>
                        <a href="profil.php" class="profile-icon" title="Profil Saya">
                            <div class="avatar-circle"><?php echo strtoupper(htmlspecialchars($user_initial)); ?></div>
                        </a>
                        <a href="js/php/logout.php" class="btn btn-outline">Keluar</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Masuk</a>
                        <a href="register.php" class="btn btn-primary">Daftar</a>
                    <?php endif; ?>
                </div>
                <button class="mobile-menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <div class="dashboard" style="background-color: var(--secondary-color); padding: 3rem 0; min-height: calc(100vh - 100px);">
        <div class="container" style="max-width: 800px;">
            <div class="dashboard-header" style="margin-bottom: 2rem;">
                <h1 class="dashboard-title">Profil Saya</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
            </div>

            <div class="dashboard-card">
                <div class="card-header" style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <h2>Informasi Akun</h2>
                </div>
                <div class="profile-info">
                    <div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2rem;">
                        <div class="avatar-circle" style="width: 100px; height: 100px; font-size: 3rem;">
                            <?php echo strtoupper(htmlspecialchars($user_initial)); ?>
                        </div>
                        <div>
                            <h3 style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($_SESSION['nama']); ?></h3>
                            <p style="color: #666; margin-bottom: 0.25rem;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                            <span class="status-badge status-active" style="margin-top: 0.5rem;"><i class="fas fa-user-check"></i> <?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>
