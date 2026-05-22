<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';

$penjual_id      = isset($_GET['penjual_id']) ? (int) $_GET['penjual_id'] : 0;
$kategori_filter = trim($_GET['kategori'] ?? '');
$search_query    = trim($_GET['search'] ?? '');

$where = [];
$params = [];
$types = '';

if ($penjual_id > 0) {
    $where[]   = 'p.user_id = ?';
    $params[]  = $penjual_id;
    $types    .= 'i';
}
if ($kategori_filter !== '') {
    $where[]   = 'p.kategori = ?';
    $params[]  = $kategori_filter;
    $types    .= 's';
}
if ($search_query !== '') {
    $where[]   = '(p.nama_produk LIKE ? OR p.deskripsi LIKE ?)';
    $search    = '%' . $search_query . '%';
    $params[]  = $search;
    $params[]  = $search;
    $types    .= 'ss';
}

$sql = 'SELECT p.*, u.nama AS nama_penjual
        FROM produk p
        JOIN users u ON p.user_id = u.user_id';
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.produk_id DESC';

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$produk = [];
while ($row = mysqli_fetch_assoc($result)) {
    $produk[] = [
        'produk_id'    => (int) $row['produk_id'],
        'nama'         => $row['nama_produk'],
        'harga'        => (int) $row['harga'],
        'deskripsi'    => $row['deskripsi'],
        'kategori'     => $row['kategori'],
        'gambar'       => $row['gambar'],
        'nama_penjual' => $row['nama_penjual'],
    ];
}

echo json_encode([
    'success' => true,
    'data'    => $produk,
], JSON_UNESCAPED_UNICODE);
exit;