<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/penjual-init.php';

$penjual_active = 'produk';
$produk_id = (int) ($_GET['id'] ?? 0);

if ($produk_id <= 0) {
    header('Location: ' . page_url('dashboard/penjual/produk.php'));
    exit;
}

$page_title = 'Edit Produk';
$page_subtitle = 'Perbarui rincian informasi dan foto produk wirausaha Anda.';

// Ambil data produk yang akan diedit (pastikan milik penjual ini)
$stmt = mysqli_prepare($conn, 'SELECT * FROM produk WHERE produk_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $produk_id, $id_penjual);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Produk tidak ditemukan atau Anda tidak memiliki akses.';
    header('Location: ' . page_url('dashboard/penjual/produk.php'));
    exit;
}

$produk_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../../includes/penjual-dashboard-top.php'; ?>

        <div class="dashboard-card glass-card section-block">
            <div class="card-header-block">
                <h2><i class="fas fa-edit"></i> Ubah Informasi Produk</h2>
            </div>

            <form action="<?= page_url('process/update-produk.php') ?>" method="POST" enctype="multipart/form-data" class="product-form form-narrow">
                <?= csrf_field() ?>
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
                        $kategori_tersimpan = kategori_normalize($produk_data['kategori'] ?? '');
                        foreach (kategori_options() as $slug => $label):
                            $selected = ($kategori_tersimpan === $slug) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>" <?= $selected ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
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
                    <img src="<?= upload_url($produk_data['gambar']) ?>"
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
                    <a href="<?= page_url('dashboard/penjual/produk.php') ?>" class="btn btn-outline btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>