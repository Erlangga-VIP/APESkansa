<?php
session_start();
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
    <title>Tambah Produk - APEskansa</title>
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
                <a href="penjual-profil.php" class="sidebar-menu-item">
                    <i class="fas fa-user"></i>
                    <span>Profil & Produk</span>
                </a>
                <a href="penjual-tambah-produk.php" class="sidebar-menu-item active">
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
                <h1 class="dashboard-title">Tambah Produk Baru</h1>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
            </div>

            <!-- Add Product Form Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Detail Produk</h2>
                </div>
                <form action="js/php/tambah-produk.php" method="POST" enctype="multipart/form-data" class="product-form">
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" class="form-control" placeholder="Contoh: Brownies Coklat" required>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" id="harga" name="harga" class="form-control" placeholder="Contoh: 15000" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Produk</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan produk Anda di sini..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar Produk</label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" required>
                        <small>Unggah gambar produk Anda. Ukuran maksimal 2MB.</small>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                        <a href="penjual-dashboard.php" class="btn btn-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>