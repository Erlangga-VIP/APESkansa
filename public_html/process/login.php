<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

csrf_require();

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Email dan password wajib diisi.';
    header('Location: ../login.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT user_id, nama, email, password, role FROM users WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    $_SESSION['error'] = 'Email tidak ditemukan.';
    header('Location: ../login.php');
    exit;
}

if (!password_verify($password, $row['password'])) {
    $_SESSION['error'] = 'Password salah.';
    header('Location: ../login.php');
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = $row['user_id'];
$_SESSION['nama']    = $row['nama'];
$_SESSION['email']   = $row['email'];
$_SESSION['role']    = $row['role'];

switch ($row['role']) {
    case 'admin':
        header('Location: ../dashboard/admin/dashboard.php');
        break;
    case 'penjual':
        header('Location: ../dashboard/penjual/index.php');
        break;
    default:
        header('Location: ../index.php');
}
exit;