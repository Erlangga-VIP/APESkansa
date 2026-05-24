<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/penjual-init.php';

$penjual_active = 'produk';
$page_title = 'Daftar Produk';
$page_subtitle = 'Kelola katalog produk wirausaha Anda.';
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../../includes/penjual-dashboard-top.php'; ?>

        <div class="dashboard-card glass-card section-block">
            <div class="card-header dashboard-card-header dashboard-card-header--actions">
                <h2><i class="fas fa-cubes"></i> Produk Saya</h2>
                <a href="<?= page_url('dashboard/penjual/tambah-produk.php') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Produk
                </a>
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
                                    <img src="<?= upload_url($row['gambar']) ?>"
                                         width="60" height="60" class="table-thumb" alt="Produk">
                                </td>
                                <td class="table-cell-bold"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge-status badge-processing">
                                        <?= htmlspecialchars(kategori_label($row['kategori']), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="table-cell-price">Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></td>
                                <td class="table-cell-ellipsis"
                                    title="<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= page_url('dashboard/penjual/edit-produk.php?id=' . (int) $row['produk_id']) ?>"
                                           class="btn btn-sm btn-warning-soft" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" action="<?= page_url('process/hapus-produk.php') ?>"
                                              onsubmit="return confirm('Hapus produk ini secara permanen?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="produk_id" value="<?= (int) $row['produk_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger-soft" title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="6" class="table-empty">Anda belum mengunggah produk. Mulailah berjualan sekarang!</td>
                            </tr>
                        <?php
                        endif;
                        mysqli_stmt_close($stmt);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
