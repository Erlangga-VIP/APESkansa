<?php

declare(strict_types=1);

$id_penjual = (int) ($_SESSION['user_id'] ?? 0);
$active = $penjual_active ?? '';

$nav_items = [
    'index'   => ['label' => 'Ringkasan', 'icon' => 'fa-chart-pie', 'url' => page_url('dashboard/penjual/index.php')],
    'profil'  => ['label' => 'Profil Toko', 'icon' => 'fa-store', 'url' => page_url('dashboard/penjual/profil.php')],
    'produk'  => ['label' => 'Daftar Produk', 'icon' => 'fa-boxes', 'url' => page_url('dashboard/penjual/produk.php')],
    'tambah'  => ['label' => 'Tambah Produk', 'icon' => 'fa-plus-circle', 'url' => page_url('dashboard/penjual/tambah-produk.php')],
    'pesanan' => ['label' => 'Pesanan Masuk', 'icon' => 'fa-shopping-cart', 'url' => page_url('dashboard/penjual/pesanan.php')],
];
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <a href="<?= page_url('index.php') ?>">
            <img src="<?= page_url('assets/img/LOGOAPE.png') ?>" alt="APEskansa Logo">
        </a>
    </div>
    <div class="sidebar-menu">
        <?php foreach ($nav_items as $key => $item): ?>
            <a href="<?= $item['url'] ?>"
               class="sidebar-menu-item <?= $active === $key ? 'active' : '' ?>">
                <i class="fas <?= $item['icon'] ?>"></i>
                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
        <a href="<?= page_url('produk.php?penjual_id=' . $id_penjual) ?>"
           class="sidebar-menu-item <?= $active === 'toko' ? 'active' : '' ?>">
            <i class="fas fa-eye"></i>
            <span>Lihat Toko Saya</span>
        </a>
        <hr>
        <a href="<?= page_url('index.php') ?>" class="sidebar-menu-item">
            <i class="fas fa-home"></i>
            <span>Ke Beranda</span>
        </a>
        <a href="<?= page_url('process/logout.php') ?>" class="sidebar-menu-item sidebar-menu-item--danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</div>
