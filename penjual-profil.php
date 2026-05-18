<?php
session_start();
// Include the database configuration
include 'js/php/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Produk - APEskansa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <a href="index.php"><img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;"></a>
            </div>
            <div class="sidebar-menu">
                <a href="penjual-profil.php" class="sidebar-menu-item active">
                    <i class="fas fa-user"></i>
                    <span>Profil & Produk</span>
                </a>
                <a href="penjual-tambah-produk.php" class="sidebar-menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Produk</span>
                </a>
                <a href="#" class="sidebar-menu-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pesanan</span>
                </a>
                <a href="index.php" class="sidebar-menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Profil & Produk Saya</h1>
                <div class="user-info">
                    <span>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
            </div>

            <!-- Profile Info Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Informasi Akun</h2>
                </div>
                <div class="profile-info">
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    <p><strong>Peran:</strong> <?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></p>
                </div>
            </div>

            <!-- Product List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Daftar Produk Saya</h2>
                    <div class="card-actions">
                         <a href="penjual-tambah-produk.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id_penjual = $_SESSION['user_id'];
                            // Using prepared statement for security
$sql = "SELECT * FROM produk WHERE user_id = ?";
                            if($stmt = mysqli_prepare($conn, $sql)){
                                mysqli_stmt_bind_param($stmt, "i", $id_penjual);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
                                        echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                        echo "<td><img src='uploads/" . htmlspecialchars($row['gambar']) . "' width='80' alt='" . htmlspecialchars($row['nama_produk']) . "'></td>";
                                        echo "<td class='action-buttons'>";
                                        echo "    <button class='btn-icon edit'><i class='fas fa-edit'></i></button>";
                                        echo "    <button class='btn-icon delete'><i class='fas fa-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center;'>Anda belum memiliki produk.</td></tr>";
                                }
                                mysqli_stmt_close($stmt);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>