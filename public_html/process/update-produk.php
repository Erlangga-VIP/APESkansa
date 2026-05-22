<?php
session_start();
include '../../config/config.php';

// Pastikan hanya penjual yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_penjual = $_SESSION['user_id'];
    $produk_id = intval($_POST['produk_id']);
    $nama_produk = $_POST['nama_produk'];
    $kategori = isset($_POST['kategori']) ? $_POST['kategori'] : 'Lainnya';
    $harga = intval($_POST['harga']);
    $deskripsi = $_POST['deskripsi'];
    $gambar_info = $_FILES['gambar'];

    // Validasi kepemilikan produk
    $check_sql = "SELECT gambar FROM produk WHERE produk_id = ? AND user_id = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "ii", $produk_id, $id_penjual);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) == 0) {
        die("<script>alert('Produk tidak ditemukan atau Anda tidak memiliki akses ke produk ini.'); window.location='../penjual-profil.php?tab=produk';</script>");
    }

    $row_produk = mysqli_fetch_assoc($result_check);
    $gambar_lama = $row_produk['gambar'];
    mysqli_stmt_close($stmt_check);

    $target_dir = "../../uploads/";

    // Cek apakah ada file gambar baru yang diunggah
    if (isset($gambar_info) && $gambar_info['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($gambar_info['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            die("<script>alert('Tipe file tidak valid. Harap unggah file gambar (JPG, PNG, GIF, WEBP).'); window.location='../penjual-edit-produk.php?id=" . $produk_id . "';</script>");
        }

        if ($gambar_info['size'] > 2097152) { // 2MB
            die("<script>alert('Ukuran file terlalu besar. Maksimal adalah 2MB.'); window.location='../penjual-edit-produk.php?id=" . $produk_id . "';</script>");
        }

        $gambar_nama_unik = uniqid() . '_' . basename($gambar_info["name"]);
        $target_file = $target_dir . $gambar_nama_unik;

        if (move_uploaded_file($gambar_info["tmp_name"], $target_file)) {
            // Hapus gambar lama dari server jika ada
            if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
                @unlink($target_dir . $gambar_lama);
            }

            // Update database dengan gambar baru
            $sql = "UPDATE produk SET nama_produk = ?, kategori = ?, harga = ?, deskripsi = ?, gambar = ? WHERE produk_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssissii", $nama_produk, $kategori, $harga, $deskripsi, $gambar_nama_unik, $produk_id, $id_penjual);
        } else {
            die("<script>alert('Maaf, terjadi error saat mengunggah gambar baru Anda.'); window.location='../penjual-edit-produk.php?id=" . $produk_id . "';</script>");
        }
    } else {
        // Update database tanpa mengubah gambar
        $sql = "UPDATE produk SET nama_produk = ?, kategori = ?, harga = ?, deskripsi = ? WHERE produk_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisii", $nama_produk, $kategori, $harga, $deskripsi, $produk_id, $id_penjual);
    }

    if ($stmt) {
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Produk berhasil diperbarui!'); window.location='../penjual-profil.php?tab=produk';</script>";
        } else {
            echo "<script>alert('Error: Gagal memperbarui data produk di database.'); window.location='../penjual-edit-produk.php?id=" . $produk_id . "';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error: Gagal menyiapkan statement database.'); window.location='../penjual-edit-produk.php?id=" . $produk_id . "';</script>";
    }

    mysqli_close($conn);
}
?>
