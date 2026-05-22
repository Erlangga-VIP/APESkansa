<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

// Hanya admin yang bisa menghapus testimoni
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$testimoni_id = (int) ($_GET['id'] ?? 0);

if ($testimoni_id <= 0) {
    header('Location: ../dashboard/admin/dashboard.php?tab=testimoni');
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM testimoni WHERE testimoni_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $testimoni_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Testimoni berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus testimoni.';
}

mysqli_stmt_close($stmt);
header('Location: ../dashboard/admin/dashboard.php?tab=testimoni');
exit;