<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=users');
    exit;
}

csrf_require();

$user_id = (int) ($_POST['user_id'] ?? 0);

if ($user_id <= 0) {
    header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=users');
    exit;
}

if ($user_id === (int) $_SESSION['user_id']) {
    $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri.';
    header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=users');
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $user_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Pengguna berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus pengguna.';
}

mysqli_stmt_close($stmt);
header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=users');
exit;
