<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    
    // Default redirect target based on role
    $redirect_url = "../profil.php";
    if ($_SESSION['role'] == 'penjual') {
        $redirect_url = "../penjual-profil.php";
    }

    // 1. Cek apakah email yang dimasukkan sudah digunakan oleh user lain
    $check_email = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email' AND user_id != $user_id");
    if (mysqli_num_rows($check_email) > 0) {
        die("<script>alert('Email sudah digunakan oleh orang lain!'); window.location='$redirect_url';</script>");
    }

    $foto_nama_baru = null;

    // 2. Proses upload foto profil jika ada
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $foto_info = $_FILES['foto_profil'];
        $target_dir = "../../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($foto_info['tmp_name']);
        
        if (in_array($file_type, $allowed_types)) {
            // Hapus foto profil lama jika ada
            $user_q = mysqli_query($conn, "SELECT foto_profil FROM users WHERE user_id = $user_id");
            if ($user_row = mysqli_fetch_assoc($user_q)) {
                $foto_lama = $target_dir . $user_row['foto_profil'];
                if ($user_row['foto_profil'] && file_exists($foto_lama)) {
                    unlink($foto_lama);
                }
            }

            $foto_nama_baru = "avatar_" . $user_id . "_" . time() . "_" . basename($foto_info["name"]);
            $target_file = $target_dir . $foto_nama_baru;
            move_uploaded_file($foto_info["tmp_name"], $target_file);
        } else {
            die("<script>alert('Tipe file tidak valid. Hanya menerima JPG, PNG, WEBP.'); window.location='$redirect_url';</script>");
        }
    }

    // 3. Bangun query update
    if ($foto_nama_baru) {
        $sql = "UPDATE users SET nama = ?, email = ?, no_hp = ?, foto_profil = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $nama, $email, $no_hp, $foto_nama_baru, $user_id);
    } else {
        $sql = "UPDATE users SET nama = ?, email = ?, no_hp = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $no_hp, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Update session
        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;
        
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='$redirect_url';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui profil.'); window.location='$redirect_url';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
