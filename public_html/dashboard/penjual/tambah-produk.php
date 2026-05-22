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
$stmt = mysqli_prepare($conn, 'SELECT nama, foto_profil FROM users WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_penjual);
mysqli_stmt_execute($stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$user_initial = mb_substr($user_data['nama'] ?? '', 0, 1);
$foto_profil = !empty($user_data['foto_profil'])
    ? '../../uploads/' . htmlspecialchars($user_data['foto_profil'], ENT_QUOTES, 'UTF-8')
    : null;
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header glass-card" style="padding: var(--space-lg) var(--space-xl); border-radius: var(--radius-lg); margin-bottom: var(--space-xl);">
            <div>
                <h1 class="dashboard-title" style="margin: 0; font-weight: 700;">Tambah Produk Baru</h1>
                <p style="color: var(--text-light); margin: 0.25rem 0 0 0;">Tambahkan produk wirausaha terbaik Anda untuk dipasarkan ke siswa.</p>
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

        <!-- Add Product Form -->
        <div class="dashboard-card glass-card" style="border-radius: var(--radius-lg); padding: var(--space-xl);">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-md); margin-bottom: var(--space-lg);">
                <h2 style="font-size: var(--fs-xl); font-weight: 700; color: var(--text-dark); margin: 0;"><i class="fas fa-plus-circle" style="color: var(--primary);"></i> Detail Informasi Produk</h2>
            </div>

            <form action="../../process/tambah-produk.php" method="POST" enctype="multipart/form-data" class="product-form" style="max-width: 600px;">
                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input type="text" id="nama_produk" name="nama_produk" class="form-control" placeholder="Contoh: Brownies Coklat Keju" required>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori Produk</label>
                    <select id="kategori" name="kategori" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori Produk...</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Kerajinan">Kerajinan</option>
                        <option value="Jasa">Jasa</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" class="form-control" placeholder="Contoh: 15000" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Produk</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan produk Anda (misal: rasa, bahan, sistem pre-order)..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar Produk</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" required>
                    <small style="color: var(--text-light); margin-top: 0.25rem; display: block;">Unggah gambar produk Anda (Max 2MB, format JPG, PNG, WEBP).</small>
                </div>

                <div style="display: flex; gap: var(--space-md);">
                    <button type="submit" class="btn btn-primary btn-lg">Simpan Produk</button>
                    <a href="profil.php?tab=produk" class="btn btn-outline btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>