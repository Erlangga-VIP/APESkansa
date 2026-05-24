<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/penjual-init.php';

$penjual_active = 'index';
$page_title = 'Ringkasan Toko';
$page_subtitle = 'Pantau performa toko dan akses menu utama dengan cepat.';
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../../includes/penjual-dashboard-top.php'; ?>

        <div class="stats-grid section-block">
            <div class="stat-card glass-card hover-float">
                <div class="flex-between">
                    <div>
                        <div class="stat-value"><?= $total_produk ?></div>
                        <div class="stat-label">Total Produk Aktif</div>
                    </div>
                    <i class="fas fa-boxes stat-icon" aria-hidden="true"></i>
                </div>
            </div>
            <div class="stat-card glass-card hover-float stat-card--warning">
                <div class="flex-between">
                    <div>
                        <div class="stat-value"><?= $pesanan_menunggu ?></div>
                        <div class="stat-label">Pesanan Menunggu</div>
                    </div>
                    <i class="fas fa-clock stat-icon" aria-hidden="true"></i>
                </div>
            </div>
            <div class="stat-card glass-card hover-float stat-card--warning">
                <div class="flex-between">
                    <div>
                        <div class="stat-value"><?= $pesanan_proses ?></div>
                        <div class="stat-label">Sedang Diproses</div>
                    </div>
                    <i class="fas fa-spinner stat-icon" aria-hidden="true"></i>
                </div>
            </div>
            <div class="stat-card glass-card hover-float stat-card--success">
                <div class="flex-between">
                    <div>
                        <div class="stat-value">Rp <?= number_format($omset, 0, ',', '.') ?></div>
                        <div class="stat-label">Omset Selesai</div>
                    </div>
                    <i class="fas fa-wallet stat-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-quick-links section-block">
            <a href="<?= page_url('dashboard/penjual/profil.php') ?>" class="quick-link-card glass-card hover-float">
                <i class="fas fa-store"></i>
                <h3>Profil Toko</h3>
                <p>Perbarui nama, email, WhatsApp, dan logo toko.</p>
            </a>
            <a href="<?= page_url('dashboard/penjual/produk.php') ?>" class="quick-link-card glass-card hover-float">
                <i class="fas fa-boxes"></i>
                <h3>Daftar Produk</h3>
                <p>Kelola, edit, atau hapus produk Anda.</p>
            </a>
            <a href="<?= page_url('dashboard/penjual/tambah-produk.php') ?>" class="quick-link-card glass-card hover-float">
                <i class="fas fa-plus-circle"></i>
                <h3>Tambah Produk</h3>
                <p>Unggah produk baru ke katalog toko.</p>
            </a>
            <a href="<?= page_url('dashboard/penjual/pesanan.php') ?>" class="quick-link-card glass-card hover-float">
                <i class="fas fa-shopping-cart"></i>
                <h3>Pesanan Masuk</h3>
                <p>Proses atau selesaikan pesanan dari pembeli.</p>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
