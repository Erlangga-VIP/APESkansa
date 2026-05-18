<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penjual - APEskansa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
<img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 70px !important;">
                    </a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li class="nav-item"><a href="index.php" class="nav-link">Beranda</a></li>
                        <li class="nav-item"><a href="produk.php" class="nav-link">Produk</a></li>
                        <li class="nav-item"><a href="penjual.php" class="nav-link active">Penjual</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <a href="login.php" class="btn btn-outline">Masuk</a>
                    <a href="register.php" class="btn btn-primary">Daftar</a>
                </div>
                <button class="mobile-menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Sellers Section -->
    <section class="products"> <!-- Menggunakan class 'products' untuk styling yang konsisten -->
        <div class="container">
            <h1 class="section-title">Daftar Penjual</h1>
            
            <div class="products-grid"> <!-- Menggunakan 'products-grid' untuk layout kartu -->
                <?php
                include 'js/php/config.php'; // Koneksi ke database

                $sql = "SELECT nama, email FROM users WHERE role = 'penjual'";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        // Menampilkan data setiap penjual
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='product-card'>"; // Card untuk setiap penjual
                            echo "    <div class='product-info'>"; // Info di dalam card
                            echo "        <h3 class='product-title'>" . htmlspecialchars($row['nama']) . "</h3>"; // Nama Penjual
                            echo "        <p class='product-seller'>Email: " . htmlspecialchars($row['email']) . "</p>"; // Info tambahan, misal email
                            echo "        <div class='product-buttons'>";
                            echo "            <a href='#' class='btn btn-primary'>Lihat Produk</a>";
                            echo "        </div>";
                            echo "    </div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Belum ada penjual yang terdaftar.</p>";
                    }
                } else {
                    // Menampilkan pesan error jika query gagal
                    echo "<p>Terjadi kesalahan saat mengambil data penjual.</p>";
                    // Untuk debugging, Anda bisa menambahkan: echo "Error: " . mysqli_error($conn);
                }

                mysqli_close($conn);
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;">
                    <p>Marketplace Siswa SMKN 1 Bawang</p>
                </div>
                <div class="footer-links">
                    <h3>Navigasi</h3>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="produk.php">Produk</a></li>
                        <li><a href="penjual.php">Penjual</a></li>
                        <li><a href="#">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Kontak</h3>
                    <ul>
                        <li><i class='fas fa-map-marker-alt'></i> Jl. Raya Bawang, Banjarnegara</li>
                        <li><i class='fas fa-phone'></i> (0286) 591256</li>
                        <li><i class='fas fa-envelope'></i> info@smkn1bawang.sch.id</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Media Sosial</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 APEskansa - SMKN 1 Bawang. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
