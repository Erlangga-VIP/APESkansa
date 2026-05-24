<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

if (isset($_SESSION['user_id'])) {
    $stmt = mysqli_prepare($conn, '
        SELECT p.*, u.nama AS nama_penjual
        FROM produk p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.produk_id DESC
        LIMIT 4
    ');
    mysqli_stmt_execute($stmt);
    $produk_rekomendasi = mysqli_stmt_get_result($stmt);

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
    <main class="container page-section">
        <div class="welcome-banner">
            <h1>Halo, <?= htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p>Selamat datang kembali di APEskansa. Jelajahi kreasi terbaik dari teman-teman sekolahmu hari ini.</p>
        </div>

        <form action="<?= page_url('produk.php') ?>" method="GET" class="search-bar">
            <div class="search-input-wrap">
                <i class="fas fa-search search-input-icon" aria-hidden="true"></i>
                <input type="text" name="search" class="form-control search-input-field"
                       placeholder="Cari jajanan, kerajinan, atau jasa...">
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Cari Produk</button>
        </form>

        <section class="section-block">
            <div class="section-header">
                <h2><i class="fas fa-tags"></i> Jelajahi Kategori</h2>
            </div>
            <div class="categories-scroll">
                <?php
                $kategori_list = [
                    'Semua'     => '',
                    'Makanan'   => 'makanan',
                    'Minuman'   => 'minuman',
                    'Kerajinan' => 'kerajinan',
                    'Jasa'      => 'jasa',
                    'Lainnya'   => 'lainnya',
                ];
                $icons = ['fa-border-all', 'fa-utensils', 'fa-coffee', 'fa-shapes', 'fa-laptop-code', 'fa-boxes'];
                $i = 0;
                $active_kategori = $_GET['kategori'] ?? '';
                foreach ($kategori_list as $nama => $slug):
                    $link = $slug ? page_url('produk.php?kategori=' . $slug) : page_url('produk.php');
                    $is_active = ($i === 0 && $active_kategori === '') || ($slug === $active_kategori);
                    $btn_class = $is_active ? 'btn-primary' : 'btn-outline';
                ?>
                    <a href="<?= $link ?>" class="btn btn-pill-chip <?= $btn_class ?>">
                        <i class="fas <?= $icons[$i] ?>"></i> <?= $nama ?>
                    </a>
                <?php $i++; endforeach; ?>
            </div>
        </section>

        <div class="home-layout">
            <section class="section-block">
                <div class="section-header">
                    <h2><i class="fas fa-fire" style="color: var(--accent);"></i> Rekomendasi Produk</h2>
                    <a href="<?= page_url('produk.php') ?>">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="products-grid">
                    <?php if (mysqli_num_rows($produk_rekomendasi) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($produk_rekomendasi)): ?>
                            <article class="product-card">
                                <div class="product-image">
                                    <img src="<?= upload_url($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="badge badge-primary"><?= htmlspecialchars(kategori_label($row['kategori'] ?: 'lainnya'), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="product-seller"><i class="fas fa-store"></i> <?= htmlspecialchars($row['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="product-price">Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></p>
                                    <a href="<?= page_url('detail-produk.php?id=' . (int) $row['produk_id']) ?>" class="btn btn-primary btn-sm btn-block">Beli Sekarang</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-state" style="grid-column: 1 / -1;">Belum ada produk rekomendasi.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="section-block">
                <div class="section-header">
                    <h2><i class="fas fa-store"></i> Toko Populer</h2>
                    <a href="<?= page_url('penjual.php') ?>">Lihat Semua</a>
                </div>
                <div class="seller-list">
                    <?php if (mysqli_num_rows($toko_populer) > 0): ?>
                        <?php while ($s_row = mysqli_fetch_assoc($toko_populer)): ?>
                            <div class="seller-item">
                                <div class="flex-center">
                                    <div class="avatar-circle" style="width: 44px; height: 44px; font-size: var(--fs-sm);">
                                        <?= strtoupper(mb_substr($s_row['nama'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4><?= htmlspecialchars($s_row['nama'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p><?= (int) $s_row['total_produk'] ?> Produk</p>
                                    </div>
                                </div>
                                <a href="<?= page_url('produk.php?penjual_id=' . (int) $s_row['user_id']) ?>" class="btn btn-outline btn-sm">Kunjungi</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-state">Belum ada toko terdaftar.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

<?php else: ?>
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <span class="hero-badge">SMKN 1 Bawang</span>
                <h1 class="hero-title">Marketplace Siswa <br><span>SMKN 1 Bawang</span></h1>
                <p class="hero-desc">Platform jual beli wirausaha siswa yang aman dan praktis. Temukan jajanan, kerajinan, dan jasa kreatif buatan teman sekolahmu.</p>
                <div class="hero-buttons">
                    <a href="<?= page_url('register.php') ?>" class="btn btn-primary btn-lg">Mulai Jualan</a>
                    <a href="<?= page_url('produk.php') ?>" class="btn btn-secondary btn-lg">Jelajahi Produk</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?= page_url('assets/img/hero-illustration.svg') ?>" alt="Ilustrasi Marketplace Sekolah">
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="stats-card">
                <div class="stats-grid">
                    <div>
                        <h3>500+</h3>
                        <p>Siswa Aktif</p>
                    </div>
                    <div>
                        <h3>200+</h3>
                        <p>Produk Kreatif</p>
                    </div>
                    <div>
                        <h3>100%</h3>
                        <p>Transaksi COD Aman</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="features-title">Kenapa APEskansa?</h2>
            <div class="products-grid">
                <?php
                $features = [
                    ['icon' => 'fa-store', 'title' => 'Jual dengan Mudah', 'desc' => 'Buka toko online dalam hitungan menit. Unggah foto, atur harga, dan mulai melayani pembeli di sekolah.'],
                    ['icon' => 'fa-search', 'title' => 'Temukan Produk Teman', 'desc' => 'Cari makanan, minuman, kerajinan, atau jasa dari siswa SMKN 1 Bawang dalam satu tempat.'],
                    ['icon' => 'fa-handshake', 'title' => 'COD Terpercaya', 'desc' => 'Transaksi langsung di lingkungan sekolah dengan sistem pemesanan yang rapi dan transparan.'],
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

    <section class="testimonials">
        <div class="container">
            <h2 class="testimonials-title">Kata Mereka</h2>
            <div class="testimonials-slider">
                <?php if (mysqli_num_rows($testimoni) > 0): ?>
                    <?php while ($t_row = mysqli_fetch_assoc($testimoni)): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <?= str_repeat('<i class="fas fa-star"></i>', (int) $t_row['rating']) ?>
                            </div>
                            <?php if (!empty($t_row['gambar'])): ?>
                                <img src="<?= upload_url($t_row['gambar']) ?>" alt="" style="border-radius: var(--radius-md); margin-bottom: var(--space-sm); max-width: 100%;">
                            <?php endif; ?>
                            <p class="testimonial-text">"<?= htmlspecialchars($t_row['isi'], ENT_QUOTES, 'UTF-8') ?>"</p>
                            <div class="testimonial-author">
                                <div class="avatar-circle"><?= strtoupper(mb_substr($t_row['nama'], 0, 1)) ?></div>
                                <div>
                                    <h4><?= htmlspecialchars($t_row['nama'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <p><?= ucfirst($t_row['role']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"APEskansa membantu saya menjual kerajinan ke teman sekolah. Sangat mudah!"</p>
                        <div class="testimonial-author">
                            <div class="avatar-circle">A</div>
                            <div>
                                <h4>Anisa Rahma</h4>
                                <p>Pembeli</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
