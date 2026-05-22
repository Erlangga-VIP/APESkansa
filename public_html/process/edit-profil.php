<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = trim($_POST['nama'] ?? '');
$email   = trim($_POST['email'] ?? '');
$no_hp   = trim($_POST['no_hp'] ?? '');

// Validasi input
$errors = [];
if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    $redirect = ($_SESSION['role'] === 'penjual')
        ? '../dashboard/penjual/profil.php'
        : '../dashboard/pembeli/profil.php';
    header('Location: ' . $redirect);
    exit;
}

// Cek keunikan email
$stmt = mysqli_prepare($conn, 'SELECT user_id FROM users WHERE email = ? AND user_id != ?');
mysqli_stmt_bind_param($stmt, 'si', $email, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    $_SESSION['error'] = 'Email sudah digunakan oleh orang lain.';
    $redirect = ($_SESSION['role'] === 'penjual')
        ? '../dashboard/penjual/profil.php'
        : '../dashboard/pembeli/profil.php';
    header('Location: ' . $redirect);
    exit;
}
mysqli_stmt_close($stmt);

// Proses upload foto
$foto_baru = null;
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto_profil'];
    $max_size = 2 * 1024 * 1024; // 2 MB
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    $file_size = $file['size'];

    if (!in_array($file_type, $allowed_types, true)) {
        $_SESSION['error'] = 'Tipe file tidak valid (JPG, PNG, GIF, WEBP).';
        $redirect = ($_SESSION['role'] === 'penjual')
            ? '../dashboard/penjual/profil.php'
            : '../dashboard/pembeli/profil.php';
        header('Location: ' . $redirect);
        exit;
    }

    if ($file_size > $max_size) {
        $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2 MB.';
        $redirect = ($_SESSION['role'] === 'penjual')
            ? '../dashboard/penjual/profil.php'
            : '../dashboard/pembeli/profil.php';
        header('Location: ' . $redirect);
        exit;
    }

    $target_dir = __DIR__ . '/../uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Hapus foto lama
    $stmt = mysqli_prepare($conn, 'SELECT foto_profil FROM users WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['foto_profil'])) {
            $path_lama = $target_dir . $row['foto_profil'];
            if (file_exists($path_lama)) {
                unlink($path_lama);
            }
        }
    }
    mysqli_stmt_close($stmt);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $foto_baru = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $target_dir . $foto_baru)) {
        $_SESSION['error'] = 'Gagal mengunggah foto.';
        $redirect = ($_SESSION['role'] === 'penjual')
            ? '../dashboard/penjual/profil.php'
            : '../dashboard/pembeli/profil.php';
        header('Location: ' . $redirect);
        exit;
    }
}

// Update database
if ($foto_baru !== null) {
    $stmt = mysqli_prepare($conn, 'UPDATE users SET nama = ?, email = ?, no_hp = ?, foto_profil = ? WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ssssi', $nama, $email, $no_hp, $foto_baru, $user_id);
} else {
    $stmt = mysqli_prepare($conn, 'UPDATE users SET nama = ?, email = ?, no_hp = ? WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'sssi', $nama, $email, $no_hp, $user_id);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['nama'] = $nama;
    $_SESSION['email'] = $email;
    $_SESSION['success'] = 'Profil berhasil diperbarui.';
} else {
    error_log('Edit profil gagal: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Terjadi kesalahan. Silakan coba lagi.';
}
mysqli_stmt_close($stmt);

$redirect = ($_SESSION['role'] === 'penjual')
    ? '../dashboard/penjual/profil.php'
    : '../dashboard/pembeli/profil.php';
header('Location: ' . $redirect);
exit;