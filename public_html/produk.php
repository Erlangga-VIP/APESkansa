<?php
session_start();
include '../config/config.php'; // Koneksi ke database

// Ambil parameter filter dari URL
$penjual_id = isset($_GET['penjual_id']) ? (int)$_GET['penjual_id'] : 0;
$kategori_filter = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Cari info penjual jika ada filter penjual_id
$nama_toko_filter = "";
if ($penjual_id > 0) {
    $toko_q = mysqli_query($conn, "SELECT nama FROM users WHERE user_id = $penjual_id AND role = 'penjual'");
    if ($toko_q && mysqli_num_rows($toko_q) > 0) {
        $toko_data = mysqli_fetch_assoc($toko_q);
        $nama_toko_filter = $toko_data['nama'];
    }
}

// Ambil informasi avatar user yang sedang login untuk header
$current_user_foto = null;
$current_user_initial = '';
if (isset($_SESSION['user_id'])) {
    $c_id = $_SESSION['user_id'];
    $c_query = mysqli_query($conn, "SELECT foto_profil, nama FROM users WHERE user_id = $c_id");
    if ($c_query && mysqli_num_rows($c_query) > 0) {
        $c_data = mysqli_fetch_assoc($c_query);
        $current_user_foto = $c_data['foto_profil'] ? 'uploads/' . htmlspecialchars($c_data['foto_profil']) : null;
        $current_user_initial = substr($c_data['nama'], 0, 1);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - APEskansa</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/assets/css/all.min.css">
</head>
<body style="background-color: var(--secondary-color); min-height: 100vh;">
    <!-- Header -->
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
                    ?>
                        <a href="<?php echo $dashboard_link; ?>" class="profile-icon" title="Profil Saya">
                            <?php if ($current_user_foto): ?>
                                <img src="<?php echo $current_user_foto; ?>" alt="Foto Profil" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div class="avatar-circle"><?php echo strtoupper(htmlspecialchars($current_user_initial)); ?></div>
                            <?php endif; ?>
                        </a>
                        <a href="process/logout.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Keluar</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Masuk</a>
                        <a href="register.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem; border-radius:8px;">Daftar</a>
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

    <!-- Filter & Search Section -->
    <div class="container" style="max-width: 1200px; padding-top: 3rem;">
        
        <!-- Dinamic Header -->
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <?php if ($penjual_id > 0 && !empty($nama_toko_filter)): ?>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text); margin:0;">Produk dari Toko: <?php echo htmlspecialchars($nama_toko_filter); ?></h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Menampilkan seluruh hasil produk wirausaha buatan siswa <?php echo htmlspecialchars($nama_toko_filter); ?>.</p>
                <?php elseif (!empty($kategori_filter)): ?>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text); margin:0;">Kategori: <?php echo htmlspecialchars($kategori_filter); ?></h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Menampilkan produk dalam lingkup kategori <?php echo htmlspecialchars($kategori_filter); ?>.</p>
                <?php elseif (!empty($search_query)): ?>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text); margin:0;">Hasil Pencarian: "<?php echo htmlspecialchars($search_query); ?>"</h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Ditemukan beberapa produk yang sesuai dengan kata kunci Anda.</p>
                <?php else: ?>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text); margin:0;">Katalog Produk Siswa</h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Temukan kreasi makanan, minuman, kerajinan, dan jasa terbaik buatan siswa SMKN 1 Bawang.</p>
                <?php endif; ?>
            </div>
            
            <!-- Back Button for active filters -->
            <?php if ($penjual_id > 0 || !empty($kategori_filter) || !empty($search_query)): ?>
                <a href="produk.php" class="btn btn-outline" style="border-radius: 8px; font-weight:600; padding: 0.5rem 1.25rem; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Lihat Semua Produk</a>
            <?php endif; ?>
        </div>

        <!-- Search & Filter Form Panel -->
        <div class="glass-card" style="padding: 1.5rem; border-radius: var(--border-radius); margin-bottom: 2.5rem;">
            <form action="produk.php" method="GET" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;">
                <?php if ($penjual_id > 0): ?>
                    <input type="hidden" name="penjual_id" value="<?php echo $penjual_id; ?>">
                <?php endif; ?>
                <?php if (!empty($kategori_filter)): ?>
                    <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_filter); ?>">
                <?php endif; ?>
                
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau deskripsi di sini..." value="<?php echo htmlspecialchars($search_query); ?>" style="padding-left: 45px; border-radius: 8px; width: 100%;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px;">Cari</button>
            </form>
            
            <!-- Category Filter Chips -->
            <div style="margin-top: 1.25rem; display: flex; gap: 0.5rem; flex-wrap: wrap; align-items:center;">
                <span style="font-size:0.85rem; font-weight:600; color:#64748b; margin-right:0.5rem;">Filter Kategori:</span>
                <?php
                $categories = ['Makanan', 'Minuman', 'Kerajinan', 'Jasa', 'Lainnya'];
                
                // Formulate chip link
                $base_all = "produk.php";
                $params_all = [];
                if ($penjual_id > 0) $params_all[] = "penjual_id=" . $penjual_id;
                if (!empty($search_query)) $params_all[] = "search=" . urlencode($search_query);
                if (count($params_all) > 0) $base_all .= "?" . implode("&", $params_all);
                
                $active_all_class = empty($kategori_filter) ? 'btn-primary' : 'btn-outline';
                echo '<a href="' . $base_all . '" class="btn ' . $active_all_class . '" style="padding:0.4rem 1rem; font-size:0.8rem; border-radius:50px; font-weight:600;">Semua</a>';
                
                foreach ($categories as $cat) {
                    $base_cat = "produk.php";
                    $params_cat = ["kategori=" . urlencode($cat)];
                    if ($penjual_id > 0) $params_cat[] = "penjual_id=" . $penjual_id;
                    if (!empty($search_query)) $params_cat[] = "search=" . urlencode($search_query);
                    $base_cat .= "?" . implode("&", $params_cat);
                    
                    $active_cat_class = ($kategori_filter === $cat) ? 'btn-primary' : 'btn-outline';
                    echo '<a href="' . $base_cat . '" class="btn ' . $active_cat_class . '" style="padding:0.4rem 1rem; font-size:0.8rem; border-radius:50px; font-weight:600;">' . $cat . '</a>';
                }
                ?>
            </div>
        </div>

    </div>

    <!-- Products Grid Section -->
    <section class="products" style="padding: 0 0 5rem 0;">
        <div class="container" style="max-width: 1200px;">
            <div class="products-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2rem;">
                <?php
                // Formulate conditional query
                $where_clauses = [];
                if ($penjual_id > 0) {
                    $where_clauses[] = "p.user_id = $penjual_id";
                }
                if (!empty($kategori_filter)) {
                    $where_clauses[] = "p.kategori = '" . mysqli_real_escape_string($conn, $kategori_filter) . "'";
                }
                if (!empty($search_query)) {
                    $where_clauses[] = "(p.nama_produk LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%' OR p.deskripsi LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%')";
                }
                
                $where_sql = "";
                if (count($where_clauses) > 0) {
                    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
                }
                
                $sql = "SELECT p.*, u.nama AS nama_penjual FROM produk p JOIN users u ON p.user_id = u.user_id $where_sql ORDER BY p.produk_id DESC";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="product-card glass-card hover-float" style="display:flex; flex-direction:column; border-radius: var(--border-radius); overflow:hidden;">
                            <div class="product-image" style="aspect-ratio:1/1; position:relative; overflow:hidden; border-bottom:1px solid var(--border-color);">
                                <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                <span class="badge-status badge-processing" style="position:absolute; top:12px; left:12px; font-size:0.7rem; font-weight:700; box-shadow:var(--box-shadow);">
                                    <?php echo htmlspecialchars($row['kategori'] ? $row['kategori'] : 'Lainnya'); ?>
                                </span>
                            </div>
                            <div class="product-info" style="padding:1.5rem; display:flex; flex-direction:column; flex:1;">
                                <h3 class="product-title" style="font-size:1.1rem; font-weight:700; color:var(--dark-text); margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                                <p class="product-seller" style="font-size:0.8rem; color:#64748b; margin-bottom:0.75rem;"><i class="fas fa-store" style="color:var(--primary-color);"></i> Oleh: <?php echo htmlspecialchars($row['nama_penjual']); ?></p>
                                <p class="product-price" style="font-size:1.25rem; font-weight:800; color:var(--primary-color); margin-bottom:1.5rem; margin-top:auto;">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                
                                <div class="product-buttons" style="margin-top:auto;">
                                    <?php
                                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'pembeli') {
                                        echo '<a href="detail-produk.php?id=' . $row['produk_id'] . '" class="btn btn-primary" style="display:block; border-radius:8px; font-weight:600; padding:0.6rem 1rem;"><i class="fas fa-shopping-cart"></i> Beli Sekarang</a>';
                                    } elseif (isset($_SESSION['role']) && ($_SESSION['role'] == 'penjual' || $_SESSION['role'] == 'admin')) {
                                        echo '<a href="detail-produk.php?id=' . $row['produk_id'] . '" class="btn btn-outline" style="display:block; border-radius:8px; font-weight:600; padding:0.6rem 1rem;"><i class="fas fa-eye"></i> Lihat Detail</a>';
                                    } else {
                                        echo '<a href="detail-produk.php?id=' . $row['produk_id'] . '" class="btn btn-primary" style="display:block; border-radius:8px; font-weight:600; padding:0.6rem 1rem;"><i class="fas fa-eye"></i> Detail & Beli</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "
                    <div style='grid-column: 1/-1; text-align:center; padding:5rem 2rem; color:#64748b;'>
                        <i class='fas fa-box-open' style='font-size:4rem; margin-bottom:1.25rem; color:var(--primary-color); opacity:0.5;'></i>
                        <h3 style='font-size:1.25rem; font-weight:700; color:var(--dark-text); margin-bottom:0.25rem;'>Produk Tidak Ditemukan</h3>
                        <p style='font-size:0.9rem;'>Maaf, kami tidak dapat menemukan produk yang sesuai dengan kriteria filter atau pencarian Anda.</p>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background-color: var(--dark-text); margin-top: 5rem;">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="assets/img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
                    <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 0.5rem;">Marketplace Siswa SMKN 1 Bawang. Media kreasi & kewirausahaan siswa.</p>
                </div>
                <div class="footer-links">
                    <h3 style="color: white;">Navigasi</h3>
                    <ul>
                        <li><a href="index.php" style="color: #94a3b8;">Beranda</a></li>
                        <li><a href="produk.php" style="color: #94a3b8;">Produk</a></li>
                        <li><a href="penjual.php" style="color: #94a3b8;">Penjual</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3 style="color: white;">Kontak Sekolah</h3>
                    <ul>
                        <li style="color: #94a3b8;"><i class="fas fa-map-marker-alt" style="color: var(--primary-color);"></i> Jl. Raya Bawang, Banjarnegara</li>
                        <li style="color: #94a3b8;"><i class="fas fa-phone" style="color: var(--primary-color);"></i> (0286) 591256</li>
                        <li style="color: #94a3b8;"><i class="fas fa-envelope" style="color: var(--primary-color);"></i> info@smkn1bawang.sch.id</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3 style="color: white;">Media Sosial</h3>
                    <div class="social-icons">
                        <a href="https://tiktok.com" target="_blank" title="TikTok" style="background: rgba(255,255,255,0.05); color:#fff;"><i class="fab fa-tiktok"></i></a>
                        <a href="https://instagram.com" target="_blank" title="Instagram" style="background: rgba(255,255,255,0.05); color:#fff;"><i class="fab fa-instagram"></i></a>
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