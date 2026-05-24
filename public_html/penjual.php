<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

// Ambil semua penjual
$stmt = mysqli_prepare($conn, "
    SELECT user_id, nama, email, foto_profil
    FROM users
    WHERE role = 'penjual'
    ORDER BY nama ASC
");
mysqli_stmt_execute($stmt);
$penjual_list = mysqli_stmt_get_result($stmt);
?>

<main class="container page-section">
    <h1 class="page-title">Wirausaha Siswa Skansa</h1>
    <p class="page-subtitle">Temukan toko dan produk dari siswa penjual di SMKN 1 Bawang.</p>

    <div class="products-grid">
        <?php if (mysqli_num_rows($penjual_list) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($penjual_list)): ?>
                <div class="product-card seller-card">
                    <?php if (!empty($row['foto_profil'])): ?>
                        <img src="<?= upload_url($row['foto_profil']) ?>" alt="Foto Toko" class="seller-card-avatar">
                    <?php else: ?>
                        <div class="avatar-circle">
                            <?= strtoupper(mb_substr($row['nama'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <h3 class="seller-card-name"><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="seller-card-email">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <a href="<?= page_url('produk.php?penjual_id=' . (int) $row['user_id']) ?>" class="btn btn-primary btn-block btn-sm">
                        <i class="fas fa-store"></i> Lihat Produk
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-state" style="grid-column: 1 / -1;">
                <i class="fas fa-store-slash" aria-hidden="true"></i>
                Belum ada wirausaha siswa terdaftar.
            </p>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>