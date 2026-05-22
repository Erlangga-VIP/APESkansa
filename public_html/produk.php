<?php
require_once __DIR__ . '/../includes/header.php';

// Ambil parameter filter dari URL
$penjual_id = isset($_GET['penjual_id']) ? (int)$_GET['penjual_id'] : 0;
$kategori_filter = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Cari info penjual jika ada filter penjual_id
$nama_toko_filter = "";
if ($penjual_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT nama FROM users WHERE user_id = ? AND role = 'penjual'");
    mysqli_stmt_bind_param($stmt, "i", $penjual_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $nama_toko_filter = $row['nama'];
    }
    mysqli_stmt_close($stmt);
}

// Query produk dengan prepared statement
$where_clauses = [];
$params = [];
$types = "";

if ($penjual_id > 0) {
    $where_clauses[] = "p.user_id = ?";
    $params[] = $penjual_id;
    $types .= "i";
}
if (!empty($kategori_filter)) {
    $where_clauses[] = "p.kategori = ?";
    $params[] = $kategori_filter;
    $types .= "s";
}
if (!empty($search_query)) {
    $where_clauses[] = "(p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
    $search_param = "%" . $search_query . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";
$sql = "SELECT p.*, u.nama AS nama_penjual FROM produk p JOIN users u ON p.user_id = u.user_id $where_sql ORDER BY p.produk_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$produk_result = mysqli_stmt_get_result($stmt);

// Kategori chip
$categories = ['Makanan', 'Minuman', 'Kerajinan', 'Jasa', 'Lainnya'];
?>

<main class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-2xl);">
    
    <!-- Header Dinamis -->
    <div class="flex-between" style="margin-bottom: var(--space-lg);">
        <div>
            <?php if ($penjual_id > 0 && !empty($nama_toko_filter)): ?>
                <h1 style="font-size: var(--fs-2xl); font-weight: 700;">Produk dari Toko: <?php echo htmlspecialchars($nama_toko_filter); ?></h1>
                <p style="color: var(--text-light);">Menampilkan produk wirausaha buatan siswa <?php echo htmlspecialchars($nama_toko_filter); ?>.</p>
            <?php elseif (!empty($kategori_filter)): ?>
                <h1 style="font-size: var(--fs-2xl); font-weight: 700;">Kategori: <?php echo htmlspecialchars($kategori_filter); ?></h1>
                <p style="color: var(--text-light);">Produk dalam kategori <?php echo htmlspecialchars($kategori_filter); ?>.</p>
            <?php elseif (!empty($search_query)): ?>
                <h1 style="font-size: var(--fs-2xl); font-weight: 700;">Hasil Pencarian: "<?php echo htmlspecialchars($search_query); ?>"</h1>
                <p style="color: var(--text-light);">Produk yang sesuai dengan kata kunci Anda.</p>
            <?php else: ?>
                <h1 style="font-size: var(--fs-2xl); font-weight: 700;">Katalog Produk Siswa</h1>
                <p style="color: var(--text-light);">Temukan kreasi terbaik buatan siswa SMKN 1 Bawang.</p>
            <?php endif; ?>
        </div>
        <?php if ($penjual_id > 0 || !empty($kategori_filter) || !empty($search_query)): ?>
            <a href="produk.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Lihat Semua Produk</a>
        <?php endif; ?>
    </div>

    <!-- Search & Filter -->
    <div class="search-bar" style="flex-wrap: wrap;">
        <form action="produk.php" method="GET" style="display: flex; gap: var(--space-sm); width: 100%; flex-wrap: wrap;">
            <?php if ($penjual_id > 0): ?>
                <input type="hidden" name="penjual_id" value="<?php echo $penjual_id; ?>">
            <?php endif; ?>
            <?php if (!empty($kategori_filter)): ?>
                <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_filter); ?>">
            <?php endif; ?>
            
            <div style="flex: 1; min-width: 200px; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: var(--space-md); top: 50%; transform: translateY(-50%); color: var(--text-light);"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search_query); ?>" style="padding-left: 2.5rem;">
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Cari</button>
        </form>
        
        <!-- Filter Chips -->
        <div style="display: flex; gap: var(--space-xs); flex-wrap: wrap; align-items: center; width: 100%; margin-top: var(--space-sm);">
            <span style="font-size: var(--fs-sm); font-weight: 600; color: var(--text-light); margin-right: var(--space-xs);">Filter:</span>
            <?php
            // Chip "Semua"
            $all_params = [];
            if ($penjual_id > 0) $all_params[] = "penjual_id=" . $penjual_id;
            if (!empty($search_query)) $all_params[] = "search=" . urlencode($search_query);
            $all_link = "produk.php" . (count($all_params) > 0 ? "?" . implode("&", $all_params) : "");
            $all_active = empty($kategori_filter) ? 'btn-primary' : 'btn-outline';
            echo '<a href="' . $all_link . '" class="btn btn-sm ' . $all_active . '">Semua</a>';
            
            foreach ($categories as $cat) {
                $cat_params = ["kategori=" . urlencode($cat)];
                if ($penjual_id > 0) $cat_params[] = "penjual_id=" . $penjual_id;
                if (!empty($search_query)) $cat_params[] = "search=" . urlencode($search_query);
                $cat_link = "produk.php?" . implode("&", $cat_params);
                $cat_active = ($kategori_filter === $cat) ? 'btn-primary' : 'btn-outline';
                echo '<a href="' . $cat_link . '" class="btn btn-sm ' . $cat_active . '">' . $cat . '</a>';
            }
            ?>
        </div>
    </div>

    <!-- Grid Produk -->
    <div class="products-grid">
        <?php if (mysqli_num_rows($produk_result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($produk_result)): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                        <span class="badge badge-primary" style="position: absolute; top: var(--space-sm); right: var(--space-sm);">
                            <?php echo htmlspecialchars($row['kategori'] ?: 'Lainnya'); ?>
                        </span>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                        <p class="product-seller"><i class="fas fa-store"></i> Oleh: <?php echo htmlspecialchars($row['nama_penjual']); ?></p>
                        <p class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'pembeli'): ?>
                            <a href="detail-produk.php?id=<?php echo $row['produk_id']; ?>" class="btn btn-primary btn-sm btn-block"><i class="fas fa-shopping-cart"></i> Beli</a>
                        <?php else: ?>
                            <a href="detail-produk.php?id=<?php echo $row['produk_id']; ?>" class="btn btn-outline btn-sm btn-block"><i class="fas fa-eye"></i> Detail</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: var(--space-3xl) var(--space-md); color: var(--text-light);">
                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: var(--space-md); opacity: 0.3;"></i>
                <h3 style="font-size: var(--fs-xl); font-weight: 700; color: var(--text-dark);">Produk Tidak Ditemukan</h3>
                <p style="font-size: var(--fs-sm);">Maaf, tidak ada produk yang sesuai dengan filter atau pencarian Anda.</p>
            </div>
        <?php endif; ?>
        <?php mysqli_stmt_close($stmt); ?>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>