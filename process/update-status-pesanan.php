<?php
session_start();
include '../../config/config.php';

// Pastikan yang login adalah penjual atau admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'penjual' && $_SESSION['role'] != 'admin')) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['action'])) {
    $pesanan_id = isset($_POST['pesanan_id']) ? (int)$_POST['pesanan_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : (isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '');
    
    $valid_statuses = ['menunggu', 'diproses', 'selesai', 'dibatalkan'];
    if ($pesanan_id == 0 || !in_array($status, $valid_statuses)) {
        die("<script>alert('Data tidak valid.'); window.location='../penjual-profil.php';</script>");
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Jika penjual, verifikasi bahwa pesanan ini ditujukan untuk produk milik penjual ini
    if ($role == 'penjual') {
        $check_sql = "SELECT pesanan_id FROM pesanan WHERE pesanan_id = ? AND penjual_id = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "ii", $pesanan_id, $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($res) == 0) {
            die("<script>alert('Anda tidak memiliki akses ke pesanan ini!'); window.location='../penjual-profil.php';</script>");
        }
        mysqli_stmt_close($stmt);
    }

    // Lakukan update status pesanan
    $update_sql = "UPDATE pesanan SET status = ? WHERE pesanan_id = ?";
    if ($stmt = mysqli_prepare($conn, $update_sql)) {
        mysqli_stmt_bind_param($stmt, "si", $status, $pesanan_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Status pesanan berhasil diperbarui menjadi " . ucfirst($status) . "!'); window.location='../penjual-profil.php?tab=pesanan';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui status pesanan.'); window.location='../penjual-profil.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>
