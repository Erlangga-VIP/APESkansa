<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APEskansa - Marketplace Siswa SMKN 1 Bawang</title>
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
                        <li class="nav-item"><a href="index.php" class="nav-link active">Beranda</a></li>
                        <li class="nav-item"><a href="produk.php" class="nav-link">Produk</a></li>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Marketplace Siswa SMKN 1 Bawang</h1>
                    <p>Platform jual beli produk antar siswa yang aman, mudah, dan terpercaya.</p>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-primary">Mulai Jualan Sekarang</a>
                        <a href="produk.php" class="btn btn-secondary">Lihat Produk</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="img/hero-illustration.svg" alt="Ilustrasi Marketplace Sekolah">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Fitur Utama</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>Jual Produkmu dengan Mudah</h3>
                    <p>Buat toko online kamu sendiri dan jual produk ke teman-teman sekolah dengan cepat dan mudah.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Temukan Produk dari Temanmu</h3>
                    <p>Cari dan temukan berbagai produk menarik yang dijual oleh teman-teman sekolahmu.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Transaksi Aman dan Cepat di Sekolah</h3>
                    <p>Lakukan transaksi dengan aman melalui sistem COD (Cash On Delivery) di lingkungan sekolah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">Testimoni Siswa</h2>
            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"APEskansa membantu saya menjual hasil kerajinan tangan ke teman-teman sekolah. Sangat mudah digunakan!"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="img/testimonial-1.svg" alt="Foto Siswa">
                        <div class="author-info">
                            <h4>Anisa Rahma</h4>
                            <p>Kelas XI RPL</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Berkat APEskansa, saya bisa menjual makanan buatan sendiri dan mendapatkan penghasilan tambahan."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="img/testimonial-2.svg" alt="Foto Siswa">
                        <div class="author-info">
                            <h4>Budi Santoso</h4>
                            <p>Kelas XII TKJ</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Platform yang sangat membantu untuk mengembangkan jiwa kewirausahaan siswa di sekolah kami."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="img/testimonial-3.svg" alt="Foto Siswa">
                        <div class="author-info">
                            <h4>Citra Dewi</h4>
                            <p>Kelas X MM</p>
                        </div>
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
                    <img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;">
                    <p>Marketplace Siswa SMKN 1 Bawang</p>
                </div>
                <div class="footer-links">
                    <h3>Navigasi</h3>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="produk.php">Produk</a></li>
                        <li><a href="penjual.php">Penjual</a></li>
                        <li><a href="tentang.php">Tentang Kami</a></li>
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