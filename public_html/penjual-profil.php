<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit;
}

$id_penjual = $_SESSION['user_id'];

// Ambil info lengkap penjual dari database
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_penjual");
$user_data = mysqli_fetch_assoc($user_query);

$user_initial = substr($user_data['nama'], 0, 1);
$foto_profil = $user_data['foto_profil'] ? 'uploads/' . htmlspecialchars($user_data['foto_profil']) : null;
$no_hp = $user_data['no_hp'] ? htmlspecialchars($user_data['no_hp']) : '';

// Statistik Toko
$total_produk_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk WHERE user_id = $id_penjual");
$total_produk = mysqli_fetch_assoc($total_produk_q)['total'];

$pesanan_proses_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE penjual_id = $id_penjual AND status = 'diproses'");
$pesanan_proses = mysqli_fetch_assoc($pesanan_proses_q)['total'];

$omset_q = mysqli_query($conn, "SELECT COALESCE(SUM(total_harga), 0) AS total FROM pesanan WHERE penjual_id = $id_penjual AND status = 'selesai'");
$omset = mysqli_fetch_assoc($omset_q)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual - APEskansa</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Perbaikan path Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--secondary-color);">
    <div class="dashboard">
        
        <!-- Sidebar -->
        <div class="sidebar glass-card" style="background-color: var(--dark-text); border-right: 1px solid rgba(255,255,255,0.05); z-index: 100;">
            <div class="sidebar-logo">
                <a href="index.php">
                    <img src="assets/img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;">
                </a>
            </div>
            <div class="sidebar-menu">
                <a href="#" class="sidebar-menu-item active" data-tab="profil">
                    <i class="fas fa-store"></i>
                    <span>Profil Toko</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-tab="produk">
                    <i class="fas fa-boxes"></i>
                    <span>Daftar Produk</span>
                </a>
                <a href="penjual-tambah-produk.php" class="sidebar-menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Produk</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-tab="pesanan">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pesanan Masuk</span>
                </a>
                <!-- LINK BARU: Lihat Toko Saya -->
                <a href="produk.php?penjual_id=<?php echo $id_penjual; ?>" class="sidebar-menu-item">
                    <i class="fas fa-eye"></i>
                    <span>Lihat Toko Saya</span>
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

        <!-- Main Content Area -->
        <div class="main-content">
            
            <!-- Dashboard Header -->
            <div class="dashboard-header glass-card" style="padding: 1.5rem 2rem; border-radius: var(--border-radius); margin-bottom: 2rem;">
                <div>
                    <h1 class="dashboard-title" style="margin: 0; font-weight: 700;">Dashboard Toko Saya</h1>
                    <p style="color: #64748b; margin: 0.25rem 0 0 0;">Kelola produk wirausaha dan transaksi sekolah Anda.</p>
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

            <!-- Store Statistics Cards -->
            <div class="stats-grid" style="margin-bottom: 2.5rem;">
                <div class="stat-card glass-card hover-float" style="border-left: 5px solid var(--primary-color); text-align: left; padding: 1.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="stat-value" style="font-size: 2.25rem; font-weight: 700; color: var(--dark-text); margin-bottom: 0.25rem;"><?php echo $total_produk; ?></div>
                            <div class="stat-label" style="font-weight: 600; font-size: 0.9rem; color: #64748b;">Total Produk Aktif</div>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(79, 70, 229, 0.15);"><i class="fas fa-boxes"></i></div>
                    </div>
                </div>
                
                <div class="stat-card glass-card hover-float" style="border-left: 5px solid var(--warning-color); text-align: left; padding: 1.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="stat-value" style="font-size: 2.25rem; font-weight: 700; color: var(--dark-text); margin-bottom: 0.25rem;"><?php echo $pesanan_proses; ?></div>
                            <div class="stat-label" style="font-weight: 600; font-size: 0.9rem; color: #64748b;">Pesanan Diproses</div>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(245, 158, 11, 0.15);"><i class="fas fa-spinner"></i></div>
                    </div>
                </div>

                <div class="stat-card glass-card hover-float" style="border-left: 5px solid var(--success-color); text-align: left; padding: 1.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="stat-value" style="font-size: 2.25rem; font-weight: 700; color: var(--dark-text); margin-bottom: 0.25rem;">Rp <?php echo number_format($omset, 0, ',', '.'); ?></div>
                            <div class="stat-label" style="font-weight: 600; font-size: 0.9rem; color: #64748b;">Omset Penjualan</div>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(16, 185, 129, 0.15);"><i class="fas fa-wallet"></i></div>
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT 1: PROFIL TOKO -->
            <div class="profile-tab-content active" id="tab-profil">
                <div class="dashboard-card glass-card" style="border-radius: var(--border-radius); padding: 2.25rem;">
                    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text); margin: 0;"><i class="fas fa-user-edit" style="color: var(--primary-color);"></i> Profil & Toko Saya</h2>
                    </div>
                    
                    <form action="process/edit-profil.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label for="nama" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Nama Toko / Penjual</label>
                            <input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($user_data['nama']); ?>" required style="border-radius: 8px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label for="email" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Email Akun</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" required style="border-radius: 8px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label for="no_hp" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">No. WhatsApp (Untuk Chat Pembeli)</label>
                            <input type="text" id="no_hp" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" value="<?php echo $no_hp; ?>" required style="border-radius: 8px;">
                            <small style="color: #64748b;">Sangat penting! Digunakan untuk menghubungkan pembeli yang mengklik tombol "Hubungi Penjual".</small>
                        </div>

                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label for="foto_profil" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display:block;">Logo / Foto Profil Toko</label>
                            <input type="file" id="foto_profil" name="foto_profil" class="form-control" accept="image/*" style="border-radius: 8px;">
                            <small style="color: #64748b;">Pilih file gambar jika ingin mengganti logo toko (Max 2MB).</small>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600; border-radius: 8px;">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <!-- TAB CONTENT 2: DAFTAR PRODUK SAYA -->
            <div class="profile-tab-content" id="tab-produk">
                <div class="dashboard-card glass-card" style="border-radius: var(--border-radius); padding: 2.25rem;">
                    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap: 1rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text); margin: 0;"><i class="fas fa-cubes" style="color: var(--primary-color);"></i> Daftar Produk Saya</h2>
                        <a href="penjual-tambah-produk.php" class="btn btn-primary" style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 8px;"><i class="fas fa-plus"></i> Tambah Produk</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" style="border-radius: 8px; overflow:hidden;">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM produk WHERE user_id = ? ORDER BY produk_id DESC";
                                if ($stmt = mysqli_prepare($conn, $sql)) {
                                    mysqli_stmt_bind_param($stmt, "i", $id_penjual);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                            <tr>
                                                <td>
                                                    <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" width="60" height="60" style="object-fit:cover; border-radius: 8px;" alt="Produk">
                                                </td>
                                                <td style="font-weight: 600;"><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                                <td>
                                                    <span class="badge-status badge-processing" style="font-size:0.75rem; font-weight:600;">
                                                        <?php echo ucfirst(htmlspecialchars($row['kategori'])); ?>
                                                    </span>
                                                </td>
                                                <td style="font-weight: 700; color: var(--primary-color);">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                                <td style="font-size:0.85rem; color:#64748b; max-width: 200px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="<?php echo htmlspecialchars($row['deskripsi']); ?>">
                                                    <?php echo htmlspecialchars($row['deskripsi']); ?>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="penjual-edit-produk.php?id=<?php echo $row['produk_id']; ?>" class="btn btn-sm btn-edit" style="border-radius:6px; padding: 0.4rem 0.75rem; font-weight:600;" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                                        <a href="#" onclick="confirmDelete(<?php echo $row['produk_id']; ?>)" class="btn btn-sm btn-delete" style="border-radius:6px; padding: 0.4rem 0.75rem; font-weight:600;" title="Hapus"><i class="fas fa-trash"></i> Hapus</a>
                                                    </div>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' style='text-align:center; padding: 2rem; color:#64748b;'>Anda belum mengunggah produk. Mulailah berjualan sekarang!</td></tr>";
                                    }
                                    mysqli_stmt_close($stmt);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT 3: PESANAN MASUK -->
            <div class="profile-tab-content" id="tab-pesanan">
                <div class="dashboard-card glass-card" style="border-radius: var(--border-radius); padding: 2.25rem;">
                    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text); margin: 0;"><i class="fas fa-receipt" style="color: var(--primary-color);"></i> Pesanan Pelanggan Masuk</h2>
                    </div>

                    <div class="table-responsive">
                        <table class="table" style="border-radius: 8px; overflow:hidden;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Produk</th>
                                    <th>Pembeli</th>
                                    <th>Jumlah</th>
                                    <th>Total Pendapatan</th>
                                    <th>Catatan</th>
                                    <th>Status</th>
                                    <th>Kelola Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders_sql = "
                                    SELECT p.*, pr.nama_produk, pr.gambar, u.nama AS nama_pembeli 
                                    FROM pesanan p 
                                    JOIN produk pr ON p.produk_id = pr.produk_id 
                                    JOIN users u ON p.pembeli_id = u.user_id 
                                    WHERE p.penjual_id = ? 
                                    ORDER BY p.pesanan_id DESC
                                ";
                                if ($stmt = mysqli_prepare($conn, $orders_sql)) {
                                    mysqli_stmt_bind_param($stmt, "i", $id_penjual);
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
                                                <td style="font-size: 0.85rem; font-weight:700; color: #64748b;">#<?php echo $row['pesanan_id']; ?></td>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" width="45" height="45" style="object-fit:cover; border-radius:6px;" alt="Img">
                                                        <span style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($row['nama_produk']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['nama_pembeli']); ?></td>
                                                <td style="text-align: center; font-weight:600;"><?php echo $row['jumlah']; ?></td>
                                                <td style="font-weight: 700; color: var(--primary-color);">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                                <td style="font-size:0.8rem; color:#64748b; max-width: 120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($row['catatan']); ?>">
                                                    <?php echo htmlspecialchars($row['catatan'] ? $row['catatan'] : '-'); ?>
                                                </td>
                                                <td>
                                                    <span class="badge-status <?php echo $status_class; ?>" style="font-size: 0.75rem; font-weight:700;">
                                                        <?php echo htmlspecialchars($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <!-- TOMBOL AKSI DIUBAH MENJADI FORM POST -->
                                                    <?php if ($row['status'] == 'menunggu'): ?>
                                                        <div style="display:flex; gap: 0.25rem;">
                                                            <form method="POST" action="process/update-status-pesanan.php" style="display:inline;">
                                                                <input type="hidden" name="pesanan_id" value="<?php echo $row['pesanan_id']; ?>">
                                                                <input type="hidden" name="status" value="diproses">
                                                                <button type="submit" class="btn btn-sm btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight:600;">
                                                                    <i class="fas fa-check"></i> Proses
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="process/update-status-pesanan.php" style="display:inline;">
                                                                <input type="hidden" name="pesanan_id" value="<?php echo $row['pesanan_id']; ?>">
                                                                <input type="hidden" name="status" value="dibatalkan">
                                                                <button type="submit" class="btn btn-sm btn-delete" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight:600; color:white;">
                                                                    <i class="fas fa-times"></i> Tolak
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php elseif ($row['status'] == 'diproses'): ?>
                                                        <form method="POST" action="process/update-status-pesanan.php" style="display:inline;">
                                                            <input type="hidden" name="pesanan_id" value="<?php echo $row['pesanan_id']; ?>">
                                                            <input type="hidden" name="status" value="selesai">
                                                            <button type="submit" class="btn btn-sm" style="background-color: var(--success-color); color:white; padding: 0.35rem 0.75rem; font-size:0.75rem; font-weight:600; border-radius:6px;">
                                                                <i class="fas fa-check-double"></i> Tandai Selesai
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span style="font-size: 0.8rem; color: #64748b; font-style:italic;">No Action</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' style='text-align:center; padding: 2rem; color:#64748b;'>Belum ada pesanan masuk untuk produk Anda.</td></tr>";
                                    }
                                    mysqli_stmt_close($stmt);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        // JS Tab Switcher + Scroll Otomatis
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.sidebar-menu-item[data-tab]');
            const contents = document.querySelectorAll('.profile-tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetTab = tab.getAttribute('data-tab');

                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    tab.classList.add('active');
                    const activeContent = document.getElementById('tab-' + targetTab);
                    activeContent.classList.add('active');

                    // Scroll otomatis ke konten yang dipilih
                    activeContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            // Set active tab berdasarkan URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                const targetTabBtn = document.querySelector(`.sidebar-menu-item[data-tab="${tabParam}"]`);
                if (targetTabBtn) {
                    targetTabBtn.click();
                }
            }
        });

        // Konfirmasi Hapus Produk (path diperbaiki)
        function confirmDelete(produkId) {
            if (confirm("Apakah Anda yakin ingin menghapus produk ini secara permanen? Tindakan ini tidak dapat dibatalkan.")) {
                window.location.href = "process/hapus-produk.php?id=" + produkId;
            }
        }
    </script>
</body>
</html>