<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
    exit;
}

$id_penjual = $_SESSION['user_id'];
$produk_id  = (int) $_GET['id'];

if ($produk_id <= 0) {
    $_SESSION['error'] = 'ID produk tidak valid.';
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
    exit;
}

// Verifikasi kepemilikan dan ambil nama gambar
$stmt = mysqli_prepare($conn, 'SELECT gambar FROM produk WHERE produk_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Produk tidak ditemukan atau Anda tidak memiliki akses.';
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
    exit;
}

$row           = mysqli_fetch_assoc($result);
$gambar_produk = $row['gambar'];
mysqli_stmt_close($stmt);

// Hapus gambar dari server
$target_dir = __DIR__ . '/../uploads/';
if (!empty($gambar_produk) && file_exists($target_dir . $gambar_produk)) {
    unlink($target_dir . $gambar_produk);
}

// Hapus record dari database
$stmt = mysqli_prepare($conn, 'DELETE FROM produk WHERE produk_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Produk berhasil dihapus.';
} else {
    error_log('Hapus produk gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal menghapus produk. Silakan coba lagi.';
}
mysqli_stmt_close($stmt);

header('Location: ../dashboard/penjual/profil.php?tab=produk');
exit;