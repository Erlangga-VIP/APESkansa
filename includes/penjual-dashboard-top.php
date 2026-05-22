<?php

declare(strict_types=1);

/** Header dashboard penjual + chip user. Variabel: $page_title, $page_subtitle */
?>
<div class="dashboard-header glass-card">
    <div>
        <h1 class="dashboard-title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($page_subtitle)): ?>
            <p class="dashboard-subtitle"><?= htmlspecialchars($page_subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
    <div class="dashboard-user-chip">
        <span><?= htmlspecialchars($user_data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        <?php if ($foto_profil): ?>
            <img src="<?= $foto_profil ?>" alt="Logo Toko">
        <?php else: ?>
            <div class="avatar-circle"><?= strtoupper(htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8')) ?></div>
        <?php endif; ?>
    </div>
</div>
