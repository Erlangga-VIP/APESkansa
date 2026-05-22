<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/penjual-init.php';

$penjual_active = 'tambah';
$page_title = 'Tambah Produk Baru';
$page_subtitle = 'Tambahkan produk wirausaha terbaik Anda untuk dipasarkan ke siswa.';
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../../includes/penjual-dashboard-top.php'; ?>

        <div class="dashboard-card glass-card section-block">
            <div class="card-header-block">
                <h2><i class="fas fa-plus-circle"></i> Detail Informasi Produk</h2>
            </div>

            <form action="<?= page_url('process/tambah-produk.php') ?>" method="POST" enctype="multipart/form-data" class="product-form form-narrow">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input type="text" id="nama_produk" name="nama_produk" class="form-control" placeholder="Contoh: Brownies Coklat Keju" required>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori Produk</label>
                    <select id="kategori" name="kategori" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori Produk...</option>
                        <?php foreach (kategori_options() as $slug => $label): ?>
                            <option value="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
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
                    <a href="<?= page_url('dashboard/penjual/produk.php') ?>" class="btn btn-outline btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>