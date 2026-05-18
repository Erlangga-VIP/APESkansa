<?php
session_start();
include 'config.php';

// Pastikan hanya penjual yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_penjual = $_SESSION['user_id'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $gambar_info = $_FILES['gambar'];

    $target_dir = "../../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if ($gambar_info['error'] !== UPLOAD_ERR_OK) {
        die("<script>alert('Terjadi error saat mengunggah gambar.'); window.location='../../penjual-tambah-produk.php';</script>");
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($gambar_info['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        die("<script>alert('Tipe file tidak valid. Harap unggah file gambar (JPG, PNG, GIF, WEBP).'); window.location='../../penjual-tambah-produk.php';</script>");
    }

    $gambar_nama_unik = uniqid() . '_' . basename($gambar_info["name"]);
    $target_file = $target_dir . $gambar_nama_unik;

    if (move_uploaded_file($gambar_info["tmp_name"], $target_file)) {
        $sql = "INSERT INTO produk (user_id, nama_produk, harga, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "isiss", $id_penjual, $nama_produk, $harga, $deskripsi, $gambar_nama_unik);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Produk berhasil ditambahkan!'); window.location='../../penjual-dashboard.php';</script>";
            } else {
                echo "<script>alert('Error: Tidak bisa menyimpan data ke database.'); window.location='../../penjual-tambah-produk.php';</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error: Gagal menyiapkan statement database.'); window.location='../../penjual-tambah-produk.php';</script>";
        }
    } else {
        echo "<script>alert('Maaf, terjadi error saat mengunggah file gambar Anda.'); window.location='../../penjual-tambah-produk.php';</script>";
    }

    mysqli_close($conn);
}
?>
