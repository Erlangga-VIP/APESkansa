<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
    exit;
}

$id_penjual  = $_SESSION['user_id'];
$produk_id   = (int) ($_POST['produk_id'] ?? 0);
$nama_produk = trim($_POST['nama_produk'] ?? '');
$kategori    = trim($_POST['kategori'] ?? '');
$harga       = (int) ($_POST['harga'] ?? 0);
$deskripsi   = trim($_POST['deskripsi'] ?? '');

// Validasi kategori
$kategori_valid = ['Makanan', 'Minuman', 'Kerajinan', 'Jasa', 'Lainnya'];
if (!in_array($kategori, $kategori_valid, true)) {
    $kategori = 'Lainnya';
}

// Validasi input
$errors = [];
if ($produk_id <= 0) {
    $errors[] = 'ID produk tidak valid.';
}
if ($nama_produk === '') {
    $errors[] = 'Nama produk wajib diisi.';
}
if ($harga <= 0) {
    $errors[] = 'Harga harus lebih dari 0.';
}
if ($deskripsi === '') {
    $errors[] = 'Deskripsi produk wajib diisi.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    header('Location: ../dashboard/penjual/edit-produk.php?id=' . $produk_id);
    exit;
}

// Verifikasi kepemilikan produk
$stmt = mysqli_prepare($conn, 'SELECT gambar FROM produk WHERE produk_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Produk tidak ditemukan atau Anda tidak memiliki akses.';
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
    exit;
}
$row_produk   = mysqli_fetch_assoc($result);
$gambar_lama  = $row_produk['gambar'];
mysqli_stmt_close($stmt);

// Proses upload gambar baru (jika ada)
$gambar_baru = null;
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file      = $_FILES['gambar'];
    $max_size  = 2 * 1024 * 1024; // 2 MB
    $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    $file_size = $file['size'];

    if (!in_array($file_type, $allowed, true)) {
        $_SESSION['error'] = 'Tipe file tidak valid (JPG, PNG, GIF, WEBP).';
        header('Location: ../dashboard/penjual/edit-produk.php?id=' . $produk_id);
        exit;
    }

    if ($file_size > $max_size) {
        $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2 MB.';
        header('Location: ../dashboard/penjual/edit-produk.php?id=' . $produk_id);
        exit;
    }

    $target_dir = __DIR__ . '/../uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ext         = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nama_file   = uniqid('produk_') . '.' . $ext;
    $target_path = $target_dir . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        $_SESSION['error'] = 'Gagal mengunggah gambar baru.';
        header('Location: ../dashboard/penjual/edit-produk.php?id=' . $produk_id);
        exit;
    }

    // Hapus gambar lama
    if (!empty($gambar_lama)) {
        $path_lama = $target_dir . $gambar_lama;
        if (file_exists($path_lama)) {
            unlink($path_lama);
        }
    }

    $gambar_baru = $nama_file;
}

// Update database
if ($gambar_baru !== null) {
    $stmt = mysqli_prepare(
        $conn,
        'UPDATE produk SET nama_produk = ?, kategori = ?, harga = ?, deskripsi = ?, gambar = ? WHERE produk_id = ? AND user_id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'ssissii', $nama_produk, $kategori, $harga, $deskripsi, $gambar_baru, $produk_id, $id_penjual);
} else {
    $stmt = mysqli_prepare(
        $conn,
        'UPDATE produk SET nama_produk = ?, kategori = ?, harga = ?, deskripsi = ? WHERE produk_id = ? AND user_id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'ssisii', $nama_produk, $kategori, $harga, $deskripsi, $produk_id, $id_penjual);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Produk berhasil diperbarui.';
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
} else {
    error_log('Update produk gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal memperbarui produk. Silakan coba lagi.';
    header('Location: ../dashboard/penjual/edit-produk.php?id=' . $produk_id);
}
mysqli_stmt_close($stmt);
exit;