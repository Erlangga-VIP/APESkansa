<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // Simpan data user ke session
                $_SESSION['user_id'] = $user['user_id']; // Fixed bug from 'id' to 'user_id'
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Arahkan berdasarkan peran
                if ($user['role'] == 'admin') {
                    header("Location: ../../admin-dashboard.php");
                } elseif ($user['role'] == 'penjual') {
                    header("Location: ../../penjual-dashboard.php");
                } else {
                    header("Location: ../../index.php");
                }
                exit;
            } else {
                echo "<script>alert('Password salah!'); window.location='../../login.php';</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!'); window.location='../../login.php';</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.'); window.location='../../login.php';</script>";
    }
}
?>
