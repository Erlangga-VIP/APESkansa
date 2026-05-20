<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pembeli') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $rating = (int)$_POST['rating'];
    
    // Validasi rating
    if ($rating < 1 || $rating > 5) $rating = 5;

    $gambar_nama = null;

    // Proses upload gambar testimoni jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $gambar_info = $_FILES['gambar'];
        $target_dir = "../../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($gambar_info['tmp_name']);
        
        if (in_array($file_type, $allowed_types)) {
            $gambar_nama = "testi_" . $user_id . "_" . time() . "_" . basename($gambar_info["name"]);
            $target_file = $target_dir . $gambar_nama;
            move_uploaded_file($gambar_info["tmp_name"], $target_file);
        }
    }

    $sql = "INSERT INTO testimoni (user_id, isi, rating, gambar) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "isis", $user_id, $isi, $rating, $gambar_nama);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Testimoni Anda berhasil dikirim! Terima kasih atas dukungannya.'); window.location='../../profil.php?tab=testimoni';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan testimoni.'); window.location='../../profil.php?tab=testimoni';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Gagal menyiapkan statement database.'); window.location='../../profil.php?tab=testimoni';</script>";
    }
    
    mysqli_close($conn);
}
?>
