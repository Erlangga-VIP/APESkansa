<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=testimoni');
    exit;
}

csrf_require();

$testimoni_id = (int) ($_POST['testimoni_id'] ?? 0);

if ($testimoni_id <= 0) {
    header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=testimoni');
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT gambar FROM testimoni WHERE testimoni_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $testimoni_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $gambar = $row['gambar'];
    if (!empty($gambar)) {
        $path = __DIR__ . '/../uploads/' . $gambar;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, 'DELETE FROM testimoni WHERE testimoni_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $testimoni_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Testimoni berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus testimoni.';
}

mysqli_stmt_close($stmt);
header('Location: ' . BASE_URL . 'dashboard/admin/dashboard.php?tab=testimoni');
exit;
