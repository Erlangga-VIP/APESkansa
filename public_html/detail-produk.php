<?php
session_start();
include '../config/config.php'; // Koneksi ke database

// Ambil ID produk dari URL dan pastikan itu adalah angka
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id == 0) {
    die("Error: Produk tidak valid.");
}

// Ambil detail produk dari database beserta info kontak penjual
$sql = "SELECT p.*, u.nama AS nama_penjual, u.no_hp AS no_hp_penjual, u.foto_profil AS foto_penjual, u.email AS email_penjual FROM produk p JOIN users u ON p.user_id = u.user_id WHERE p.produk_id = ?";
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

if ($product === null) {
    die("Produk tidak ditemukan.");
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
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - APEskansa</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                    <span class="bar"></span><span class="bar"></span><span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Product Detail Section -->
    <section class="product-detail" style="padding: 4rem 0;">
        <div class="container" style="max-width: 1000px;">
            <div class="product-detail-container glass-card" style="border-radius: var(--border-radius); display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; padding: 2.5rem; overflow: hidden;">
                <div class="product-detail-image" style="border-radius: 12px; overflow:hidden; border: 1px solid var(--border-color); aspect-ratio: 1/1;">
                    <img src="uploads/<?php echo htmlspecialchars($product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="product-detail-info" style="display:flex; flex-direction:column; justify-content:center;">
                    <span class="badge-status badge-processing" style="align-self:flex-start; margin-bottom: 0.75rem; font-size: 0.75rem; font-weight:600;">
                        <?php echo htmlspecialchars($product['kategori'] ? $product['kategori'] : 'Lainnya'); ?>
                    </span>
                    <h1 style="font-size: 2rem; font-weight: 700; color: var(--dark-text); margin-bottom: 0.5rem; line-height: 1.2;"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                    <p class="product-detail-price" style="font-size: 1.75rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.5rem;">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                    
                    <!-- Penjual Info Card -->
                    <div class="product-detail-seller" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.75rem; background: var(--accent-color); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                        <div class="seller-avatar">
                            <?php if ($product['foto_penjual']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($product['foto_penjual']); ?>" alt="Foto Penjual" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-color);">
                            <?php else: ?>
                                <div class="avatar-circle" style="width: 50px; height: 50px; font-size: 1.2rem;"><?php echo strtoupper(htmlspecialchars(substr($product['nama_penjual'], 0, 1))); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="seller-info">
                            <h4 style="font-size: 0.75rem; color:#64748b; font-weight: 500; margin: 0;">Penjual / Toko</h4>
                            <h3 style="font-size: 1.05rem; color: var(--dark-text); font-weight: 700; margin: 0;"><?php echo htmlspecialchars($product['nama_penjual']); ?></h3>
                        </div>
                    </div>
                    
                    <div class="product-detail-description" style="margin-bottom: 2.25rem;">
                        <h2 style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing:0.05em; color: var(--dark-text); margin-bottom: 0.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">Deskripsi Produk</h2>
                        <p style="color:#64748b; font-size: 0.95rem; line-height: 1.6; max-height:180px; overflow-y:auto; padding-right:5px;"><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
                    </div>
                    
                    <!-- PERBAIKAN: Tombol Hubungi Penjual untuk SEMUA, Pesan hanya untuk pembeli -->
                    <div class="product-detail-buttons" style="display:flex; gap: 1rem; flex-wrap:wrap;">
                        <?php
                        // Tombol Pesan (hanya untuk pembeli yang login)
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 'pembeli') {
                            echo '<button id="open-checkout-btn" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px; display:inline-flex; align-items:center; gap:0.5rem;"><i class="fas fa-shopping-bag"></i> Pesan Sekarang</button>';
                        }

                        // Tombol Hubungi Penjual (WhatsApp) untuk SEMUA pengunjung
                        $no_hp = trim($product['no_hp_penjual']);
                        $wa_link = "";
                        $has_wa = false;
                        if (!empty($no_hp)) {
                            $has_wa = true;
                            $clean_no = preg_replace('/[^0-9]/', '', $no_hp);
                            if (substr($clean_no, 0, 1) === '0') {
                                $clean_no = '62' . substr($clean_no, 1);
                            }
                            $pesan = "Halo " . $product['nama_penjual'] . ", saya tertarik dengan produk *" . $product['nama_produk'] . "* Anda yang dijual di APEskansa. Apakah masih tersedia?";
                            $wa_link = "https://wa.me/" . $clean_no . "?text=" . urlencode($pesan);
                        }

                        if ($has_wa) {
                            echo '<a href="' . $wa_link . '" target="_blank" class="btn btn-outline" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px; display:inline-flex; align-items:center; gap:0.5rem;"><i class="fab fa-whatsapp" style="font-size:1.2rem;"></i> Hubungi Penjual</a>';
                        } else {
                            // Jika tidak ada WA, tampilkan tombol yang membuka modal kontak (email)
                            echo '<button id="open-contact-btn" class="btn btn-outline" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px; display:inline-flex; align-items:center; gap:0.5rem;"><i class="fas fa-envelope"></i> Hubungi Penjual</button>';
                        }

                        // Guest: tombol login untuk memesan
                        if (!isset($_SESSION['user_id'])) {
                            echo '<a href="login.php" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px;"><i class="fas fa-sign-in-alt"></i> Login untuk Memesan</a>';
                        } elseif ($_SESSION['role'] != 'pembeli') {
                            echo '<span style="font-style:italic; font-size:0.9rem; color:#64748b;"><i class="fas fa-info-circle"></i> Anda masuk sebagai ' . $_SESSION['role'] . '. Fitur pemesanan hanya untuk pembeli.</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Checkout -->
    <div class="modal-overlay" id="checkout-modal">
        <div class="modal-box">
            <div class="modal-header">
                <h3><i class="fas fa-shopping-cart"></i> Konfirmasi Pemesanan</h3>
                <button class="modal-close-btn" id="close-checkout-btn">&times;</button>
            </div>
            <form action="process/buat-pesanan.php" method="POST">
                <input type="hidden" name="produk_id" value="<?php echo $product_id; ?>">
                <div class="modal-body">
                    <div style="display:flex; gap:1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom:1.25rem; margin-bottom:1.5rem; align-items:center;">
                        <img src="uploads/<?php echo htmlspecialchars($product['gambar']); ?>" alt="Img" style="width: 80px; height: 80px; object-fit:cover; border-radius:10px; border: 1px solid var(--border-color);">
                        <div>
                            <h4 style="font-size: 1.05rem; color: var(--dark-text); font-weight:600; margin:0 0 0.25rem 0;"><?php echo htmlspecialchars($product['nama_produk']); ?></h4>
                            <p style="color:var(--primary-color); font-weight:700; font-size:0.95rem; margin:0;">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                            <p style="font-size:0.8rem; color:#64748b; margin:0.25rem 0 0 0;"><i class="fas fa-store"></i> <?php echo htmlspecialchars($product['nama_penjual']); ?></p>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="jumlah" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Jumlah Pembelian</label>
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <button type="button" id="btn-minus" style="padding: 0.5rem 1rem; border-radius:8px; border:1px solid var(--border-color); background:var(--accent-color); font-weight:700; cursor:pointer; font-size:1.1rem; line-height:1;">-</button>
                            <input type="number" id="jumlah" name="jumlah" value="1" min="1" class="form-control" style="text-align:center; max-width:80px; font-weight:700; border-radius:8px; padding:0.5rem; border:1px solid var(--border-color);" readonly>
                            <button type="button" id="btn-plus" style="padding: 0.5rem 1rem; border-radius:8px; border:1px solid var(--border-color); background:var(--accent-color); font-weight:700; cursor:pointer; font-size:1.1rem; line-height:1;">+</button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="catatan" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Catatan Tambahan (Opsional)</label>
                        <textarea id="catatan" name="catatan" class="form-control" rows="3" placeholder="Contoh: COD di Kelas XI RPL 2 jam istirahat pertama, atau varian rasa..." style="border-radius: 8px; resize:none; padding:0.75rem; border: 1px solid var(--border-color); width:100%; font-family:inherit;"></textarea>
                    </div>

                    <div style="background: rgba(79, 70, 229, 0.05); padding:1rem; border-radius:10px; border:1px solid rgba(79,70,229,0.1); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-weight:600; color:#64748b; font-size:0.95rem;">Total Pembayaran:</span>
                        <span style="font-size:1.3rem; font-weight:800; color:var(--primary-color);" id="total-bayar-display">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancel-checkout-btn" style="border-radius:8px; font-weight:600; padding:0.6rem 1.5rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:8px; font-weight:600; padding:0.6rem 1.5rem;">Konfirmasi & Pesan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Kontak Penjual -->
    <div class="modal-overlay" id="contact-modal">
        <div class="modal-box" style="max-width:420px;">
            <div class="modal-header">
                <h3><i class="fas fa-address-book"></i> Kontak Penjual</h3>
                <button class="modal-close-btn" id="close-contact-btn">&times;</button>
            </div>
            <div class="modal-body" style="text-align:center; padding: 2rem 1.75rem;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background:rgba(245, 158, 11, 0.1); color: var(--warning-color); display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 1.25rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 style="font-size:1.1rem; font-weight:700; color:var(--dark-text); margin-bottom:0.5rem;">Nomor WhatsApp Belum Diatur</h4>
                <p style="color:#64748b; font-size:0.9rem; margin-bottom:1.5rem;">Penjual ini belum menyetel nomor WhatsApp di profil toko mereka. Anda dapat menghubungi penjual melalui email berikut:</p>
                
                <div style="background:var(--accent-color); padding:0.75rem 1.25rem; border-radius:8px; border:1px solid var(--border-color); font-weight:600; color:var(--primary-color); display:inline-flex; align-items:center; gap:0.5rem; word-break:break-all;">
                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($product['email_penjual'] ? $product['email_penjual'] : '-'); ?>
                </div>
            </div>
            <div class="modal-footer" style="justify-content:center; background:none; border-top:none; padding-bottom: 1.5rem;">
                <button type="button" class="btn btn-primary" id="ok-contact-btn" style="border-radius:8px; font-weight:600; padding:0.5rem 2rem;">Baik, Saya Mengerti</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const checkoutModal = document.getElementById('checkout-modal');
            const contactModal = document.getElementById('contact-modal');
            
            const openCheckoutBtn = document.getElementById('open-checkout-btn');
            const closeCheckoutBtn = document.getElementById('close-checkout-btn');
            const cancelCheckoutBtn = document.getElementById('cancel-checkout-btn');
            
            const openContactBtn = document.getElementById('open-contact-btn');
            const closeContactBtn = document.getElementById('close-contact-btn');
            const okContactBtn = document.getElementById('ok-contact-btn');
            
            if(openCheckoutBtn) {
                openCheckoutBtn.addEventListener('click', () => {
                    checkoutModal.classList.add('active');
                });
            }
            
            const closeCheckout = () => {
                checkoutModal.classList.remove('active');
            };
            
            if(closeCheckoutBtn) closeCheckoutBtn.addEventListener('click', closeCheckout);
            if(cancelCheckoutBtn) cancelCheckoutBtn.addEventListener('click', closeCheckout);
            
            if(openContactBtn) {
                openContactBtn.addEventListener('click', () => {
                    contactModal.classList.add('active');
                });
            }
            
            const closeContact = () => {
                contactModal.classList.remove('active');
            };
            
            if(closeContactBtn) closeContactBtn.addEventListener('click', closeContact);
            if(okContactBtn) okContactBtn.addEventListener('click', closeContact);
            
            window.addEventListener('click', (e) => {
                if (e.target === checkoutModal) closeCheckout();
                if (e.target === contactModal) closeContact();
            });
            
            const inputJumlah = document.getElementById('jumlah');
            const btnMinus = document.getElementById('btn-minus');
            const btnPlus = document.getElementById('btn-plus');
            const totalDisplay = document.getElementById('total-bayar-display');
            const hargaSatuan = <?php echo (int)$product['harga']; ?>;
            
            if (btnMinus && btnPlus && inputJumlah) {
                btnMinus.addEventListener('click', () => {
                    let val = parseInt(inputJumlah.value);
                    if (val > 1) {
                        val--;
                        inputJumlah.value = val;
                        updateTotal(val);
                    }
                });
                
                btnPlus.addEventListener('click', () => {
                    let val = parseInt(inputJumlah.value);
                    val++;
                    inputJumlah.value = val;
                    updateTotal(val);
                });
            }
            
            function updateTotal(qty) {
                const total = qty * hargaSatuan;
                totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }
        });
    </script>
</body>
</html>