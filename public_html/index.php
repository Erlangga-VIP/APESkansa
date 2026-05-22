<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

// ============================================================
// QUERY DATA
// ============================================================
if (isset($_SESSION['user_id'])) {
    // Produk rekomendasi (4 terbaru)
    $stmt = mysqli_prepare($conn, '
        SELECT p.*, u.nama AS nama_penjual
        FROM produk p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.produk_id DESC
        LIMIT 4
    ');
    mysqli_stmt_execute($stmt);
    $produk_rekomendasi = mysqli_stmt_get_result($stmt);

    // Toko populer (4 penjual dengan produk terbanyak)
    $stmt_sellers = mysqli_prepare($conn, '
        SELECT user_id, nama, email,
               (SELECT COUNT(*) FROM produk WHERE user_id = users.user_id) AS total_produk
        FROM users
        WHERE role = \'penjual\'
        ORDER BY total_produk DESC
        LIMIT 4
    ');
    mysqli_stmt_execute($stmt_sellers);
    $toko_populer = mysqli_stmt_get_result($stmt_sellers);
}

// Testimoni (6 terbaru)
$stmt_testi = mysqli_prepare($conn, '
    SELECT t.*, u.nama, u.role
    FROM testimoni t
    JOIN users u ON t.user_id = u.user_id
    ORDER BY t.testimoni_id DESC
    LIMIT 6
');
mysqli_stmt_execute($stmt_testi);
$testimoni = mysqli_stmt_get_result($stmt_testi);
?>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- ========== DASHBOARD SETELAH LOGIN ========== -->
    <main class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-2xl);">

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h1>Halo, <?= htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') ?>! 👋</h1>
            <p>Selamat datang kembali di APEskansa. Temukan produk-produk kreasi terbaik dari teman-teman sekolahmu hari ini!</p>
        </div>

        <!-- Search Bar -->
        <form action="produk.php" method="GET" class="search-bar">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: var(--space-md); top: 50%; transform: translateY(-50%); color: var(--text-light);"></i>
                <input type="text" name="cari" class="form-control" placeholder="Cari jajanan, kerajinan, atau jasa di sini..." style="padding-left: 2.5rem;">
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Cari</button>
        </form>

        <!-- Kategori -->
        <section style="margin-bottom: var(--space-2xl);">
            <h2 style="font-size: var(--fs-2xl); font-weight: 600; margin-bottom: var(--space-lg); color: var(--text-dark);">
                <i class="fas fa-tags" style="color: var(--primary); margin-right: var(--space-xs);"></i> Jelajahi Kategori
            </h2>
            <div class="categories-scroll">
                <?php
                $kategori_list = [
                    'Semua'     => '',
                    'Makanan'   => 'makanan',
                    'Minuman'   => 'minuman',
                    'Kerajinan' => 'kerajinan',
                    'Jasa'      => 'jasa',
                    'Lainnya'   => 'lainnya'
                ];
                $icons = ['fa-border-all', 'fa-utensils', 'fa-coffee', 'fa-shapes', 'fa-laptop-code', 'fa-boxes'];
                $i = 0;
                $active_kategori = $_GET['kategori'] ?? '';
                foreach ($kategori_list as $nama => $slug):
                    $link = $slug ? "produk.php?kategori=" . $slug : "produk.php";
                    $is_active = ($i === 0 && $active_kategori === '') || ($slug === $active_kategori);
                    $btn_class = $is_active ? 'btn-primary' : 'btn-outline';
                ?>
                    <a href="<?= $link ?>" class="btn <?= $btn_class ?>">
                        <i class="fas <?= $icons[$i] ?>"></i> <?= $nama ?>
                    </a>
                <?php $i++; endforeach; ?>
            </div>
        </section>

        <!-- Rekomendasi & Toko Populer -->
        <div class="row">
            <!-- Rekomendasi Produk -->
            <div class="col" style="flex: 2;">
                <div class="section-heading">
                    <h2><i class="fas fa-fire" style="color: var(--danger); margin-right: var(--space-xs);"></i> Rekomendasi Produk</h2>
                    <a href="produk.php">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="products-grid">
                    <?php if (mysqli_num_rows($produk_rekomendasi) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($produk_rekomendasi)): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="uploads/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="badge badge-primary" style="position: absolute; top: var(--space-sm); right: var(--space-sm);">
                                        <?= htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="product-seller"><i class="fas fa-store"></i> <?= htmlspecialchars($row['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="product-price">Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></p>
                                    <a href="detail-produk.php?id=<?= (int) $row['produk_id'] ?>" class="btn btn-primary btn-sm btn-block">Beli Sekarang</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: var(--text-light); grid-column: 1/-1;">Belum ada produk rekomendasi.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Toko Populer -->
            <div class="col" style="flex: 1;">
                <div class="section-heading">
                    <h2><i class="fas fa-store" style="color: var(--primary); margin-right: var(--space-xs);"></i> Toko Populer</h2>
                    <a href="penjual.php">Lihat Semua</a>
                </div>
                <div class="seller-list">
                    <?php if (mysqli_num_rows($toko_populer) > 0): ?>
                        <?php while ($s_row = mysqli_fetch_assoc($toko_populer)): ?>
                            <div class="seller-item">
                                <div class="flex-center">
                                    <div class="avatar-circle" style="width: 40px; height: 40px; font-size: var(--fs-sm);">
                                        <?= strtoupper(mb_substr($s_row['nama'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 style="font-weight: 600; font-size: var(--fs-sm);"><?= htmlspecialchars($s_row['nama'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p style="font-size: var(--fs-xs); color: var(--text-light);"><?= (int) $s_row['total_produk'] ?> Produk</p>
                                    </div>
                                </div>
                                <a href="produk.php?penjual_id=<?= (int) $s_row['user_id'] ?>" class="btn btn-outline btn-sm">Kunjungi</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: var(--text-light);">Belum ada toko yang terdaftar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php else: ?>
    <!-- ========== LANDING PAGE UNTUK GUEST ========== -->

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <span class="hero-badge">SMKN 1 BAWANG MARKETPLACE</span>
                <h1 class="hero-title">Marketplace Siswa <br><span>SMKN 1 Bawang</span></h1>
                <p class="hero-desc">Platform jual beli wirausaha siswa antar jurusan yang aman, praktis, dan terpercaya di lingkungan sekolah. Temukan jajanan lezat hingga kerajinan tangan menarik buatan temanmu!</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg">Mulai Jualan Sekarang</a>
                    <a href="produk.php" class="btn btn-secondary btn-lg">Lihat Produk</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="assets/img/hero-illustration.svg" alt="Ilustrasi Marketplace Sekolah">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-card">
                <div class="stats-grid">
                    <div style="text-align: center;">
                        <h3>500+</h3>
                        <p>Siswa Aktif</p>
                    </div>
                    <div style="text-align: center;">
                        <h3>200+</h3>
                        <p>Produk Kreatif</p>
                    </div>
                    <div style="text-align: center;">
                        <h3>100%</h3>
                        <p>Transaksi Aman COD</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="features-title">Fitur & Keunggulan Utama</h2>
            <div class="products-grid">
                <?php
                $features = [
                    ['icon' => 'fa-store', 'title' => 'Jual Produkmu dengan Mudah', 'desc' => 'Buka toko online pribadimu dalam hitungan detik! Unggah foto produk, tulis deskripsi menarik, tentukan harga, dan mulailah melayani pelanggan di sekolah.'],
                    ['icon' => 'fa-search', 'title' => 'Temukan Produk dari Temanmu', 'desc' => 'Jelajahi beragam produk berkualitas tinggi dari teman-teman sekolahmu. Cari makanan ringan, minuman menyegarkan, kerajinan tangan, atau jasa belajar/desain.'],
                    ['icon' => 'fa-handshake', 'title' => 'Transaksi Aman COD', 'desc' => 'Tidak perlu cemas soal metode pembayaran! Transaksi diselesaikan secara langsung (COD) di area sekolah di bawah pemantauan tertib OSIS/sekolah.']
                ];
                foreach ($features as $f):
                ?>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas <?= $f['icon'] ?>"></i></div>
                        <h3><?= $f['title'] ?></h3>
                        <p><?= $f['desc'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="testimonials-title">Testimoni Siswa SMKN 1 Bawang</h2>
            <div class="testimonials-slider">
                <?php if (mysqli_num_rows($testimoni) > 0): ?>
                    <?php while ($t_row = mysqli_fetch_assoc($testimoni)): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <?= str_repeat('<i class="fas fa-star"></i>', (int) $t_row['rating']) ?>
                            </div>
                            <?php if (!empty($t_row['gambar'])): ?>
                                <div style="margin-bottom: var(--space-sm);">
                                    <img src="uploads/<?= htmlspecialchars($t_row['gambar'], ENT_QUOTES, 'UTF-8') ?>" alt="Testimonial Image" style="max-width: 100%; border-radius: var(--radius-sm);">
                                </div>
                            <?php endif; ?>
                            <p class="testimonial-text">"<?= htmlspecialchars($t_row['isi'], ENT_QUOTES, 'UTF-8') ?>"</p>
                            <div class="testimonial-author">
                                <div class="avatar-circle">
                                    <?= strtoupper(mb_substr($t_row['nama'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h4 style="font-weight: 600; font-size: var(--fs-sm);"><?= htmlspecialchars($t_row['nama'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <p style="font-size: var(--fs-xs); color: var(--text-light);"><?= ucfirst($t_row['role']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"APEskansa membantu saya menjual hasil kerajinan tangan ke teman-teman sekolah. Sangat mudah digunakan!"</p>
                        <div class="testimonial-author">
                            <div class="avatar-circle">A</div>
                            <div>
                                <h4>Anisa Rahma</h4>
                                <p>Kelas XI RPL</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>