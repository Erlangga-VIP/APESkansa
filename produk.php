<?php
session_start();
include 'js/php/config.php'; // Koneksi ke database
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - APEskansa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
                    </a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li class="nav-item"><a href="index.php" class="nav-link">Beranda</a></li>
                        <li class="nav-item"><a href="produk.php" class="nav-link active">Produk</a></li>
                        <li class="nav-item"><a href="penjual.php" class="nav-link">Penjual</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])):
                        $dashboard_link = 'profil.php';
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] == 'penjual') {
                                $dashboard_link = 'penjual-profil.php';
                            } elseif ($_SESSION['role'] == 'admin') {
                                $dashboard_link = 'admin-dashboard.php';
                            }
                        }
                        $user_initial = isset($_SESSION['nama']) ? substr($_SESSION['nama'], 0, 1) : 'U';
                    ?>
                        <a href="<?php echo $dashboard_link; ?>" class="profile-icon" title="Profil Saya">
                            <div class="avatar-circle"><?php echo strtoupper(htmlspecialchars($user_initial)); ?></div>
                        </a>
                        <a href="js/php/logout.php" class="btn btn-outline">Keluar</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Masuk</a>
                        <a href="register.php" class="btn btn-primary">Daftar</a>
                    <?php endif; ?>
                </div>
                <button class="mobile-menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Products Section -->
    <section class="products">
        <div class="container">
            <h1 class="section-title">Produk Siswa</h1>
            
            <!-- Products Grid -->
            <div class="products-grid">
                <?php
                // Ambil semua produk dan join dengan tabel user untuk mendapatkan nama penjual
                // Diurutkan berdasarkan nama produk karena kemungkinan ada kesalahan penamaan kolom id_produk di database
$sql = "SELECT p.*, u.nama AS nama_penjual FROM produk p JOIN users u ON p.user_id = u.user_id ORDER BY p.nama_produk ASC";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title"><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                                <p class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                <p class="product-seller">Oleh: <?php echo htmlspecialchars($row['nama_penjual']); ?></p>
                                <div class="product-buttons">
                                    <?php
                                    // Logika untuk menampilkan tombol berdasarkan peran pengguna
if (isset($_SESSION['role']) && $_SESSION['role'] == 'pembeli') {
                                        // Jika login sebagai pembeli, tampilkan tombol beli
                                        echo '<a href="detail-produk.php?id=' . $row['produk_id'] . '" class="btn btn-primary">Beli Sekarang</a>';
                                    } elseif (isset($_SESSION['role']) && ($_SESSION['role'] == 'penjual' || $_SESSION['role'] == 'admin')) {
                                    } elseif (isset($_SESSION['role']) && ($_SESSION['role'] == 'penjual' || $_SESSION['role'] == 'admin')) {
                                        // Jika login sebagai penjual atau admin, tampilkan tombol lihat detail
echo '<a href="detail-produk.php?id=' . $row['produk_id'] . '" class="btn btn-outline">Lihat Detail</a>';
                                    } else {
                                        // Jika tidak login, tampilkan tombol untuk login
                                        echo '<a href="login.php" class="btn btn-primary">Login untuk Membeli</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>Belum ada produk yang tersedia.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;">
                    <p>Marketplace Siswa SMKN 1 Bawang</p>
                </div>
                <div class="footer-links">
                    <h3>Navigasi</h3>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="produk.php">Produk</a></li>
                        <li><a href="penjual.php">Penjual</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Kontak</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Jl. Raya Bawang, Banjarnegara</li>
                        <li><i class="fas fa-phone"></i> (0286) 591256</li>
                        <li><i class="fas fa-envelope"></i> info@smkn1bawang.sch.id</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Media Sosial</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 APEskansa - SMKN 1 Bawang. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>