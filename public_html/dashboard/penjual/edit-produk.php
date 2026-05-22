<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';

// Proteksi akses: hanya penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header('Location: ../../login.php');
    exit;
}

$id_penjual = $_SESSION['user_id'];
$produk_id  = (int) ($_GET['id'] ?? 0);

if ($produk_id <= 0) {
    header('Location: profil.php?tab=produk');
    exit;
}

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

// Ambil data produk yang akan diedit (pastikan milik penjual ini)
$stmt = mysqli_prepare($conn, 'SELECT * FROM produk WHERE produk_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo '<script>alert("Produk tidak ditemukan atau Anda tidak memiliki akses."); window.location="profil.php?tab=produk";</script>';
    exit;
}

$produk_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header glass-card" style="padding: var(--space-lg) var(--space-xl); border-radius: var(--radius-lg); margin-bottom: var(--space-xl);">
            <div>
                <h1 class="dashboard-title" style="margin: 0; font-weight: 700;">Edit Produk</h1>
                <p style="color: var(--text-light); margin: 0.25rem 0 0 0;">Perbarui rincian informasi dan foto produk wirausaha Anda.</p>
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

        <!-- Edit Product Form -->
        <div class="dashboard-card glass-card" style="border-radius: var(--radius-lg); padding: var(--space-xl);">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-md); margin-bottom: var(--space-lg);">
                <h2 style="font-size: var(--fs-xl); font-weight: 700; color: var(--text-dark); margin: 0;"><i class="fas fa-edit" style="color: var(--primary);"></i> Ubah Informasi Produk</h2>
            </div>

            <form action="../../process/update-produk.php" method="POST" enctype="multipart/form-data" class="product-form" style="max-width: 600px;">
                <input type="hidden" name="produk_id" value="<?= $produk_id ?>">

                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input type="text" id="nama_produk" name="nama_produk" class="form-control"
                           value="<?= htmlspecialchars($produk_data['nama_produk'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori Produk</label>
                    <select id="kategori" name="kategori" class="form-control" required>
                        <?php
                        $kategori_list = ['Makanan', 'Minuman', 'Kerajinan', 'Jasa', 'Lainnya'];
                        foreach ($kategori_list as $kat):
                            $selected = ($produk_data['kategori'] === $kat) ? 'selected' : '';
                        ?>
                            <option value="<?= $kat ?>" <?= $selected ?>><?= $kat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" class="form-control"
                           value="<?= (int) $produk_data['harga'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Produk</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" required><?= htmlspecialchars($produk_data['deskripsi'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Gambar Produk Saat Ini</label>
                    <img src="../../uploads/<?= htmlspecialchars($produk_data['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="Gambar Produk"
                         style="width: 150px; height: 150px; object-fit: cover; border-radius: var(--radius-md); border: 2px solid var(--border); display: block; margin-bottom: 0.75rem;">
                </div>

                <div class="form-group">
                    <label for="gambar">Ganti Gambar Produk (Opsional)</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                    <small style="color: var(--text-light); margin-top: 0.25rem; display: block;">Biarkan kosong jika tidak ingin mengganti gambar produk (Max 2MB).</small>
                </div>

                <div style="display: flex; gap: var(--space-md);">
                    <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                    <a href="profil.php?tab=produk" class="btn btn-outline btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>