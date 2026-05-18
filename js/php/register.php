<?php
include 'config.php'; // koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Cek apakah email sudah terdaftar
    $check_query = "SELECT email FROM users WHERE email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result) {
        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Email sudah digunakan!'); window.location='../../register.php';</script>";
        } else {
            $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='../../login.php';</script>";
            } else {
                // Tampilkan pesan error umum ke pengguna, dan log error detail di server
                error_log("Error: " . $sql . "\n" . mysqli_error($conn));
                echo "<script>alert('Terjadi kesalahan saat pendaftaran. Silakan coba lagi nanti.'); window.location='../../register.php';</script>";
            }
        }
    } else {
        // Tampilkan pesan error umum ke pengguna, dan log error detail di server
        error_log("Error: " . $check_query . "\n" . mysqli_error($conn));
        echo "<script>alert('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.'); window.location='../../register.php';</script>";
    }
}
?>
