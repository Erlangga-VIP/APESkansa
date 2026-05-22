<?php

declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_skansaape');
define('BASE_URL', '/APEskansa/public_html/');

require_once __DIR__ . '/../includes/csrf.php';

/** @return array<string, string> slug kategori => label tampilan */
function kategori_options(): array
{
    return [
        'makanan'   => 'Makanan',
        'minuman'   => 'Minuman',
        'kerajinan' => 'Kerajinan',
        'jasa'      => 'Jasa',
        'lainnya'   => 'Lainnya',
    ];
}

function kategori_normalize(string $input): string
{
    $input = trim($input);
    $options = kategori_options();

    if (isset($options[$input])) {
        return $input;
    }

    foreach ($options as $slug => $label) {
        if (strcasecmp($input, $label) === 0 || strcasecmp($input, $slug) === 0) {
            return $slug;
        }
    }

    return 'lainnya';
}

function kategori_label(string $slug): string
{
    $options = kategori_options();

    return $options[$slug] ?? ucfirst($slug);
}

function upload_url(string $filename): string
{
    return BASE_URL . 'uploads/' . rawurlencode($filename);
}

function page_url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

/** @return string|null ekstensi aman dari MIME, null jika tidak didukung */
function allowed_image_extension(string $mime): ?string
{
    return match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        default      => null,
    };
}

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');