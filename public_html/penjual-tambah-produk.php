<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit;
}

$id_penjual = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_penjual");
$user_data = mysqli_fetch_assoc($user_query);

$user_initial = substr($user_data['nama'], 0, 1);
$foto_profil = $user_data['foto_profil'] ? 'uploads/' . htmlspecialchars($user_data['foto_profil']) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - APEskansa</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/assets/css/all.min.css">
</head>
<body style="background-color: var(--secondary-color);">
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar glass-card" style="background-color: var(--dark-text); border-right: 1px solid rgba(255,255,255,0.05); z-index: 100;">
            <div class="sidebar-logo">
                <a href="index.php"><img src="assets/img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;"></a>
            </div>
            <div class="sidebar-menu">
                <a href="penjual-profil.php?tab=profil" class="sidebar-menu-item">
                    <i class="fas fa-store"></i>
                    <span>Profil Toko</span>
                </a>
                <a href="penjual-profil.php?tab=produk" class="sidebar-menu-item active">
                    <i class="fas fa-boxes"></i>
                    <span>Daftar Produk</span>
                </a>
                <a href="penjual-tambah-produk.php" class="sidebar-menu-item active">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Produk</span>
                </a>
                <a href="penjual-profil.php?tab=pesanan" class="sidebar-menu-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pesanan Masuk</span>
                </a>
                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 1.5rem 1rem;">
                <a href="index.php" class="sidebar-menu-item">
                    <i class="fas fa-home"></i>
                    <span>Ke Beranda</span>
                </a>
                <a href="process/logout.php" class="sidebar-menu-item" style="color: var(--danger-color);">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard Header -->
            <div class="dashboard-header glass-card" style="padding: 1.5rem 2rem; border-radius: var(--border-radius); margin-bottom: 2rem;">
                <div>
                    <h1 class="dashboard-title" style="margin: 0; font-weight: 700;">Tambah Produk Baru</h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Tambahkan produk wirausaha terbaik Anda untuk dipasarkan ke siswa.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-weight: 500; color: var(--text-color); font-size: 0.95rem;">
                        <?php echo htmlspecialchars($user_data['nama']); ?>
                    </span>
                    <?php if ($foto_profil): ?>
                        <img src="<?php echo $foto_profil; ?>" alt="Logo Toko" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-color);">
                    <?php else: ?>
                        <div class="avatar-circle" style="width: 45px; height: 45px;"><?php echo strtoupper(htmlspecialchars($user_initial)); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add Product Form Card -->
            <div class="dashboard-card glass-card" style="border-radius: var(--border-radius); padding: 2.25rem;">
                <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text); margin: 0;"><i class="fas fa-plus-circle" style="color: var(--primary-color);"></i> Detail Informasi Produk</h2>
                </div>
                <form action="process/tambah-produk.php" method="POST" enctype="multipart/form-data" class="product-form" style="max-width: 600px;">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="nama_produk" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" class="form-control" placeholder="Contoh: Brownies Coklat Keju" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="kategori" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Kategori Produk</label>
                        <select id="kategori" name="kategori" class="form-control" required style="border-radius: 8px; padding: 0.75rem;">
                            <option value="" disabled selected>Pilih Kategori Produk...</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Kerajinan">Kerajinan</option>
                            <option value="Jasa">Jasa</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="harga" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Harga (Rp)</label>
                        <input type="number" id="harga" name="harga" class="form-control" placeholder="Contoh: 15000" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="deskripsi" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Deskripsi Produk</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan produk Anda (misal: rasa, bahan, sistem pre-order)..." required style="border-radius: 8px; resize:none;"></textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="gambar" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Gambar Produk</label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" required style="border-radius: 8px;">
                        <small style="color: #64748b; margin-top: 0.25rem; display:block;">Unggah gambar produk Anda (Max 2MB, format JPG, PNG, WEBP).</small>
                    </div>
                    
                    <div class="form-buttons" style="display:flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px;">Simpan Produk</button>
                        <a href="penjual-profil.php?tab=produk" class="btn btn-outline" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>