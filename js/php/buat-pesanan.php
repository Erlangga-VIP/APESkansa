<?php
session_start();
include 'config.php';

// Pastikan pembeli sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pembeli_id = $_SESSION['user_id'];
    $produk_id = intval($_POST['produk_id']);
    $jumlah = intval($_POST['jumlah']);
    $catatan = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';

    if ($jumlah <= 0) {
        die("<script>alert('Jumlah pesanan harus minimal 1.'); window.history.back();</script>");
    }

    // Ambil detail produk untuk mendapatkan harga dan penjual_id
    $produk_sql = "SELECT harga, user_id FROM produk WHERE produk_id = ?";
    if ($stmt_prod = mysqli_prepare($conn, $produk_sql)) {
        mysqli_stmt_bind_param($stmt_prod, "i", $produk_id);
        mysqli_stmt_execute($stmt_prod);
        $result_prod = mysqli_stmt_get_result($stmt_prod);

        if (mysqli_num_rows($result_prod) > 0) {
            $row_prod = mysqli_fetch_assoc($result_prod);
            $harga = $row_prod['harga'];
            $penjual_id = $row_prod['user_id'];
            mysqli_stmt_close($stmt_prod);

            // Pembeli tidak boleh membeli barangnya sendiri
            if ($pembeli_id == $penjual_id) {
                die("<script>alert('Anda tidak bisa membeli produk Anda sendiri.'); window.history.back();</script>");
            }

            // Hitung total harga secara aman di server
            $total_harga = $harga * $jumlah;

            // Simpan pesanan ke database
            $order_sql = "INSERT INTO pesanan (produk_id, pembeli_id, penjual_id, jumlah, total_harga, status, catatan) VALUES (?, ?, ?, ?, ?, 'menunggu', ?)";
            if ($stmt_order = mysqli_prepare($conn, $order_sql)) {
                mysqli_stmt_bind_param($stmt_order, "iiiiis", $produk_id, $pembeli_id, $penjual_id, $jumlah, $total_harga, $catatan);

                if (mysqli_stmt_execute($stmt_order)) {
                    echo "<script>alert('Pesanan Anda berhasil dikirim ke Penjual! Silakan pantau status pesanan di halaman Profil.'); window.location='../../profil.php?tab=pesanan';</script>";
                } else {
                    echo "<script>alert('Error: Gagal memproses pesanan Anda ke database.'); window.history.back();</script>";
                }
                mysqli_stmt_close($stmt_order);
            } else {
                echo "<script>alert('Error: Gagal memuat query pembuatan pesanan.'); window.history.back();</script>";
            }
        } else {
            mysqli_stmt_close($stmt_prod);
            echo "<script>alert('Produk tidak ditemukan.'); window.location='../../produk.php';</script>";
        }
    } else {
        echo "<script>alert('Error: Gagal memuat statement pencarian produk.'); window.history.back();</script>";
    }

    mysqli_close($conn);
} else {
    header("Location: ../../produk.php");
    exit;
}
?>
