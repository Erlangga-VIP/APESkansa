<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';

// Proteksi akses: hanya penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header('Location: ../../login.php');
    exit;
}

$id_penjual = $_SESSION['user_id'];

// Ambil info penjual
$stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$user_initial = mb_substr($user_data['nama'] ?? '', 0, 1);
$foto_profil = !empty($user_data['foto_profil'])
    ? '../../uploads/' . htmlspecialchars($user_data['foto_profil'], ENT_QUOTES, 'UTF-8')
    : null;
$no_hp = htmlspecialchars($user_data['no_hp'] ?? '', ENT_QUOTES, 'UTF-8');

// Statistik Toko
$total_produk    = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk WHERE user_id = $id_penjual"))['total'];
$pesanan_proses  = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE penjual_id = $id_penjual AND status = 'diproses'"))['total'];
$omset           = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_harga), 0) AS total FROM pesanan WHERE penjual_id = $id_penjual AND status = 'selesai'"))['total'];
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header glass-card" style="padding: var(--space-lg) var(--space-xl); border-radius: var(--radius-lg); margin-bottom: var(--space-xl);">
            <div>
                <h1 class="dashboard-title" style="margin: 0; font-weight: 700;">Dashboard Toko Saya</h1>
                <p style="color: var(--text-light); margin: 0.25rem 0 0 0;">Kelola produk wirausaha dan transaksi sekolah Anda.</p>
            </div>
            <div class="flex-center">
                <span style="font-weight: 500; color: var(--text); font-size: var(--fs-sm);">
                    <?= htmlspecialchars($user_data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php if ($foto_profil): ?>
                    <img src="<?= $foto_profil ?>" alt="Logo Toko" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">
                <?php else: ?>
                    <div class="avatar-circle" style="width: 45px; height: 45px;">
                        <?= strtoupper(htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Store Statistics Cards -->
        <div class="stats-grid" style="margin-bottom: var(--space-2xl);">
            <div class="stat-card glass-card hover-float" style="border-left: 5px solid var(--primary); text-align: left; padding: var(--space-lg);">
                <div class="flex-between">
                    <div>
                        <div class="stat-value" style="font-size: var(--fs-3xl); font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem;"><?= $total_produk ?></div>
                        <div class="stat-label" style="font-weight: 600; font-size: var(--fs-sm); color: var(--text-light);">Total Produk Aktif</div>
                    </div>
                    <div style="font-size: 2.5rem; color: rgba(79, 70, 229, 0.15);"><i class="fas fa-boxes"></i></div>
                </div>
            </div>

            <div class="stat-card glass-card hover-float" style="border-left: 5px solid var(--warning); text-align: left; padding: var(--space-lg);">
                <div class="flex-between">
                    <div>
                        <div class="stat-value" style="font-size: var(--fs-3xl); font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem;"><?= $pesanan_proses ?></div>
                        <div class="stat-label" style="font-weight: 600; font-size: var(--fs-sm); color: var(--text-light);">Pesanan Diproses</div>
                    </div>
                    <div style="font-size: 2.5rem; color: rgba(245, 158, 11, 0.15);"><i class="fas fa-spinner"></i></div>
                </div>
            </div>

            <div class="stat-card glass-card hover-float" style="border-left: 5px solid var(--success); text-align: left; padding: var(--space-lg);">
                <div class="flex-between">
                    <div>
                        <div class="stat-value" style="font-size: var(--fs-3xl); font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem;">Rp <?= number_format($omset, 0, ',', '.') ?></div>
                        <div class="stat-label" style="font-weight: 600; font-size: var(--fs-sm); color: var(--text-light);">Omset Penjualan</div>
                    </div>
                    <div style="font-size: 2.5rem; color: rgba(16, 185, 129, 0.15);"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT 1: PROFIL TOKO -->
        <div class="profile-tab-content active" id="tab-profil">
            <div class="dashboard-card glass-card" style="border-radius: var(--radius-lg); padding: var(--space-xl);">
                <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-md); margin-bottom: var(--space-lg);">
                    <h2 style="font-size: var(--fs-xl); font-weight: 700; color: var(--text-dark); margin: 0;"><i class="fas fa-user-edit" style="color: var(--primary);"></i> Profil & Toko Saya</h2>
                </div>

                <form action="../../process/edit-profil.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                    <div class="form-group">
                        <label for="nama">Nama Toko / Penjual</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                               value="<?= htmlspecialchars($user_data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Akun</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($user_data['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. WhatsApp (Untuk Chat Pembeli)</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control"
                               placeholder="Contoh: 081234567890" value="<?= $no_hp ?>" required>
                        <small style="color: var(--text-light);">Sangat penting! Digunakan untuk menghubungkan pembeli yang mengklik tombol "Hubungi Penjual".</small>
                    </div>
                    <div class="form-group">
                        <label for="foto_profil">Logo / Foto Profil Toko</label>
                        <input type="file" id="foto_profil" name="foto_profil" class="form-control" accept="image/*">
                        <small style="color: var(--text-light);">Pilih file gambar jika ingin mengganti logo toko (Max 2MB).</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <!-- TAB CONTENT 2: DAFTAR PRODUK SAYA -->
        <div class="profile-tab-content" id="tab-produk">
            <div class="dashboard-card glass-card" style="border-radius: var(--radius-lg); padding: var(--space-xl);">
                <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-md); margin-bottom: var(--space-lg); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-md);">
                    <h2 style="font-size: var(--fs-xl); font-weight: 700; color: var(--text-dark); margin: 0;"><i class="fas fa-cubes" style="color: var(--primary);"></i> Daftar Produk Saya</h2>
                    <a href="tambah-produk.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Produk</a>
                </div>

                <div class="table-responsive">
                    <table class="table">
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
                            $stmt = mysqli_prepare($conn, 'SELECT * FROM produk WHERE user_id = ? ORDER BY produk_id DESC');
                            mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
                            mysqli_stmt_execute($stmt);
                            $produk_list = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($produk_list) > 0):
                                while ($row = mysqli_fetch_assoc($produk_list)):
                            ?>
                                <tr>
                                    <td>
                                        <img src="../../uploads/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                                             width="60" height="60" style="object-fit:cover; border-radius: var(--radius-sm);" alt="Produk">
                                    </td>
                                    <td style="font-weight: 600;"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="badge-status badge-processing" style="font-size: var(--fs-xs);">
                                            <?= htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 700; color: var(--primary);">Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></td>
                                    <td style="font-size: var(--fs-xs); color: var(--text-light); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                        title="<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="display: flex; gap: var(--space-xs);">
                                            <a href="edit-produk.php?id=<?= (int) $row['produk_id'] ?>" class="btn btn-sm btn-edit" style="background: var(--warning); color: var(--text-dark); border: none; padding: 0.25rem 0.5rem; font-size: var(--fs-xs); font-weight: 600; border-radius: var(--radius-sm);" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" onclick="confirmDelete(<?= (int) $row['produk_id'] ?>)" class="btn btn-sm btn-delete" style="background: var(--danger); color: var(--white); border: none; padding: 0.25rem 0.5rem; font-size: var(--fs-xs); font-weight: 600; border-radius: var(--radius-sm);" title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: var(--space-xl); color: var(--text-light);">
                                        Anda belum mengunggah produk. Mulailah berjualan sekarang!
                                    </td>
                                </tr>
                            <?php endif;
                            mysqli_stmt_close($stmt);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT 3: PESANAN MASUK -->
        <div class="profile-tab-content" id="tab-pesanan">
            <div class="dashboard-card glass-card" style="border-radius: var(--radius-lg); padding: var(--space-xl);">
                <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-md); margin-bottom: var(--space-lg);">
                    <h2 style="font-size: var(--fs-xl); font-weight: 700; color: var(--text-dark); margin: 0;"><i class="fas fa-receipt" style="color: var(--primary);"></i> Pesanan Pelanggan Masuk</h2>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produk</th>
                                <th>Pembeli</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = mysqli_prepare($conn, '
                                SELECT p.*, pr.nama_produk, pr.gambar, u.nama AS nama_pembeli
                                FROM pesanan p
                                JOIN produk pr ON p.produk_id = pr.produk_id
                                JOIN users u ON p.pembeli_id = u.user_id
                                WHERE p.penjual_id = ?
                                ORDER BY p.pesanan_id DESC
                            ');
                            mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
                            mysqli_stmt_execute($stmt);
                            $pesanan_list = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($pesanan_list) > 0):
                                while ($row = mysqli_fetch_assoc($pesanan_list)):
                                    $status_class = match ($row['status']) {
                                        'diproses'   => 'badge-processing',
                                        'selesai'    => 'badge-completed',
                                        'dibatalkan' => 'badge-cancelled',
                                        default      => 'badge-waiting'
                                    };
                            ?>
                                <tr>
                                    <td style="font-size: var(--fs-xs); font-weight: 700; color: var(--text-light);">#<?= (int) $row['pesanan_id'] ?></td>
                                    <td>
                                        <div class="flex-center">
                                            <img src="../../uploads/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                                                 width="45" height="45" style="object-fit:cover; border-radius: var(--radius-sm);" alt="Img">
                                            <span style="font-weight: 600; font-size: var(--fs-sm);"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_pembeli'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td style="text-align: center; font-weight: 600;"><?= (int) $row['jumlah'] ?></td>
                                    <td style="font-weight: 700; color: var(--primary);">Rp <?= number_format((int) $row['total_harga'], 0, ',', '.') ?></td>
                                    <td style="font-size: var(--fs-xs); color: var(--text-light); max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                        title="<?= htmlspecialchars($row['catatan'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($row['catatan'] ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td><span class="badge-status <?= $status_class ?>" style="font-size: var(--fs-xs);"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td>
                                        <?php if ($row['status'] === 'menunggu'): ?>
                                            <div style="display: flex; gap: 0.25rem;">
                                                <form method="POST" action="../../process/update-status-pesanan.php" style="display:inline;">
                                                    <input type="hidden" name="pesanan_id" value="<?= (int) $row['pesanan_id'] ?>">
                                                    <input type="hidden" name="status" value="diproses">
                                                    <button type="submit" class="btn btn-sm btn-outline" style="font-size: var(--fs-xs); font-weight: 600;">
                                                        <i class="fas fa-check"></i> Proses
                                                    </button>
                                                </form>
                                                <form method="POST" action="../../process/update-status-pesanan.php" style="display:inline;">
                                                    <input type="hidden" name="pesanan_id" value="<?= (int) $row['pesanan_id'] ?>">
                                                    <input type="hidden" name="status" value="dibatalkan">
                                                    <button type="submit" class="btn btn-sm btn-delete" style="background: var(--danger); color: var(--white); border: none; font-size: var(--fs-xs); font-weight: 600;">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        <?php elseif ($row['status'] === 'diproses'): ?>
                                            <form method="POST" action="../../process/update-status-pesanan.php" style="display:inline;">
                                                <input type="hidden" name="pesanan_id" value="<?= (int) $row['pesanan_id'] ?>">
                                                <input type="hidden" name="status" value="selesai">
                                                <button type="submit" class="btn btn-sm" style="background: var(--success); color: var(--white); padding: 0.35rem 0.75rem; font-size: var(--fs-xs); font-weight: 600; border-radius: var(--radius-sm);">
                                                    <i class="fas fa-check-double"></i> Tandai Selesai
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: var(--fs-xs); color: var(--text-light); font-style: italic;">No Action</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: var(--space-xl); color: var(--text-light);">
                                        Belum ada pesanan masuk untuk produk Anda.
                                    </td>
                                </tr>
                            <?php endif;
                            mysqli_stmt_close($stmt);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
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
            activeContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        const targetTabBtn = document.querySelector(`.sidebar-menu-item[data-tab="${tabParam}"]`);
        if (targetTabBtn) targetTabBtn.click();
    }
});

function confirmDelete(produkId) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini secara permanen? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = '../../process/hapus-produk.php?id=' + produkId;
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>