<?php
/**
 * Seeder Pesanan Dummy untuk Testing
 * Letakkan di folder tools/, jalankan sekali via browser atau CLI.
 */

// Koneksi database (relatif terhadap posisi file ini di tools/)
require_once '../config/config.php';

echo "=== Seeder Pesanan APEskansa ===\n\n";

// Pastikan tabel pesanan ada
$check = mysqli_query($conn, "SHOW TABLES LIKE 'pesanan'");
if (mysqli_num_rows($check) == 0) {
    die("Tabel 'pesanan' tidak ditemukan. Jalankan patch-db.php terlebih dahulu.\n");
}

// Cari user dengan role yang tepat
$pembeli = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, nama FROM users WHERE role='pembeli' LIMIT 1"));
$penjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, nama FROM users WHERE role='penjual' LIMIT 1"));

if (!$pembeli || !$penjual) {
    die("User pembeli atau penjual tidak ditemukan. Pastikan sudah seeding user.\n");
}

// Cari produk milik penjual
$produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT produk_id, nama_produk, harga FROM produk WHERE user_id = " . $penjual['user_id'] . " LIMIT 1"));

if (!$produk) {
    die("Penjual belum memiliki produk. Tambahkan produk dulu.\n");
}

$pembeli_id = $pembeli['user_id'];
$penjual_id = $penjual['user_id'];
$produk_id  = $produk['produk_id'];

// Buat beberapa pesanan dengan status berbeda
$pesanan = [
    [
        'jumlah' => 1,
        'total_harga' => $produk['harga'],
        'status' => 'menunggu',
        'catatan' => 'COD di kelas XI RPL 1 jam istirahat'
    ],
    [
        'jumlah' => 2,
        'total_harga' => $produk['harga'] * 2,
        'status' => 'menunggu',
        'catatan' => 'Warna merah kalau ada'
    ],
    [
        'jumlah' => 1,
        'total_harga' => $produk['harga'],
        'status' => 'diproses',
        'catatan' => 'Tolong dibungkus rapi'
    ],
];

$stmt = mysqli_prepare($conn, "INSERT INTO pesanan (produk_id, pembeli_id, penjual_id, jumlah, total_harga, status, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($pesanan as $p) {
    mysqli_stmt_bind_param($stmt, "iiiiiss", 
        $produk_id, $pembeli_id, $penjual_id, 
        $p['jumlah'], $p['total_harga'], $p['status'], $p['catatan']
    );
    if (mysqli_stmt_execute($stmt)) {
        echo "✔ Pesanan '{$p['status']}' oleh {$pembeli['nama']} ke {$penjual['nama']} berhasil.\n";
    } else {
        echo "✘ Gagal: " . mysqli_error($conn) . "\n";
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo "\n✅ Seeder selesai. Silakan login sebagai penjual ({$penjual['nama']}) untuk melihat pesanan masuk.\n";