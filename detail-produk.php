<?php
session_start();
include 'js/php/config.php'; // Koneksi ke database

// Ambil ID produk dari URL dan pastikan itu adalah angka
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id == 0) {
    // Jika tidak ada ID atau ID bukan angka, hentikan skrip
die("Error: Produk tidak valid.");
}

// Ambil detail produk dari database menggunakan prepared statement untuk keamanan
$sql = "SELECT p.*, u.nama AS nama_penjual FROM produk p JOIN users u ON p.user_id = u.user_id WHERE p.produk_id = ?";
$product = null;
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Jika produk dengan ID tersebut tidak ditemukan di database, hentikan skrip
if ($product === null) {
    die("Produk tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - APEskansa</title>
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
                    <a href="index.php"><img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;"></a>
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
                    <span class="bar"></span><span class="bar"></span><span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Product Detail Section -->
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-container">
                <div class="product-detail-image">
                    <img src="uploads/<?php echo htmlspecialchars($product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                </div>
                <div class="product-detail-info">
                    <h1><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                    <p class="product-detail-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                    
                    <div class="product-detail-seller">
                        <div class="seller-avatar">
                            <img src="img/testimonial-1.svg" alt="Foto Penjual"> <!-- Placeholder avatar -->
                        </div>
                        <div class="seller-info">
                            <h3><?php echo htmlspecialchars($product['nama_penjual']); ?></h3>
                        </div>
                    </div>
                    
                    <div class="product-detail-description">
                        <h2>Deskripsi Produk</h2>
                        <p><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
                    </div>
                    
                    <div class="product-detail-buttons">
                        <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 'pembeli') {
                            echo '<a href="#" class="btn btn-primary">Pesan Sekarang</a>';
                            echo '<a href="#" class="btn btn-outline">Hubungi Penjual</a>';
                        } elseif (isset($_SESSION['role']) && ($_SESSION['role'] == 'penjual' || $_SESSION['role'] == 'admin')) {
                            // Penjual atau admin tidak bisa membeli, jadi tidak ada tombol yang ditampilkan
                        } else {
                            echo '<a href="login.php" class="btn btn-primary">Login untuk Memesan</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
<img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
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