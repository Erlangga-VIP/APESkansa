<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';

$tab_legacy = $_GET['tab'] ?? '';
if ($tab_legacy === 'produk') {
    header('Location: ' . page_url('dashboard/penjual/produk.php'));
    exit;
}
if ($tab_legacy === 'pesanan') {
    header('Location: ' . page_url('dashboard/penjual/pesanan.php'));
    exit;
}

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/penjual-init.php';

$penjual_active = 'profil';
$page_title = 'Profil Toko';
$page_subtitle = 'Kelola identitas toko dan kontak WhatsApp untuk pembeli.';
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../../includes/penjual-dashboard-top.php'; ?>

        <div class="dashboard-card glass-card section-block">
            <div class="card-header dashboard-card-header">
                <h2><i class="fas fa-user-edit"></i> Profil & Toko Saya</h2>
            </div>

            <form action="<?= page_url('process/edit-profil.php') ?>" method="POST" enctype="multipart/form-data" class="form-narrow">
                <?= csrf_field() ?>
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
                    <small class="form-hint">Digunakan saat pembeli menekan tombol &quot;Hubungi Penjual&quot;.</small>
                </div>
                <div class="form-group">
                    <label for="foto_profil">Logo / Foto Profil Toko</label>
                    <input type="file" id="foto_profil" name="foto_profil" class="form-control" accept="image/*">
                    <small class="form-hint">Maks. 2 MB. Kosongkan jika tidak ingin mengganti logo.</small>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
