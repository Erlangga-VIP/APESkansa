<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect penjual or admin to their specific dashboards
if ($_SESSION['role'] == 'penjual') {
    header("Location: penjual-profil.php");
    exit;
} elseif ($_SESSION['role'] == 'admin') {
    header("Location: admin-dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil info lengkap pengguna dari database
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

$user_initial = substr($user_data['nama'], 0, 1);
$foto_profil = $user_data['foto_profil'] ? 'uploads/' . htmlspecialchars($user_data['foto_profil']) : null;
$no_hp = $user_data['no_hp'] ? htmlspecialchars($user_data['no_hp']) : '';

// Statistik Pembelian
$total_pesanan_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE pembeli_id = $user_id");
$total_pesanan = mysqli_fetch_assoc($total_pesanan_q)['total'];

$pesanan_proses_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE pembeli_id = $user_id AND status = 'diproses'");
$pesanan_proses = mysqli_fetch_assoc($pesanan_proses_q)['total'];

$pesanan_selesai_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE pembeli_id = $user_id AND status = 'selesai'");
$pesanan_selesai = mysqli_fetch_assoc($pesanan_selesai_q)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - APEskansa</title>
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
                        <li class="nav-item"><a href="produk.php" class="nav-link">Produk</a></li>
                        <li class="nav-item"><a href="penjual.php" class="nav-link">Penjual</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <a href="profil.php" class="profile-icon" title="Profil Saya">
                        <?php if ($foto_profil): ?>
                            <img src="<?php echo $foto_profil; ?>" alt="Foto Profil" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div class="avatar-circle"><?php echo strtoupper(htmlspecialchars($user_initial)); ?></div>
                        <?php endif; ?>
                    </a>
                    <a href="process/logout.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Keluar</a>
                </div>
                <button class="mobile-menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <div class="container" style="max-width: 1000px; padding: 3rem 15px;">
        
        <!-- Welcome Section -->
        <div class="glass-card" style="padding: 2rem; border-radius: var(--border-radius); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                <?php if ($foto_profil): ?>
                    <img src="<?php echo $foto_profil; ?>" alt="Foto Profil" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color);">
                <?php else: ?>
                    <div class="avatar-circle" style="width: 90px; height: 90px; font-size: 2.5rem;"><?php echo strtoupper(htmlspecialchars($user_initial)); ?></div>
                <?php endif; ?>
                <div>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($user_data['nama']); ?></h1>
                    <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 0.5rem;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <span class="status-badge status-active"><i class="fas fa-user-check"></i> <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?></span>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div style="display: flex; gap: 1.5rem;" class="stats-container">
                <div style="text-align: center; background: rgba(79, 70, 229, 0.05); padding: 0.75rem 1.25rem; border-radius: 10px; border: 1px solid rgba(79,70,229,0.1);">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color); margin:0;"><?php echo $total_pesanan; ?></h3>
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 500; margin:0;">Total Belanja</p>
                </div>
                <div style="text-align: center; background: rgba(59, 130, 246, 0.05); padding: 0.75rem 1.25rem; border-radius: 10px; border: 1px solid rgba(59,130,246,0.1);">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #2563eb; margin:0;"><?php echo $pesanan_proses; ?></h3>
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 500; margin:0;">Diproses</p>
                </div>
                <div style="text-align: center; background: rgba(16, 185, 129, 0.05); padding: 0.75rem 1.25rem; border-radius: 10px; border: 1px solid rgba(16,185,129,0.1);">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #059669; margin:0;"><?php echo $pesanan_selesai; ?></h3>
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 500; margin:0;">Selesai</p>
                </div>
            </div>
        </div>

        <!-- Dashboard Layout: Tabs + Content -->
        <div class="glass-card" style="padding: 2.25rem; border-radius: var(--border-radius);">
            
            <!-- Tab Buttons -->
            <div class="profile-tabs">
                <button class="profile-tab-btn active" data-tab="profil"><i class="fas fa-id-card"></i> Profil Saya</button>
                <button class="profile-tab-btn" data-tab="pesanan"><i class="fas fa-shopping-bag"></i> Pesanan Saya</button>
                <button class="profile-tab-btn" data-tab="testimoni"><i class="fas fa-star"></i> Tulis Testimoni</button>
            </div>

            <!-- TAB CONTENT 1: PROFIL -->
            <div class="profile-tab-content active" id="tab-profil">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--dark-text);">Informasi Akun</h3>
                
                <form action="process/edit-profil.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="nama" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($user_data['nama']); ?>" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="email" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="no_hp" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">No. WhatsApp (Aktif)</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" value="<?php echo $no_hp; ?>" style="border-radius: 8px;">
                        <small style="color: #64748b;">Gunakan format nomor HP biasa (dimulai dengan 08 / 62).</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="foto_profil" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Foto Profil</label>
                        <input type="file" id="foto_profil" name="foto_profil" class="form-control" accept="image/*" style="border-radius: 8px;">
                        <small style="color: #64748b;">Pilih file gambar jika ingin mengganti foto profil (Max 2MB).</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600; border-radius: 8px;">Simpan Perubahan</button>
                </form>
            </div>

            <!-- TAB CONTENT 2: PESANAN SAYA -->
            <div class="profile-tab-content" id="tab-pesanan">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--dark-text);">Riwayat Belanja Saya</h3>
                
                <div class="table-responsive">
                    <table class="table" style="border-radius: 8px; overflow:hidden;">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Penjual</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orders_sql = "
                                SELECT p.*, pr.nama_produk, pr.gambar, u.nama AS nama_penjual 
                                FROM pesanan p 
                                JOIN produk pr ON p.produk_id = pr.produk_id 
                                JOIN users u ON p.penjual_id = u.user_id 
                                WHERE p.pembeli_id = ? 
                                ORDER BY p.pesanan_id DESC
                            ";
                            if ($stmt = mysqli_prepare($conn, $orders_sql)) {
                                mysqli_stmt_bind_param($stmt, "i", $user_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $status_class = 'badge-waiting';
                                        if ($row['status'] == 'diproses') $status_class = 'badge-processing';
                                        if ($row['status'] == 'selesai') $status_class = 'badge-completed';
                                        if ($row['status'] == 'dibatalkan') $status_class = 'badge-cancelled';
                            ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" width="50" height="50" style="object-fit:cover; border-radius:8px;" alt="Gambar">
                                                    <span style="font-weight: 600;"><?php echo htmlspecialchars($row['nama_produk']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['nama_penjual']); ?></td>
                                            <td style="text-align: center;"><?php echo $row['jumlah']; ?></td>
                                            <td style="font-weight: 700; color: var(--primary-color);">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge-status <?php echo $status_class; ?>" style="font-size:0.75rem; font-weight:700;">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.85rem; color:#64748b; max-width: 150px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="<?php echo htmlspecialchars($row['catatan']); ?>">
                                                <?php echo htmlspecialchars($row['catatan'] ? $row['catatan'] : '-'); ?>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center; padding: 2rem; color: #64748b;'>Anda belum memesan produk apa pun.</td></tr>";
                                }
                                mysqli_stmt_close($stmt);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB CONTENT 3: TULIS TESTIMONI -->
            <div class="profile-tab-content" id="tab-testimoni">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--dark-text);">Beri Testimoni & Penilaian</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">Bagikan pengalaman belanja Anda di APEskansa. Ulasan Anda akan membantu kami meningkatkan layanan dan memotivasi wirausaha siswa!</p>
                
                <form action="process/beri-testimoni.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                    
                    <!-- Star Rating Selector -->
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Nilai Kualitas Pelayanan / Aplikasi</label>
                        <div style="display: flex; gap: 0.5rem; font-size: 1.75rem; color: var(--warning-color);" id="star-selector">
                            <i class="fas fa-star" data-value="1" style="cursor:pointer;"></i>
                            <i class="fas fa-star" data-value="2" style="cursor:pointer;"></i>
                            <i class="fas fa-star" data-value="3" style="cursor:pointer;"></i>
                            <i class="fas fa-star" data-value="4" style="cursor:pointer;"></i>
                            <i class="fas fa-star" data-value="5" style="cursor:pointer;"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating-value" value="5">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="isi" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Isi Ulasan Testimoni</label>
                        <textarea id="isi" name="isi" class="form-control" rows="4" placeholder="Tulis pendapat atau kritik saran Anda di sini..." required style="border-radius: 8px; resize:none;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="gambar" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Foto Ulasan / Pendukung (Opsional)</label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" style="border-radius: 8px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600; border-radius: 8px;"><i class="fas fa-paper-plane"></i> Kirim Testimoni</button>
                </form>
            </div>

        </div>
    </div>

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
    <script>
        // JS Tab Switcher
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.profile-tab-btn');
            const contents = document.querySelectorAll('.profile-tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetTab = tab.getAttribute('data-tab');

                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    tab.classList.add('active');
                    document.getElementById('tab-' + targetTab).classList.add('active');
                });
            });

            // Set active tab based on URL param if exists
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                const targetTabBtn = document.querySelector(`.profile-tab-btn[data-tab="${tabParam}"]`);
                if (targetTabBtn) {
                    targetTabBtn.click();
                }
            }

            // Star Rating selector
            const stars = document.querySelectorAll('#star-selector i');
            const ratingInput = document.getElementById('rating-value');

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    ratingInput.value = value;
                    
                    stars.forEach((s, idx) => {
                        if (idx < value) {
                            s.className = 'fas fa-star';
                        } else {
                            s.className = 'far fa-star';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
