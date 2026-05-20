<?php
session_start();
include 'config.php';

// Pastikan hanya penjual yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../../login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_penjual = $_SESSION['user_id'];
    $produk_id = intval($_GET['id']);

    // Validasi kepemilikan produk dan ambil nama berkas gambar
    $check_sql = "SELECT gambar FROM produk WHERE produk_id = ? AND user_id = ?";
    if ($stmt_check = mysqli_prepare($conn, $check_sql)) {
        mysqli_stmt_bind_param($stmt_check, "ii", $produk_id, $id_penjual);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $row_produk = mysqli_fetch_assoc($result_check);
            $gambar_produk = $row_produk['gambar'];
            mysqli_stmt_close($stmt_check);

            // Hapus gambar dari server jika ada
            $target_dir = "../../uploads/";
            if (!empty($gambar_produk) && file_exists($target_dir . $gambar_produk)) {
                @unlink($target_dir . $gambar_produk);
            }

            // Hapus record produk dari database
            $delete_sql = "DELETE FROM produk WHERE produk_id = ? AND user_id = ?";
            if ($stmt_delete = mysqli_prepare($conn, $delete_sql)) {
                mysqli_stmt_bind_param($stmt_delete, "ii", $produk_id, $id_penjual);
                
                if (mysqli_stmt_execute($stmt_delete)) {
                    echo "<script>alert('Produk berhasil dihapus!'); window.location='../../penjual-profil.php?tab=produk';</script>";
                } else {
                    echo "<script>alert('Error: Gagal menghapus produk dari database.'); window.location='../../penjual-profil.php?tab=produk';</script>";
                }
                mysqli_stmt_close($stmt_delete);
            } else {
                echo "<script>alert('Error: Gagal menyiapkan query penghapusan database.'); window.location='../../penjual-profil.php?tab=produk';</script>";
            }
        } else {
            mysqli_stmt_close($stmt_check);
            echo "<script>alert('Produk tidak ditemukan atau Anda tidak memiliki akses ke produk ini.'); window.location='../../penjual-profil.php?tab=produk';</script>";
        }
    } else {
        echo "<script>alert('Error: Gagal menyiapkan query validasi kepemilikan produk.'); window.location='../../penjual-profil.php?tab=produk';</script>";
    }
} else {
    header("Location: ../../penjual-profil.php?tab=produk");
    exit;
}

mysqli_close($conn);
?>
