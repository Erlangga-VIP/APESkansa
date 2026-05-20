<?php
session_start();
include 'js/php/config.php'; // Koneksi ke database

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
    <title>Daftar Penjual - APEskansa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--secondary-color); min-height: 100vh;">
    <!-- Header -->
    <header class="header glass-card">
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
                        <li class="nav-item"><a href="produk.php" class="nav-link">Produk</a></li>
                        <li class="nav-item"><a href="penjual.php" class="nav-link active">Penjual</a></li>
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
                        <a href="js/php/logout.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Keluar</a>
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

    <!-- Sellers Section -->
    <section class="products" style="padding: 4rem 0;">
        <div class="container">
            <h1 class="section-title" style="margin-bottom: 3rem; font-weight:700; color:var(--dark-text);">Wirausaha Siswa Skansa</h1>
            
            <div class="products-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                <?php
                $sql = "SELECT user_id, nama, email, foto_profil FROM users WHERE role = 'penjual' ORDER BY nama ASC";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $toko_initial = substr($row['nama'], 0, 1);
                            $foto_toko = $row['foto_profil'] ? 'uploads/' . htmlspecialchars($row['foto_profil']) : null;
                ?>
                            <div class="product-card glass-card hover-float" style="padding: 2rem; border-radius: var(--border-radius); display:flex; flex-direction:column; align-items:center; text-align:center;">
                                <div style="margin-bottom: 1.25rem;">
                                    <?php if ($foto_toko): ?>
                                        <img src="<?php echo $foto_toko; ?>" alt="Logo Toko" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); box-shadow: var(--box-shadow);">
                                    <?php else: ?>
                                        <div class="avatar-circle" style="width: 90px; height: 90px; font-size: 2.25rem; font-weight:700; box-shadow: var(--box-shadow);"><?php echo strtoupper(htmlspecialchars($toko_initial)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($row['nama']); ?></h3>
                                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></p>
                                
                                <div style="margin-top: auto; width: 100%;">
                                    <a href="produk.php?penjual_id=<?php echo $row['user_id']; ?>" class="btn btn-primary" style="display: block; width: 100%; border-radius:8px; font-weight:600; padding:0.6rem 1rem;"><i class="fas fa-store"></i> Lihat Produk</a>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo "<div style='grid-column: 1/-1; text-align:center; padding:3rem; color:#64748b;'><i class='fas fa-store-slash' style='font-size:3rem; margin-bottom:1rem; color:var(--primary-color);'></i><p>Belum ada wirausaha siswa terdaftar.</p></div>";
                    }
                } else {
                    echo "<div style='grid-column: 1/-1; text-align:center; padding:3rem; color:#64748b;'><p>Terjadi kesalahan saat mengambil data penjual.</p></div>";
                }

                mysqli_close($conn);
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background-color: var(--dark-text); margin-top: 5rem;">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
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

    <script src="js/script.js"></script>
</body>
</html>
