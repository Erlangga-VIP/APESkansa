<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

// Ambil parameter filter dari URL
$penjual_id      = (int) ($_GET['penjual_id'] ?? 0);
$kategori_filter = trim($_GET['kategori'] ?? '');
if ($kategori_filter !== '') {
    $kategori_filter = kategori_normalize($kategori_filter);
}
$search_query    = trim($_GET['search'] ?? $_GET['cari'] ?? '');

// Cari info penjual jika ada filter penjual_id
$nama_toko_filter = '';
if ($penjual_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT nama FROM users WHERE user_id = ? AND role = 'penjual'");
    mysqli_stmt_bind_param($stmt, 'i', $penjual_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $nama_toko_filter = $row['nama'];
    }
    mysqli_stmt_close($stmt);
}

// Query produk dengan prepared statement
$where = [];
$params = [];
$types  = '';

if ($penjual_id > 0) {
    $where[]  = 'p.user_id = ?';
    $params[] = $penjual_id;
    $types   .= 'i';
}
if ($kategori_filter !== '') {
    $where[]  = 'p.kategori = ?';
    $params[] = $kategori_filter;
    $types   .= 's';
}
if ($search_query !== '') {
    $where[]  = '(p.nama_produk LIKE ? OR p.deskripsi LIKE ?)';
    $search   = '%' . $search_query . '%';
    $params[] = $search;
    $params[] = $search;
    $types   .= 'ss';
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
$produk_result = mysqli_stmt_get_result($stmt);

// Kategori chip
$categories = kategori_options();
?>

<main class="container page-section">
    <div class="flex-between">
        <div>
            <?php if ($penjual_id > 0 && $nama_toko_filter !== ''): ?>
                <h1 class="page-title">Produk dari Toko: <?= htmlspecialchars($nama_toko_filter, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="page-subtitle">Menampilkan produk wirausaha buatan siswa <?= htmlspecialchars($nama_toko_filter, ENT_QUOTES, 'UTF-8') ?>.</p>
            <?php elseif ($kategori_filter !== ''): ?>
                <h1 class="page-title">Kategori: <?= htmlspecialchars(kategori_label($kategori_filter), ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="page-subtitle">Produk dalam kategori <?= htmlspecialchars(kategori_label($kategori_filter), ENT_QUOTES, 'UTF-8') ?>.</p>
            <?php elseif ($search_query !== ''): ?>
                <h1 class="page-title">Hasil Pencarian: "<?= htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8') ?>"</h1>
                <p class="page-subtitle">Produk yang sesuai dengan kata kunci Anda.</p>
            <?php else: ?>
                <h1 class="page-title">Katalog Produk Siswa</h1>
                <p class="page-subtitle">Temukan kreasi terbaik buatan siswa SMKN 1 Bawang.</p>
            <?php endif; ?>
        </div>
        <?php if ($penjual_id > 0 || $kategori_filter !== '' || $search_query !== ''): ?>
            <a href="<?= page_url('produk.php') ?>" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Lihat Semua</a>
        <?php endif; ?>
    </div>

    <div class="filter-panel">
        <form action="<?= page_url('produk.php') ?>" method="GET" class="product-search-form">
            <?php if ($penjual_id > 0): ?>
                <input type="hidden" name="penjual_id" value="<?= $penjual_id ?>">
            <?php endif; ?>
            <?php if ($kategori_filter !== ''): ?>
                <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_filter, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <div class="search-input-wrap">
                <i class="fas fa-search search-input-icon" aria-hidden="true"></i>
                <input type="text" name="search" class="form-control search-input-field" placeholder="Cari produk..."
                       value="<?= htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <div class="filter-chips">
            <span>Filter:</span>
            <?php
            $all_params = [];
            if ($penjual_id > 0) {
                $all_params[] = 'penjual_id=' . $penjual_id;
            }
            if ($search_query !== '') {
                $all_params[] = 'search=' . urlencode($search_query);
            }
            $all_link = page_url('produk.php' . (count($all_params) > 0 ? '?' . implode('&', $all_params) : ''));
            $all_active = ($kategori_filter === '') ? 'btn-primary' : 'btn-outline';
            echo '<a href="' . $all_link . '" class="btn btn-sm ' . $all_active . '">Semua</a>';
            
            foreach ($categories as $slug => $label) {
                $cat_params = ['kategori=' . urlencode($slug)];
                if ($penjual_id > 0) {
                    $cat_params[] = 'penjual_id=' . $penjual_id;
                }
                if ($search_query !== '') {
                    $cat_params[] = 'search=' . urlencode($search_query);
                }
                $cat_link = page_url('produk.php?' . implode('&', $cat_params));
                $cat_active = ($kategori_filter === $slug) ? 'btn-primary' : 'btn-outline';
                echo '<a href="' . $cat_link . '" class="btn btn-sm ' . $cat_active . '">'
                    . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
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
                        <img src="<?= upload_url($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="badge badge-primary" style="position: absolute; top: var(--space-sm); right: var(--space-sm);">
                            <?= htmlspecialchars(kategori_label($row['kategori'] ?: 'lainnya'), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="product-seller"><i class="fas fa-store"></i> Oleh: <?= htmlspecialchars($row['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="product-price">Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></p>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pembeli'): ?>
                            <a href="<?= page_url('detail-produk.php?id=' . (int) $row['produk_id']) ?>" class="btn btn-primary btn-sm btn-block"><i class="fas fa-shopping-cart"></i> Beli</a>
                        <?php else: ?>
                            <a href="<?= page_url('detail-produk.php?id=' . (int) $row['produk_id']) ?>" class="btn btn-outline btn-sm btn-block"><i class="fas fa-eye"></i> Detail</a>
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