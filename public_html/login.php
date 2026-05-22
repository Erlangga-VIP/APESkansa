<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

$page_title = 'Login – APEskansa';
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php require_once __DIR__ . '/../includes/head-common.php'; ?>
</head>
<body class="auth-page">
    <div class="auth-shell">
        <div class="auth-brand-panel">
            <h2>Belanja & jualan di satu tempat</h2>
            <p>Marketplace resmi wirausaha siswa SMKN 1 Bawang. Aman, dekat, dan mudah untuk komunitas sekolah.</p>
            <ul>
                <li><i class="fas fa-check-circle"></i> Katalog produk siswa</li>
                <li><i class="fas fa-check-circle"></i> Pemesanan online + COD</li>
                <li><i class="fas fa-check-circle"></i> Dashboard toko penjual</li>
            </ul>
        </div>

        <div class="auth-container">
            <div class="auth-logo">
                <a href="<?= page_url('index.php') ?>">
                    <img src="<?= page_url('assets/img/LOGOAPE.png') ?>" alt="APEskansa">
                </a>
            </div>

            <h1 class="auth-title">Masuk ke Akun</h1>

            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom: var(--space-lg);">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= page_url('process/login.php') ?>" method="POST" class="auth-form">
                <?= csrf_field() ?>
                <div class="form-group input-icon-wrap">
                    <label for="email">Email</label>
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="nama@email.com" required autofocus>
                </div>
                <div class="form-group input-icon-wrap">
                    <label for="password">Password</label>
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Password Anda" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Login</button>
            </form>

            <div class="auth-links">
                <p>Belum punya akun? <a href="<?= page_url('register.php') ?>">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</body>
</html>
