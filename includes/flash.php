<?php

declare(strict_types=1);

$flash_error = $_SESSION['error'] ?? null;
$flash_success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

if ($flash_error === null && $flash_success === null) {
    return;
}
?>
<div class="flash-stack" role="status" aria-live="polite">
    <?php if ($flash_error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>
    <?php if ($flash_success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>
</div>
