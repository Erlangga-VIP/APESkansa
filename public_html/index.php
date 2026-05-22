<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APEskansa - Marketplace Siswa SMKN 1 Bawang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header/Navbar -->
    <header class="header glass-card">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <img src="assets/img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
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
                        <a href="process/logout.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Keluar</a>
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

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- ========================================== -->
        <!-- BERANDA KHUSUS USER (SETELAH LOGIN)        -->
        <!-- ========================================== -->
        <main class="container" style="padding: 2.5rem 15px; min-height: 80vh;">
            
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-banner-text">
                    <h1>Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>! 👋</h1>
                    <p>Selamat datang kembali di APEskansa. Temukan produk-produk kreasi terbaik dari teman-teman sekolahmu hari ini!</p>
                </div>
            </div>

            <!-- Search Bar Section -->
            <div class="glass-card" style="padding: 1.5rem; border-radius: var(--border-radius); margin-bottom: 2.5rem;">
                <form action="produk.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px; position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" name="cari" class="form-control" placeholder="Cari jajanan, kerajinan, atau jasa di sini..." style="padding-left: 2.75rem; height: 100%; border-radius: 8px;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;"><i class="fas fa-search"></i> Cari</button>
                </form>
            </div>

            <!-- Kategori Produk Section -->
            <section style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--dark-text); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-tags" style="color: var(--primary-color);"></i> Jelajahi Kategori
                </h2>
                <div class="categories-scroll">
                    <button class="category-btn active" onclick="window.location='produk.php'">
                        <i class="fas fa-border-all"></i>
                        <span>Semua</span>
                    </button>
                    <button class="category-btn" onclick="window.location='produk.php?kategori=makanan'">
                        <i class="fas fa-utensils"></i>
                        <span>Makanan</span>
                    </button>
                    <button class="category-btn" onclick="window.location='produk.php?kategori=minuman'">
                        <i class="fas fa-coffee"></i>
                        <span>Minuman</span>
                    </button>
                    <button class="category-btn" onclick="window.location='produk.php?kategori=kerajinan'">
                        <i class="fas fa-shapes"></i>
                        <span>Kerajinan</span>
                    </button>
                    <button class="category-btn" onclick="window.location='produk.php?kategori=jasa'">
                        <i class="fas fa-laptop-code"></i>
                        <span>Jasa</span>
                    </button>
                    <button class="category-btn" onclick="window.location='produk.php?kategori=lainnya'">
                        <i class="fas fa-boxes"></i>
                        <span>Lainnya</span>
                    </button>
                </div>
            </section>

            <!-- Grid Layout for Rekomendasi & Toko Populer -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; margin-bottom: 3rem;" class="form-row">
                
                <!-- Rekomendasi Produk -->
                <section>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--dark-text); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                            <i class="fas fa-fire" style="color: var(--danger-color);"></i> Rekomendasi Produk
                        </h2>
                        <a href="produk.php" style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <div class="products-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem;">
                        <?php
                        $sql = "SELECT p.*, u.nama AS nama_penjual FROM produk p JOIN users u ON p.user_id = u.user_id ORDER BY p.produk_id DESC LIMIT 4";
                        $result = mysqli_query($conn, $sql);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $kat_color = 'badge-waiting';
                                if ($row['kategori'] == 'makanan') $kat_color = 'badge-processing';
                                if ($row['kategori'] == 'minuman') $kat_color = 'badge-completed';
                                if ($row['kategori'] == 'jasa') $kat_color = 'badge-cancelled';
                        ?>
                                <div class="product-card hover-float">
                                    <div class="product-image" style="height: 160px; overflow: hidden; position: relative;">
                                        <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <span class="badge-status <?php echo $kat_color; ?>" style="position: absolute; top: 10px; right: 10px; z-index: 10; font-size: 0.7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px;">
                                            <?php echo ucfirst(htmlspecialchars($row['kategori'])); ?>
                                        </span>
                                    </div>
                                    <div class="product-info" style="padding: 1rem;">
                                        <h3 class="product-title" style="font-size: 1.05rem; margin-bottom: 0.25rem; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?php echo htmlspecialchars($row['nama_produk']); ?>
                                        </h3>
                                        <p class="product-price" style="font-size: 1.1rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem;">
                                            Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                        </p>
                                        <p class="product-seller" style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">
                                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($row['nama_penjual']); ?>
                                        </p>
                                        <div class="product-buttons">
                                            <a href="detail-produk.php?id=<?php echo $row['produk_id']; ?>" class="btn btn-primary" style="width: 100%; padding: 0.5rem; font-size: 0.85rem; font-weight: 600; text-align: center;">Beli Sekarang</a>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p style='color: #64748b;'>Belum ada produk rekomendasi.</p>";
                        }
                        ?>
                    </div>
                </section>

                <!-- Toko Populer / Aktif -->
                <section>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--dark-text); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                            <i class="fas fa-store" style="color: var(--primary-color);"></i> Toko Populer
                        </h2>
                        <a href="penjual.php" style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;">Lihat Semua</a>
                    </div>
                    
                    <div class="glass-card" style="padding: 1.5rem; border-radius: var(--border-radius); display: flex; flex-direction: column; gap: 1.25rem;">
                        <?php
                        $sql_sellers = "SELECT user_id, nama, email, (SELECT COUNT(*) FROM produk WHERE user_id = users.user_id) AS total_produk FROM users WHERE role = 'penjual' LIMIT 4";
                        $res_sellers = mysqli_query($conn, $sql_sellers);
                        if ($res_sellers && mysqli_num_rows($res_sellers) > 0) {
                            while($s_row = mysqli_fetch_assoc($res_sellers)) {
                                $initial = strtoupper(substr($s_row['nama'], 0, 1));
                        ?>
                                <div style="display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;" class="seller-item">
                                    <div class="seller-circle-avatar" style="width: 45px; height: 45px; font-size: 1.25rem; margin-bottom: 0; flex-shrink: 0;">
                                        <?php echo $initial; ?>
                                    </div>
                                    <div style="flex: 1; overflow: hidden;">
                                        <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--dark-text); margin: 0; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                            <?php echo htmlspecialchars($s_row['nama']); ?>
                                        </h4>
                                        <p style="font-size: 0.8rem; color: #64748b; margin: 0;">
                                            <?php echo $s_row['total_produk']; ?> Produk Aktif
                                        </p>
                                    </div>
                                    <a href="produk.php?penjual_id=<?php echo $s_row['user_id']; ?>" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 600; border-radius: 8px;">Kunjungi</a>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p style='color: #64748b;'>Belum ada toko yang terdaftar.</p>";
                        }
                        ?>
                    </div>
                </section>
            </div>
            
        </main>

    <?php else: ?>
        <!-- ========================================== -->
        <!-- LANDING PAGE UTAMA (SEBELUM LOGIN)         -->
        <!-- ========================================== -->
        
        <!-- Hero Section -->
        <section class="hero" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 6rem 0 7rem 0;">
            <div class="container">
                <div class="hero-content" style="align-items: center; gap: 3rem;">
                    <div class="hero-text" style="flex: 1.1;">
                        <span style="background: rgba(79, 70, 229, 0.1); color: var(--primary-color); padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; font-size: 0.9rem; display: inline-block; margin-bottom: 1.5rem; letter-spacing: 0.05em;">SMKN 1 BAWANG MARKETPLACE</span>
                        <h1 style="font-size: 3.25rem; font-weight: 700; line-height: 1.2; margin-bottom: 1.5rem; color: var(--dark-text);">
                            Marketplace Siswa <br><span style="color: var(--primary-color); background: linear-gradient(to right, #4f46e5, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SMKN 1 Bawang</span>
                        </h1>
                        <p style="font-size: 1.25rem; color: #475569; margin-bottom: 2.5rem; line-height: 1.75;">
                            Platform jual beli wirausaha siswa antar jurusan yang aman, praktis, dan terpercaya di lingkungan sekolah. Temukan jajanan lezat hingga kerajinan tangan menarik buatan temanmu!
                        </p>
                        <div class="hero-buttons" style="gap: 1.25rem;">
                            <a href="register.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem; font-weight: 600; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);">Mulai Jualan Sekarang</a>
                            <a href="produk.php" class="btn btn-secondary" style="padding: 1rem 2rem; font-size: 1rem; font-weight: 600; background: white; border: 1px solid var(--border-color); box-shadow: var(--box-shadow);">Lihat Produk</a>
                        </div>
                    </div>
                    <div class="hero-image" style="flex: 0.9; text-align: center;">
                        <img src="assets/img/hero-illustration.svg" alt="Ilustrasi Marketplace Sekolah" style="max-width: 90%; animation: float 4s ease-in-out infinite; filter: drop-shadow(0 15px 30px rgba(15, 23, 42, 0.08));">
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section style="margin-top: -3.5rem; position: relative; z-index: 20; padding: 0 15px;">
            <div class="container" style="max-width: 1000px;">
                <div class="glass-card" style="border-radius: 16px; padding: 2.5rem; display: grid; grid-template-columns: repeat(3, 1fr); text-align: center; gap: 2rem;" class="stats-container">
                    <div>
                        <h3 style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem;">500+</h3>
                        <p style="color: #64748b; font-weight: 500;">Siswa Aktif</p>
                    </div>
                    <div style="border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">
                        <h3 style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem;">200+</h3>
                        <p style="color: #64748b; font-weight: 500;">Produk Kreatif</p>
                    </div>
                    <div>
                        <h3 style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem;">100%</h3>
                        <p style="color: #64748b; font-weight: 500;">Transaksi Aman COD</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" style="padding: 7rem 0; background-color: white;">
            <div class="container">
                <h2 class="section-title">Fitur & Keunggulan Utama</h2>
                <div class="features-grid">
                    <div class="feature-card hover-float" style="padding: 2.5rem 2rem; border-radius: var(--border-radius); border: 1px solid var(--border-color); background: #f8fafc;">
                        <div class="feature-icon" style="background: rgba(79, 70, 229, 0.1); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                            <i class="fas fa-store" style="font-size: 1.75rem; color: var(--primary-color);"></i>
                        </div>
                        <h3>Jual Produkmu dengan Mudah</h3>
                        <p style="color: #475569;">Buka toko online pribadimu dalam hitungan detik! Unggah foto produk, tulis deskripsi menarik, tentukan harga, dan mulailah melayani pelanggan di sekolah.</p>
                    </div>
                    <div class="feature-card hover-float" style="padding: 2.5rem 2rem; border-radius: var(--border-radius); border: 1px solid var(--border-color); background: #f8fafc;">
                        <div class="feature-icon" style="background: rgba(79, 70, 229, 0.1); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                            <i class="fas fa-search" style="font-size: 1.75rem; color: var(--primary-color);"></i>
                        </div>
                        <h3>Temukan Produk dari Temanmu</h3>
                        <p style="color: #475569;">Jelajahi beragam produk berkualitas tinggi dari teman-teman sekolahmu. Cari makanan ringan, minuman menyegarkan, kerajinan tangan, atau jasa belajar/desain.</p>
                    </div>
                    <div class="feature-card hover-float" style="padding: 2.5rem 2rem; border-radius: var(--border-radius); border: 1px solid var(--border-color); background: #f8fafc;">
                        <div class="feature-icon" style="background: rgba(79, 70, 229, 0.1); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                            <i class="fas fa-handshake" style="font-size: 1.75rem; color: var(--primary-color);"></i>
                        </div>
                        <h3>Transaksi Aman COD</h3>
                        <p style="color: #475569;">Tidak perlu cemas soal metode pembayaran! Transaksi diselesaikan secara langsung (COD) di area sekolah di bawah pemantauan tertib OSIS/sekolah.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials" style="padding: 6rem 0; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);">
            <div class="container">
                <h2 class="section-title" style="margin-bottom: 3.5rem;">Testimoni Siswa SMKN 1 Bawang</h2>
                <div class="testimonials-slider" style="display: flex; gap: 2rem; overflow-x: auto; padding-bottom: 1.5rem;">
                    <?php
                    $sql_testi = "SELECT t.*, u.nama, u.role FROM testimoni t JOIN users u ON t.user_id = u.user_id ORDER BY t.testimoni_id DESC LIMIT 6";
                    $res_testi = mysqli_query($conn, $sql_testi);
                    if ($res_testi && mysqli_num_rows($res_testi) > 0) {
                        while ($t_row = mysqli_fetch_assoc($res_testi)) {
                            $initial = strtoupper(substr($t_row['nama'], 0, 1));
                    ?>
                            <div class="testimonial-card hover-float" style="min-width: 320px; background: white; border-radius: var(--border-radius); padding: 2rem; box-shadow: var(--box-shadow); flex: 1;">
                                <div style="color: var(--warning-color); margin-bottom: 1rem; font-size: 1rem;">
                                    <?php for($i=1; $i<=$t_row['rating']; $i++) { echo '<i class="fas fa-star"></i>'; } ?>
                                </div>
                                <?php if (!empty($t_row['gambar'])): ?>
                                    <div style="margin-bottom: 1rem; text-align:center;">
                                        <img src="uploads/<?php echo htmlspecialchars($t_row['gambar']); ?>" alt="Testimonial Image" style="max-width:100%; max-height:200px; object-fit:cover; border-radius:8px;">
                                    </div>
                                <?php endif; ?>
                                <div class="testimonial-content" style="margin-bottom: 1.5rem; font-size: 0.95rem; color: #475569; font-style: italic; line-height: 1.6;">
                                    "<?php echo htmlspecialchars($t_row['isi']); ?>"
                                </div>
                                <div class="testimonial-author" style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="seller-circle-avatar" style="width: 48px; height: 48px; font-size: 1.25rem; margin-bottom: 0; flex-shrink: 0; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                                        <?php echo $initial; ?>
                                    </div>
                                    <div class="author-info">
                                        <h4 style="font-weight: 700; font-size: 0.95rem; color: var(--dark-text); margin: 0;"><?php echo htmlspecialchars($t_row['nama']); ?></h4>
                                        <p style="font-size: 0.8rem; color: #64748b; margin: 0;"><?php echo ucfirst(htmlspecialchars($t_row['role'])); ?></p>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                    ?>
                        <!-- Fallback jika query kosong -->
                        <div class="testimonial-card" style="min-width: 300px; background: white; border-radius: var(--border-radius); padding: 2rem;">
                            <div class="testimonial-content">
                                <p>"APEskansa membantu saya menjual hasil kerajinan tangan ke teman-teman sekolah. Sangat mudah digunakan!"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="seller-circle-avatar" style="width: 48px; height: 48px; font-size: 1.25rem;">A</div>
                                <div class="author-info">
                                    <h4>Anisa Rahma</h4>
                                    <p>Kelas XI RPL</p>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer" style="background-color: var(--dark-text); border-top: 1px solid #1e293b;">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="assets/img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
                    <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 0.5rem;">Marketplace Siswa SMKN 1 Bawang. Media kreasi & kewirausahaan siswa.</p>
                </div>
                <div class="footer-links">
                    <h3 style="color: white; font-weight: 600;">Navigasi</h3>
                    <ul>
                        <li><a href="index.php" style="color: #94a3b8;">Beranda</a></li>
                        <li><a href="produk.php" style="color: #94a3b8;">Produk</a></li>
                        <li><a href="penjual.php" style="color: #94a3b8;">Penjual</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3 style="color: white; font-weight: 600;">Kontak Sekolah</h3>
                    <ul>
                        <li style="color: #94a3b8;"><i class="fas fa-map-marker-alt" style="color: var(--primary-color);"></i> Jl. Raya Bawang, Banjarnegara</li>
                        <li style="color: #94a3b8;"><i class="fas fa-phone" style="color: var(--primary-color);"></i> (0286) 591256</li>
                        <li style="color: #94a3b8;"><i class="fas fa-envelope" style="color: var(--primary-color);"></i> info@smkn1bawang.sch.id</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3 style="color: white; font-weight: 600;">Media Sosial</h3>
                    <div class="social-icons">
                        <a href="https://tiktok.com" target="_blank" title="TikTok" style="background: rgba(255,255,255,0.05); color: #fff;"><i class="fab fa-tiktok"></i></a>
                        <a href="https://instagram.com" target="_blank" title="Instagram" style="background: rgba(255,255,255,0.05); color: #fff;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom" style="border-top: 1px solid #334155; color: #64748b;">
                <p>&copy; 2026 APEskansa - SMKN 1 Bawang. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>