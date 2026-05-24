<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'penjual' && $role !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $redirect = $role === 'admin'
        ? BASE_URL . 'dashboard/admin/dashboard.php?tab=produk'
        : BASE_URL . 'dashboard/penjual/produk.php';
    header('Location: ' . $redirect);
    exit;
}

csrf_require();

$produk_id = (int) ($_POST['produk_id'] ?? 0);

if ($produk_id <= 0) {
    $_SESSION['error'] = 'ID produk tidak valid.';
    $redirect = $role === 'admin'
        ? BASE_URL . 'dashboard/admin/dashboard.php?tab=produk'
        : BASE_URL . 'dashboard/penjual/produk.php';
    header('Location: ' . $redirect);
    exit;
}

if ($role === 'admin') {
    $stmt = mysqli_prepare($conn, 'SELECT gambar FROM produk WHERE produk_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $produk_id);
} else {
    $id_penjual = (int) $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, 'SELECT gambar FROM produk WHERE produk_id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Produk tidak ditemukan atau Anda tidak memiliki akses.';
    $redirect = $role === 'admin'
        ? BASE_URL . 'dashboard/admin/dashboard.php?tab=produk'
        : BASE_URL . 'dashboard/penjual/produk.php';
    header('Location: ' . $redirect);
    exit;
}

$row = mysqli_fetch_assoc($result);
$gambar_produk = $row['gambar'];
mysqli_stmt_close($stmt);

$target_dir = __DIR__ . '/../uploads/';
if (!empty($gambar_produk) && file_exists($target_dir . $gambar_produk)) {
    unlink($target_dir . $gambar_produk);
}

if ($role === 'admin') {
    $stmt = mysqli_prepare($conn, 'DELETE FROM produk WHERE produk_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $produk_id);
} else {
    $stmt = mysqli_prepare($conn, 'DELETE FROM produk WHERE produk_id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Produk berhasil dihapus.';
} else {
    error_log('Hapus produk gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal menghapus produk. Silakan coba lagi.';
}
mysqli_stmt_close($stmt);

$redirect = $role === 'admin'
    ? BASE_URL . 'dashboard/admin/dashboard.php?tab=produk'
    : BASE_URL . 'dashboard/penjual/produk.php';
header('Location: ' . $redirect);
exit;
