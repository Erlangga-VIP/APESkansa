<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'pembeli';

// Validasi input
$errors = [];
if ($nama === '') {
    $errors[] = 'Nama lengkap wajib diisi.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password minimal 6 karakter.';
}
if (!in_array($role, ['pembeli', 'penjual'], true)) {
    $role = 'pembeli';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    header('Location: ../register.php');
    exit;
}

// Cek email sudah terdaftar
$stmt = mysqli_prepare($conn, 'SELECT user_id FROM users WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    $_SESSION['error'] = 'Email sudah digunakan.';
    header('Location: ../register.php');
    exit;
}
mysqli_stmt_close($stmt);

// Insert user baru
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, 'INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'ssss', $nama, $email, $hashed_password, $role);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Pendaftaran berhasil. Silakan login.';
    header('Location: ../login.php');
} else {
    error_log('Register error: ' . mysqli_error($conn));
    $_SESSION['error'] = 'Terjadi kesalahan. Silakan coba lagi.';
    header('Location: ../register.php');
}
mysqli_stmt_close($stmt);
exit;