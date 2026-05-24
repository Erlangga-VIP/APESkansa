<?php

declare(strict_types=1);

/**
 * Inisialisasi dashboard penjual: auth, data user, statistik toko.
 * Set $penjual_active sebelum require (index|profil|produk|tambah|pesanan|edit).
 */

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'penjual') {
    header('Location: ' . page_url('login.php'));
    exit;
}

$id_penjual = (int) $_SESSION['user_id'];
$penjual_active = $penjual_active ?? '';

$stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
mysqli_stmt_close($stmt);

$user_initial = mb_substr($user_data['nama'] ?? '', 0, 1);
$foto_profil = !empty($user_data['foto_profil']) ? upload_url($user_data['foto_profil']) : null;
$no_hp = htmlspecialchars($user_data['no_hp'] ?? '', ENT_QUOTES, 'UTF-8');

$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM produk WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$total_produk = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE penjual_id = ? AND status = 'diproses'");
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$pesanan_proses = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE penjual_id = ? AND status = 'menunggu'");
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$pesanan_menunggu = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(total_harga), 0) AS total FROM pesanan WHERE penjual_id = ? AND status = 'selesai'");
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$omset = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);
