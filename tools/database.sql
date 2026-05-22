-- APEskansa Database Schema - v2.0 (Complete Setup)
--
-- Skrip ini membuat tabel `users`, `produk`, `pesanan`, dan `testimoni`
-- beserta relasi foreign key yang diperlukan.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Struktur tabel untuk `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pembeli','penjual','admin') NOT NULL DEFAULT 'pembeli',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Struktur tabel untuk `produk`
--
CREATE TABLE IF NOT EXISTS `produk` (
  `produk_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `kategori` enum('makanan','minuman','kerajinan','jasa','lainnya') NOT NULL DEFAULT 'makanan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`produk_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `fk_produk_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Struktur tabel untuk `pesanan`
--
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

--
-- Struktur tabel untuk `testimoni`
--
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

COMMIT;