<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'pembeli') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . page_url('index.php'));
    exit;
}

csrf_require();

$pembeli_id = (int) $_SESSION['user_id'];
$produk_id  = (int) ($_POST['produk_id'] ?? 0);
$jumlah     = (int) ($_POST['jumlah'] ?? 0);
$catatan    = trim($_POST['catatan'] ?? '');

if ($produk_id <= 0 || $jumlah <= 0) {
    $_SESSION['error'] = 'Data pesanan tidak valid.';
    header('Location: ' . page_url('produk.php'));
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT harga, user_id FROM produk WHERE produk_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $produk_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    $_SESSION['error'] = 'Produk tidak ditemukan.';
    header('Location: ' . page_url('produk.php'));
    exit;
}
mysqli_stmt_close($stmt);

if ($pembeli_id === (int) $row['user_id']) {
    $_SESSION['error'] = 'Anda tidak bisa membeli produk sendiri.';
    header('Location: ' . page_url('detail-produk.php?id=' . $produk_id));
    exit;
}

$total_harga = $row['harga'] * $jumlah;
$penjual_id  = (int) $row['user_id'];

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO pesanan (produk_id, pembeli_id, penjual_id, jumlah, total_harga, status, catatan)
     VALUES (?, ?, ?, ?, ?, 'menunggu', ?)"
);
mysqli_stmt_bind_param($stmt, 'iiiiis', $produk_id, $pembeli_id, $penjual_id, $jumlah, $total_harga, $catatan);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Pesanan berhasil dikirim.';
    header('Location: ' . page_url('dashboard/pembeli/profil.php?tab=pesanan'));
} else {
    error_log('Buat pesanan gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal memproses pesanan. Silakan coba lagi.';
    header('Location: ' . page_url('detail-produk.php?id=' . $produk_id));
}
mysqli_stmt_close($stmt);
exit;