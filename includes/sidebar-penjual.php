<?php

declare(strict_types=1);

$id_penjual = $_SESSION['user_id'] ?? 0;
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <a href="<?= BASE_URL ?>index.php">
            <img src="<?= BASE_URL ?>assets/img/LOGOAPE.png" alt="APEskansa Logo">
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
        <a href="<?= BASE_URL ?>dashboard/penjual/tambah-produk.php" class="sidebar-menu-item">
            <i class="fas fa-plus-circle"></i>
            <span>Tambah Produk</span>
        </a>
        <a href="#" class="sidebar-menu-item" data-tab="pesanan">
            <i class="fas fa-shopping-cart"></i>
            <span>Pesanan Masuk</span>
        </a>
        <a href="<?= BASE_URL ?>produk.php?penjual_id=<?= $id_penjual ?>" class="sidebar-menu-item">
            <i class="fas fa-eye"></i>
            <span>Lihat Toko Saya</span>
        </a>
        <hr style="border:0; border-top:1px solid rgba(255,255,255,0.08); margin:1.5rem 1rem;">
        <a href="<?= BASE_URL ?>index.php" class="sidebar-menu-item">
            <i class="fas fa-home"></i>
            <span>Ke Beranda</span>
        </a>
        <a href="<?= BASE_URL ?>process/logout.php" class="sidebar-menu-item" style="color: var(--danger);">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</div>