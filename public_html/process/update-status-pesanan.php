<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$redirect_pesanan = BASE_URL . 'dashboard/penjual/pesanan.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect_pesanan);
    exit;
}

csrf_require();

$pesanan_id = (int) ($_POST['pesanan_id'] ?? 0);
$status     = trim($_POST['status'] ?? '');

$valid_statuses = ['menunggu', 'diproses', 'selesai', 'dibatalkan'];

if ($pesanan_id <= 0 || !in_array($status, $valid_statuses, true)) {
    $_SESSION['error'] = 'Data tidak valid.';
    header('Location: ' . $redirect_pesanan);
    exit;
}

// Pastikan pesanan adalah milik penjual yang sedang login
$stmt = mysqli_prepare($conn, 'SELECT pesanan_id FROM pesanan WHERE pesanan_id = ? AND penjual_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $pesanan_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke pesanan ini.';
    header('Location: ' . $redirect_pesanan);
    exit;
}
mysqli_stmt_close($stmt);

// Update status
$stmt = mysqli_prepare($conn, 'UPDATE pesanan SET status = ? WHERE pesanan_id = ?');
mysqli_stmt_bind_param($stmt, 'si', $status, $pesanan_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Status pesanan berhasil diperbarui.';
} else {
    error_log('Gagal update status pesanan: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal memperbarui status. Silakan coba lagi.';
}
mysqli_stmt_close($stmt);

header('Location: ' . $redirect_pesanan);
exit;