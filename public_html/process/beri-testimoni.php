<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
    exit;
}

csrf_require();

$user_id = (int) $_SESSION['user_id'];
$isi     = trim($_POST['isi'] ?? '');
$rating  = (int) ($_POST['rating'] ?? 5);

if ($isi === '') {
    $_SESSION['error'] = 'Isi testimoni tidak boleh kosong.';
    header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
    exit;
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

$gambar_nama = null;

// Proses upload gambar jika ada
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file          = $_FILES['gambar'];
    $max_size      = 2 * 1024 * 1024;
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type     = mime_content_type($file['tmp_name']);
    $file_size     = $file['size'];

    if (!in_array($file_type, $allowed_types, true)) {
        $_SESSION['error'] = 'Tipe file tidak valid (JPG, PNG, GIF, WEBP).';
        header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
        exit;
    }

    if ($file_size > $max_size) {
        $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2 MB.';
        header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
        exit;
    }

    $target_dir = __DIR__ . '/../uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ext = allowed_image_extension($file_type);
    if ($ext === null) {
        $_SESSION['error'] = 'Tipe file tidak valid (JPG, PNG, GIF, WEBP).';
        header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
        exit;
    }
    $gambar_nama = 'testi_' . $user_id . '_' . time() . '.' . $ext;
    $target_file = $target_dir . $gambar_nama;

    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        $_SESSION['error'] = 'Gagal mengunggah gambar.';
        header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
        exit;
    }
}

$stmt = mysqli_prepare($conn, 'INSERT INTO testimoni (user_id, isi, rating, gambar) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'isis', $user_id, $isi, $rating, $gambar_nama);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Testimoni berhasil dikirim.';
} else {
    if ($gambar_nama !== null && file_exists($target_file)) {
        unlink($target_file);
    }
    error_log('Testimoni gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Gagal menyimpan testimoni. Silakan coba lagi.';
}
mysqli_stmt_close($stmt);

header('Location: ../dashboard/pembeli/profil.php?tab=testimoni');
exit;