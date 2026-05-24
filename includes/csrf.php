<?php

declare(strict_types=1);

function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = $_POST['csrf_token'] ?? '';

    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_require(): void
{
    if (!csrf_verify()) {
        $_SESSION['error'] = 'Permintaan tidak valid. Silakan muat ulang halaman dan coba lagi.';
        $fallback = defined('BASE_URL') ? BASE_URL . 'index.php' : '/';
        header('Location: ' . $fallback);
        exit;
    }
}
