<?php
/**
 * APEskansa Database Auto-Patch Script
 * File ini digunakan untuk menambahkan kolom-kolom baru dan membuat tabel yang diperlukan
 * agar fitur Pesanan, WhatsApp, dan Testimoni dapat berjalan dengan lancar.
 */

// Sertakan konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_skansaape";

echo "=== Memulai Database Auto-Patch APEskansa ===\n\n";

// 1. Membuat koneksi awal ke MySQL Server
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Koneksi ke server gagal: " . mysqli_connect_error() . "\n");
}

// 2. Cek apakah database ada, jika tidak buat database-nya
$db_check = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
if (mysqli_num_rows($db_check) == 0) {
    echo "Database '$dbname' tidak ditemukan. Membuat database baru...\n";
    if (mysqli_query($conn, "CREATE DATABASE `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
        echo "Database '$dbname' berhasil dibuat.\n";
    } else {
        die("Gagal membuat database: " . mysqli_error($conn) . "\n");
    }
}

// Pilih database
if (!mysqli_select_db($conn, $dbname)) {
    die("Gagal memilih database '$dbname': " . mysqli_error($conn) . "\n");
}

// 3. Cek & buat tabel 'users' jika belum ada
$create_users = "
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pembeli','penjual','admin') NOT NULL DEFAULT 'pembeli',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
if (mysqli_query($conn, $create_users)) {
    echo "Tabel 'users' aman (ada/berhasil dibuat).\n";
} else {
    echo "Error membuat tabel users: " . mysqli_error($conn) . "\n";
}

// 4. Tambah kolom 'no_hp' ke tabel 'users' jika belum ada
$check_nohp = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'no_hp'");
if (mysqli_num_rows($check_nohp) == 0) {
    if (mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `no_hp` varchar(20) DEFAULT NULL AFTER `email`")) {
        echo "Kolom 'no_hp' berhasil ditambahkan ke tabel 'users'.\n";
    } else {
        echo "Gagal menambahkan kolom 'no_hp': " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Kolom 'no_hp' sudah ada di tabel 'users'.\n";
}

// 5. Tambah kolom 'foto_profil' ke tabel 'users' jika belum ada
$check_foto = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'foto_profil'");
if (mysqli_num_rows($check_foto) == 0) {
    if (mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `foto_profil` varchar(255) DEFAULT NULL AFTER `no_hp`")) {
        echo "Kolom 'foto_profil' berhasil ditambahkan ke tabel 'users'.\n";
    } else {
        echo "Gagal menambahkan kolom 'foto_profil': " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Kolom 'foto_profil' sudah ada di tabel 'users'.\n";
}

// 6. Cek & buat tabel 'produk' jika belum ada
$create_produk = "
CREATE TABLE IF NOT EXISTS `produk` (
  `produk_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`produk_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `fk_produk_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
if (mysqli_query($conn, $create_produk)) {
    echo "Tabel 'produk' aman (ada/berhasil dibuat).\n";
} else {
    echo "Error membuat tabel produk: " . mysqli_error($conn) . "\n";
}

// 7. Tambah kolom 'kategori' ke tabel 'produk' jika belum ada
$check_kategori = mysqli_query($conn, "SHOW COLUMNS FROM `produk` LIKE 'kategori'");
if (mysqli_num_rows($check_kategori) == 0) {
    if (mysqli_query($conn, "ALTER TABLE `produk` ADD COLUMN `kategori` enum('makanan','minuman','kerajinan','jasa','lainnya') NOT NULL DEFAULT 'makanan' AFTER `gambar`")) {
        echo "Kolom 'kategori' berhasil ditambahkan ke tabel 'produk'.\n";
    } else {
        echo "Gagal menambahkan kolom 'kategori': " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Kolom 'kategori' sudah ada di tabel 'produk'.\n";
}

// 8. Buat tabel 'pesanan'
$create_pesanan = "
CREATE TABLE IF NOT EXISTS `pesanan` (
  `pesanan_id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `pembeli_id` int(11) NOT NULL,
  `penjual_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `total_harga` int(11) NOT NULL,
  `status` enum('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pesanan_id`),
  KEY `produk_id_idx` (`produk_id`),
  KEY `pembeli_id_idx` (`pembeli_id`),
  KEY `penjual_id_idx` (`penjual_id`),
  CONSTRAINT `fk_pesanan_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pesanan_pembeli` FOREIGN KEY (`pembeli_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pesanan_penjual` FOREIGN KEY (`penjual_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
if (mysqli_query($conn, $create_pesanan)) {
    echo "Tabel 'pesanan' berhasil dibuat/sudah ada.\n";
} else {
    echo "Gagal membuat tabel 'pesanan': " . mysqli_error($conn) . "\n";
}

// 9. Buat tabel 'testimoni'
$create_testimoni = "
CREATE TABLE IF NOT EXISTS `testimoni` (
  `testimoni_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `isi` text NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`testimoni_id`),
  KEY `user_id_testimoni_idx` (`user_id`),
  CONSTRAINT `fk_testimoni_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
if (mysqli_query($conn, $create_testimoni)) {
    echo "Tabel 'testimoni' berhasil dibuat/sudah ada.\n";
} else {
    echo "Gagal membuat tabel 'testimoni': " . mysqli_error($conn) . "\n";
}

// 10. Tambah data pembeli dummy & penjual dummy jika database masih kosong untuk keperluan demo ulasan
$users_check = mysqli_query($conn, "SELECT * FROM `users` LIMIT 1");
if (mysqli_num_rows($users_check) == 0) {
    echo "Tabel 'users' kosong. Menambahkan data pengguna default...\n";
    $pass_pembeli = password_hash("pembeli123", PASSWORD_DEFAULT);
    $pass_penjual = password_hash("penjual123", PASSWORD_DEFAULT);
    $pass_admin = password_hash("admin123", PASSWORD_DEFAULT);
    
    mysqli_query($conn, "INSERT INTO `users` (nama, email, password, no_hp, role) VALUES 
        ('Anisa Rahma', 'anisa@gmail.com', '$pass_pembeli', '081234567890', 'pembeli'),
        ('Budi Santoso', 'budi@gmail.com', '$pass_penjual', '089876543210', 'penjual'),
        ('Citra Dewi', 'citra@gmail.com', '$pass_pembeli', '085678901234', 'pembeli'),
        ('Rian Hidayat', 'rian@gmail.com', '$pass_penjual', '082134567899', 'penjual'),
        ('Administrator', 'admin@smkn1bawang.sch.id', '$pass_admin', '081111111111', 'admin')
    ");
    echo "Pengguna default berhasil dibuat (anisa@gmail.com / pembeli123, budi@gmail.com / penjual123, dst).\n";
}

// 11. Seeding testimoni awal jika tabel testimoni masih kosong
$testi_check = mysqli_query($conn, "SELECT * FROM `testimoni` LIMIT 1");
if (mysqli_num_rows($testi_check) == 0) {
    echo "Tabel 'testimoni' kosong. Melakukan seeding data testimoni default...\n";
    
    // Cari user_id dari user yang ada
    $user_res = mysqli_query($conn, "SELECT user_id, nama FROM users WHERE nama IN ('Anisa Rahma', 'Budi Santoso', 'Citra Dewi') LIMIT 3");
    $users_map = [];
    while ($u = mysqli_fetch_assoc($user_res)) {
        $users_map[$u['nama']] = $u['user_id'];
    }
    
    if (!empty($users_map)) {
        $id_anisa = isset($users_map['Anisa Rahma']) ? $users_map['Anisa Rahma'] : 1;
        $id_budi = isset($users_map['Budi Santoso']) ? $users_map['Budi Santoso'] : 2;
        $id_citra = isset($users_map['Citra Dewi']) ? $users_map['Citra Dewi'] : 3;
        
        mysqli_query($conn, "INSERT INTO `testimoni` (user_id, isi, rating, gambar) VALUES 
            ($id_anisa, 'APEskansa membantu saya menjual hasil kerajinan tangan ke teman-teman sekolah. Sangat mudah digunakan!', 5, 'testi_1.png'),
            ($id_budi, 'Berkat APEskansa, saya bisa menjual makanan buatan sendiri dan mendapatkan penghasilan tambahan.', 5, 'testi_2.png'),
            ($id_citra, 'Platform yang sangat membantu untuk mengembangkan jiwa kewirausahaan siswa di sekolah kami. Transaksinya aman!', 5, 'testi_3.png')
        ");
        echo "Data testimoni default berhasil ditambahkan.\n";
    }
}

// 12. Seeding produk default jika tabel produk masih kosong untuk menjamin visual beranda langsung terisi
$prod_check = mysqli_query($conn, "SELECT * FROM `produk` LIMIT 1");
if (mysqli_num_rows($prod_check) == 0) {
    echo "Tabel 'produk' kosong. Melakukan seeding produk default...\n";
    
    // Cari penjual
    $penjual_res = mysqli_query($conn, "SELECT user_id FROM users WHERE role = 'penjual' LIMIT 1");
    if ($p_row = mysqli_fetch_assoc($penjual_res)) {
        $id_penjual = $p_row['user_id'];
        
        // Buat folder uploads jika belum ada
        $uploads_dir = "uploads";
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }
        
        mysqli_query($conn, "INSERT INTO `produk` (user_id, nama_produk, harga, deskripsi, gambar, kategori) VALUES 
            ($id_penjual, 'Brownies Lumer Keju', 15000, 'Brownies cokelat panggang premium dengan limpahan saus cokelat lumer dan taburan keju cheddar parut di atasnya. Dibuat fresh setiap hari di jurusan Kuliner.', 'brownies.jpg', 'makanan'),
            ($id_penjual, 'Es Matcha Latte Gula Aren', 8000, 'Minuman es matcha latte segar beraroma harum teh hijau Jepang dengan manis legit sirup gula aren alami. Sangat menyegarkan diminum di jam istirahat sekolah.', 'matcha.jpg', 'minuman'),
            ($id_penjual, 'Gantungan Kunci Rajut Karakter', 12000, 'Gantungan kunci rajut handmade dengan berbagai karakter lucu (hewan, emoji, dll). Dibuat dengan benang rajut premium yang kuat dan rapi.', 'gantungan_rajut.jpg', 'kerajinan'),
            ($id_penjual, 'Jasa Instalasi OS & Software', 35000, 'Melayani jasa instalasi sistem operasi Windows/Linux, driver laptop, software dasar, dan software desain (Adobe, Corel). Dikerjakan profesional oleh siswa kelas XI RPL.', 'jasa_install.jpg', 'jasa'),
            ($id_penjual, 'Buku Ring Kertas Binder A5', 10000, 'Buku tulis binder ukuran A5 dengan cover PP transparan doff estetik, ring kokoh yang bisa dibuka tutup, berisi 60 lembar kertas garis.', 'binder.jpg', 'lainnya')
        ");
        echo "Produk default berhasil ditambahkan untuk demo.\n";
    }
}

mysqli_close($conn);
echo "\n=== Database Auto-Patch Selesai dengan Sukses! ===\n";
?>
