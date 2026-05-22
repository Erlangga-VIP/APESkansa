<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/penjual-init.php';

$penjual_active = 'pesanan';
$page_title = 'Pesanan Masuk';
$page_subtitle = 'Kelola pesanan dari pembeli dan perbarui status transaksi.';
?>

<div class="dashboard">
    <?php require_once __DIR__ . '/../../../includes/sidebar-penjual.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../../includes/penjual-dashboard-top.php'; ?>

        <div class="dashboard-card glass-card section-block">
            <div class="card-header dashboard-card-header">
                <h2><i class="fas fa-receipt"></i> Pesanan Pelanggan</h2>
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
                                    default      => 'badge-waiting',
                                };
                        ?>
                            <tr>
                                <td class="table-cell-muted">#<?= (int) $row['pesanan_id'] ?></td>
                                <td>
                                    <div class="flex-center table-product-cell">
                                        <img src="<?= upload_url($row['gambar']) ?>"
                                             width="45" height="45" class="table-thumb" alt="">
                                        <span><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['nama_pembeli'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-center table-cell-bold"><?= (int) $row['jumlah'] ?></td>
                                <td class="table-cell-price">Rp <?= number_format((int) $row['total_harga'], 0, ',', '.') ?></td>
                                <td class="table-cell-ellipsis"
                                    title="<?= htmlspecialchars($row['catatan'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($row['catatan'] ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <span class="badge-status <?= $status_class ?>">
                                        <?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'menunggu'): ?>
                                        <div class="action-buttons">
                                            <form method="POST" action="<?= page_url('process/update-status-pesanan.php') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="pesanan_id" value="<?= (int) $row['pesanan_id'] ?>">
                                                <input type="hidden" name="status" value="diproses">
                                                <button type="submit" class="btn btn-sm btn-outline">
                                                    <i class="fas fa-check"></i> Proses
                                                </button>
                                            </form>
                                            <form method="POST" action="<?= page_url('process/update-status-pesanan.php') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="pesanan_id" value="<?= (int) $row['pesanan_id'] ?>">
                                                <input type="hidden" name="status" value="dibatalkan">
                                                <button type="submit" class="btn btn-sm btn-danger-soft">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php elseif ($row['status'] === 'diproses'): ?>
                                        <form method="POST" action="<?= page_url('process/update-status-pesanan.php') ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="pesanan_id" value="<?= (int) $row['pesanan_id'] ?>">
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="btn btn-sm btn-success-soft">
                                                <i class="fas fa-check-double"></i> Selesai
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="table-cell-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="8" class="table-empty">Belum ada pesanan masuk untuk produk Anda.</td>
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
