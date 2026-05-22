<?php
session_start();
include '../../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit;
}

$id_penjual = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_penjual");
$user_data = mysqli_fetch_assoc($user_query);

$user_initial = substr($user_data['nama'], 0, 1);
$foto_profil = $user_data['foto_profil'] ? 'uploads/' . htmlspecialchars($user_data['foto_profil']) : null;

// Validasi dan ambil data produk
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard/penjual/profil.php?tab=produk");
    exit;
}

$produk_id = intval($_GET['id']);
$produk_query = mysqli_query($conn, "SELECT * FROM produk WHERE produk_id = $produk_id AND user_id = $id_penjual");

if (mysqli_num_rows($produk_query) == 0) {
    echo "<script>alert('Produk tidak ditemukan atau Anda tidak memiliki akses ke produk ini.'); window.location='dashboard/penjual/profil.php?tab=produk';</script>";
    exit;
}

$produk_data = mysqli_fetch_assoc($produk_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - APEskansa</title>
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
                <a href="dashboard/penjual/profil.php?tab=profil" class="sidebar-menu-item">
                    <i class="fas fa-store"></i>
                    <span>Profil Toko</span>
                </a>
                <a href="dashboard/penjual/profil.php?tab=produk" class="sidebar-menu-item active">
                    <i class="fas fa-boxes"></i>
                    <span>Daftar Produk</span>
                </a>
                <a href="dashboard/penjual/tambah-produk.php" class="sidebar-menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Produk</span>
                </a>
                <a href="dashboard/penjual/profil.php?tab=pesanan" class="sidebar-menu-item">
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
                    <h1 class="dashboard-title" style="margin: 0; font-weight: 700;">Edit Produk</h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Perbarui rincian informasi dan foto produk wirausaha Anda.</p>
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

            <!-- Edit Product Form Card -->
            <div class="dashboard-card glass-card" style="border-radius: var(--border-radius); padding: 2.25rem;">
                <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text); margin: 0;"><i class="fas fa-edit" style="color: var(--primary-color);"></i> Ubah Informasi Produk</h2>
                </div>
                
                <form action="process/update-produk.php" method="POST" enctype="multipart/form-data" class="product-form" style="max-width: 600px;">
                    <input type="hidden" name="produk_id" value="<?php echo $produk_id; ?>">
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="nama_produk" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" class="form-control" value="<?php echo htmlspecialchars($produk_data['nama_produk']); ?>" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="kategori" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Kategori Produk</label>
                        <select id="kategori" name="kategori" class="form-control" required style="border-radius: 8px; padding: 0.75rem;">
                            <option value="Makanan" <?php echo ($produk_data['kategori'] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                            <option value="Minuman" <?php echo ($produk_data['kategori'] == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                            <option value="Kerajinan" <?php echo ($produk_data['kategori'] == 'Kerajinan') ? 'selected' : ''; ?>>Kerajinan</option>
                            <option value="Jasa" <?php echo ($produk_data['kategori'] == 'Jasa') ? 'selected' : ''; ?>>Jasa</option>
                            <option value="Lainnya" <?php echo ($produk_data['kategori'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="harga" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Harga (Rp)</label>
                        <input type="number" id="harga" name="harga" class="form-control" value="<?php echo intval($produk_data['harga']); ?>" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="deskripsi" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Deskripsi Produk</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" required style="border-radius: 8px; resize:none;"><?php echo htmlspecialchars($produk_data['deskripsi']); ?></textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Gambar Produk Saat Ini</label>
                        <img src="uploads/<?php echo htmlspecialchars($produk_data['gambar']); ?>" alt="Gambar Produk" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid var(--border-color); display:block; margin-bottom: 0.75rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="gambar" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Ganti Gambar Produk (Opsional)</label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" style="border-radius: 8px;">
                        <small style="color: #64748b; margin-top: 0.25rem; display:block;">Biarkan kosong jika tidak ingin mengganti gambar produk (Max 2MB).</small>
                    </div>
                    
                    <div class="form-buttons" style="display:flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px;">Simpan Perubahan</button>
                        <a href="dashboard/penjual/profil.php?tab=produk" class="btn btn-outline" style="padding: 0.75rem 2rem; font-weight:600; border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
