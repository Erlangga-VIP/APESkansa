<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <main class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-2xl);">
        
        <div class="welcome-banner">
            <h1>Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>! 👋</h1>
            <p>Temukan produk-produk kreasi terbaik dari teman-teman sekolahmu hari ini!</p>
        </div>

        <form action="produk.php" method="GET" class="search-bar">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: var(--space-md); top: 50%; transform: translateY(-50%); color: var(--text-light);"></i>
                <input type="text" name="cari" class="form-control" placeholder="Cari produk..." style="padding-left: 2.5rem;">
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Cari</button>
        </form>

        <section style="margin-bottom: var(--space-2xl);">
            <h2 style="font-size: var(--fs-2xl); font-weight: 600; margin-bottom: var(--space-lg); color: var(--text-dark);">
                <i class="fas fa-tags" style="color: var(--primary); margin-right: var(--space-xs);"></i> Jelajahi Kategori
            </h2>
            <div class="categories-scroll">
                <?php
                $kategori_list = ['Semua' => '', 'Makanan' => 'makanan', 'Minuman' => 'minuman', 'Kerajinan' => 'kerajinan', 'Jasa' => 'jasa', 'Lainnya' => 'lainnya'];
                $icons = ['fa-border-all', 'fa-utensils', 'fa-coffee', 'fa-shapes', 'fa-laptop-code', 'fa-boxes'];
                $i = 0;
                foreach ($kategori_list as $nama => $slug):
                    $link = $slug ? 'produk.php?kategori=' . $slug : 'produk.php';
                    $active = ($i == 0) ? 'btn-primary' : 'btn-outline';
                ?>
                    <a href="<?php echo $link; ?>" class="btn <?php echo $active; ?>">
                        <i class="fas <?php echo $icons[$i]; ?>"></i> <?php echo $nama; ?>
                    </a>
                <?php $i++; endforeach; ?>
            </div>
        </section>

        <div class="row">
            <div class="col" style="flex: 2;">
                <div class="section-heading">
                    <h2><i class="fas fa-fire" style="color: var(--danger);"></i> Rekomendasi</h2>
                    <a href="produk.php">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="products-grid">
                    <?php
                    $sql = "SELECT p.*, u.nama AS nama_penjual FROM produk p JOIN users u ON p.user_id = u.user_id ORDER BY p.produk_id DESC LIMIT 4";
                    $result = mysqli_query($conn, $sql);
                    if ($result && mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                    <span class="badge badge-primary" style="position: absolute; top: var(--space-sm); right: var(--space-sm);">
                                        <?php echo htmlspecialchars($row['kategori']); ?>
                                    </span>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title"><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                                    <p class="product-seller"><i class="fas fa-store"></i> <?php echo htmlspecialchars($row['nama_penjual']); ?></p>
                                    <p class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                    <a href="detail-produk.php?id=<?php echo $row['produk_id']; ?>" class="btn btn-primary btn-sm btn-block">Beli</a>
                                </div>
                            </div>
                    <?php
                        endwhile;
                    else:
                        echo "<p style='color: var(--text-light);'>Belum ada produk.</p>";
                    endif;
                    ?>
                </div>
            </div>

            <div class="col" style="flex: 1;">
                <div class="section-heading">
                    <h2><i class="fas fa-store" style="color: var(--primary);"></i> Toko Populer</h2>
                    <a href="penjual.php">Semua</a>
                </div>
                <div class="seller-list">
                    <?php
                    $sql_sellers = "SELECT user_id, nama, (SELECT COUNT(*) FROM produk WHERE user_id = users.user_id) AS total_produk FROM users WHERE role = 'penjual' LIMIT 4";
                    $res_sellers = mysqli_query($conn, $sql_sellers);
                    if ($res_sellers && mysqli_num_rows($res_sellers) > 0):
                        while ($s_row = mysqli_fetch_assoc($res_sellers)):
                    ?>
                            <div class="seller-item">
                                <div class="flex-center">
                                    <div class="avatar-circle" style="width: 40px; height: 40px; font-size: var(--fs-sm);">
                                        <?php echo strtoupper(substr($s_row['nama'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h4><?php echo htmlspecialchars($s_row['nama']); ?></h4>
                                        <p><?php echo $s_row['total_produk']; ?> Produk</p>
                                    </div>
                                </div>
                                <a href="produk.php?penjual_id=<?php echo $s_row['user_id']; ?>" class="btn btn-outline btn-sm">Kunjungi</a>
                            </div>
                    <?php
                        endwhile;
                    else:
                        echo "<p style='color: var(--text-light);'>Belum ada toko.</p>";
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </main>

<?php else: ?>
    <!-- LANDING PAGE -->
    
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <span class="hero-badge">SMKN 1 BAWANG MARKETPLACE</span>
                <h1 class="hero-title">Marketplace Siswa <br><span>SMKN 1 Bawang</span></h1>
                <p class="hero-desc">Platform jual beli wirausaha siswa antar jurusan yang aman, praktis, dan terpercaya.</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg">Mulai Jualan</a>
                    <a href="produk.php" class="btn btn-secondary btn-lg">Lihat Produk</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="assets/img/hero-illustration.svg" alt="Ilustrasi Marketplace">
            </div>
        </div>
    </section>

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
                        <p>Transaksi Aman</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="features-title">Fitur & Keunggulan</h2>
            <div class="products-grid">
                <?php
                $features = [
                    ['icon' => 'fa-store', 'title' => 'Jual Produkmu dengan Mudah', 'desc' => 'Buka toko online pribadimu dalam hitungan detik!'],
                    ['icon' => 'fa-search', 'title' => 'Temukan Produk dari Temanmu', 'desc' => 'Jelajahi beragam produk berkualitas tinggi.'],
                    ['icon' => 'fa-handshake', 'title' => 'Transaksi Aman COD', 'desc' => 'Transaksi diselesaikan secara langsung di area sekolah.']
                ];
                foreach ($features as $f):
                ?>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas <?php echo $f['icon']; ?>"></i></div>
                        <h3><?php echo $f['title']; ?></h3>
                        <p><?php echo $f['desc']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <h2 class="testimonials-title">Testimoni Siswa</h2>
            <div class="testimonials-slider">
                <?php
                $sql_testi = "SELECT t.*, u.nama, u.role FROM testimoni t JOIN users u ON t.user_id = u.user_id ORDER BY t.testimoni_id DESC LIMIT 6";
                $res_testi = mysqli_query($conn, $sql_testi);
                if ($res_testi && mysqli_num_rows($res_testi) > 0):
                    while ($t_row = mysqli_fetch_assoc($res_testi)):
                ?>
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <?php for ($i = 1; $i <= $t_row['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                            </div>
                            <p class="testimonial-text">"<?php echo htmlspecialchars($t_row['isi']); ?>"</p>
                            <div class="testimonial-author">
                                <div class="avatar-circle"><?php echo strtoupper(substr($t_row['nama'], 0, 1)); ?></div>
                                <div>
                                    <h4><?php echo htmlspecialchars($t_row['nama']); ?></h4>
                                    <p><?php echo ucfirst($t_row['role']); ?></p>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                else:
                    echo "<p style='color: var(--text-light);'>Belum ada testimoni.</p>";
                endif;
                ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>