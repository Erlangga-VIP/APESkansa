<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/penjual/tambah-produk.php');
    exit;
}

$id_penjual  = $_SESSION['user_id'];
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
if ($nama_produk === '') {
    $errors[] = 'Nama produk wajib diisi.';
}
if ($harga <= 0) {
    $errors[] = 'Harga harus lebih dari 0.';
}
if ($deskripsi === '') {
    $errors[] = 'Deskripsi produk wajib diisi.';
}
if (empty($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Gambar produk wajib diunggah.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    header('Location: ../dashboard/penjual/tambah-produk.php');
    exit;
}

// Validasi file gambar
$gambar = $_FILES['gambar'];
$max_size = 2 * 1024 * 1024; // 2 MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$file_type = mime_content_type($gambar['tmp_name']);
$file_size = $gambar['size'];

if (!in_array($file_type, $allowed_types, true)) {
    $_SESSION['error'] = 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
    header('Location: ../dashboard/penjual/tambah-produk.php');
    exit;
}

if ($file_size > $max_size) {
    $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2 MB.';
    header('Location: ../dashboard/penjual/tambah-produk.php');
    exit;
}

// Upload gambar
$target_dir = __DIR__ . '/../uploads/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$ext = pathinfo($gambar['name'], PATHINFO_EXTENSION);
$nama_file = uniqid('produk_') . '.' . $ext;
$target_path = $target_dir . $nama_file;

if (!move_uploaded_file($gambar['tmp_name'], $target_path)) {
    $_SESSION['error'] = 'Gagal mengunggah gambar.';
    header('Location: ../dashboard/penjual/tambah-produk.php');
    exit;
}

// Simpan ke database
$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO produk (user_id, nama_produk, kategori, harga, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmt, 'ississ', $id_penjual, $nama_produk, $kategori, $harga, $deskripsi, $nama_file);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Produk berhasil ditambahkan.';
    header('Location: ../dashboard/penjual/profil.php?tab=produk');
} else {
    // Hapus gambar jika gagal simpan
    if (file_exists($target_path)) {
        unlink($target_path);
    }
    error_log('Tambah produk gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal menyimpan produk. Silakan coba lagi.';
    header('Location: ../dashboard/penjual/tambah-produk.php');
}
mysqli_stmt_close($stmt);
exit;