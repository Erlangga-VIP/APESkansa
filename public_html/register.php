<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

$page_title = 'Daftar – APEskansa';
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php require_once __DIR__ . '/../includes/head-common.php'; ?>
</head>
<body class="auth-page">
    <div class="auth-shell">
        <div class="auth-brand-panel">
            <h2>Mulai berjualan hari ini</h2>
            <p>Daftar sebagai pembeli atau penjual dan jadilah bagian dari ekosistem wirausaha siswa yang tumbuh bersama.</p>
            <ul>
                <li><i class="fas fa-user-check"></i> Akun pembeli & penjual</li>
                <li><i class="fas fa-image"></i> Upload produk dengan foto</li>
                <li><i class="fas fa-star"></i> Testimoni komunitas</li>
            </ul>
        </div>

        <div class="auth-container">
            <div class="auth-logo">
                <a href="<?= page_url('index.php') ?>">
                    <img src="<?= page_url('assets/img/LOGOAPE.png') ?>" alt="APEskansa">
                </a>
            </div>

            <h1 class="auth-title">Buat Akun Baru</h1>

            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom: var(--space-lg);">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" style="margin-bottom: var(--space-lg);">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= page_url('process/register.php') ?>" method="POST" class="auth-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                           placeholder="Nama lengkap Anda" required autofocus>
                </div>
                <div class="form-group input-icon-wrap">
                    <label for="email">Email</label>
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="nama@email.com" required>
                </div>
                <div class="form-group input-icon-wrap">
                    <label for="password">Password</label>
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Min. 6 karakter" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="role">Daftar sebagai</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="pembeli">Pembeli</option>
                        <option value="penjual">Penjual</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
            </form>

            <div class="auth-links">
                <p>Sudah punya akun? <a href="<?= page_url('login.php') ?>">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
