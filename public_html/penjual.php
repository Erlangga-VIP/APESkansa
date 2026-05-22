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

<main class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-2xl);">
    <h1 style="font-size: var(--fs-3xl); font-weight: 700; margin-bottom: var(--space-2xl); color: var(--text-dark);">
        Wirausaha Siswa Skansa
    </h1>

    <div class="products-grid">
        <?php if (mysqli_num_rows($penjual_list) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($penjual_list)): ?>
                <div class="product-card" style="text-align: center; padding: var(--space-xl);">
                    <?php if (!empty($row['foto_profil'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['foto_profil'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="Foto Toko"
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: 0 auto var(--space-md); border: 2px solid var(--primary);">
                    <?php else: ?>
                        <div class="avatar-circle" style="width: 80px; height: 80px; font-size: var(--fs-2xl); margin: 0 auto var(--space-md);">
                            <?= strtoupper(mb_substr($row['nama'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <h3 style="font-weight: 700; font-size: var(--fs-lg); margin-bottom: var(--space-xs);">
                        <?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p style="color: var(--text-light); font-size: var(--fs-sm); margin-bottom: var(--space-md);">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <a href="produk.php?penjual_id=<?= (int) $row['user_id'] ?>" class="btn btn-primary btn-block btn-sm">
                        <i class="fas fa-store"></i> Lihat Produk
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; color: var(--text-light); padding: var(--space-3xl);">
                <i class="fas fa-store-slash" style="font-size: 3rem; display: block; margin-bottom: var(--space-md); opacity: 0.3;"></i>
                Belum ada wirausaha siswa terdaftar.
            </p>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>