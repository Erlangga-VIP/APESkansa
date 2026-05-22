<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';

// Proteksi akses: hanya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// --- Statistik ---
$total_users     = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM users'))['total'];
$total_produk    = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM produk'))['total'];
$total_pesanan   = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM pesanan'))['total'];
$total_testimoni = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM testimoni'))['total'];

// --- Tab aktif ---
$tab = $_GET['tab'] ?? 'dashboard';

// --- Data untuk setiap tab (query di bawah) ---
$users     = [];
$produk    = [];
$pesanan   = [];
$testimoni = [];

if ($tab === 'users') {
    $stmt = mysqli_prepare($conn, 'SELECT user_id, nama, email, role, created_at FROM users ORDER BY created_at DESC');
    mysqli_stmt_execute($stmt);
    $users = mysqli_stmt_get_result($stmt);
} elseif ($tab === 'produk') {
    $stmt = mysqli_prepare($conn, '
        SELECT p.*, u.nama AS nama_penjual
        FROM produk p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.produk_id DESC
    ');
    mysqli_stmt_execute($stmt);
    $produk = mysqli_stmt_get_result($stmt);
} elseif ($tab === 'pesanan') {
    $stmt = mysqli_prepare($conn, '
        SELECT p.*, pr.nama_produk, u1.nama AS nama_pembeli, u2.nama AS nama_penjual
        FROM pesanan p
        JOIN produk pr ON p.produk_id = pr.produk_id
        JOIN users u1 ON p.pembeli_id = u1.user_id
        JOIN users u2 ON p.penjual_id = u2.user_id
        ORDER BY p.pesanan_id DESC
    ');
    mysqli_stmt_execute($stmt);
    $pesanan = mysqli_stmt_get_result($stmt);
} elseif ($tab === 'testimoni') {
    $stmt = mysqli_prepare($conn, '
        SELECT t.*, u.nama
        FROM testimoni t
        JOIN users u ON t.user_id = u.user_id
        ORDER BY t.testimoni_id DESC
    ');
    mysqli_stmt_execute($stmt);
    $testimoni = mysqli_stmt_get_result($stmt);
}
?>

<div class="dashboard">
    <!-- Sidebar Admin -->
    <div class="sidebar" style="background: var(--text-dark); color: var(--white);">
        <div class="sidebar-logo">
            <a href="../../index.php">
                <img src="../../assets/img/LOGOAPE.png" alt="APEskansa Logo" style="height: 60px !important;">
            </a>
        </div>
        <div class="sidebar-menu">
            <a href="?tab=dashboard" class="sidebar-menu-item <?= $tab === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="?tab=users" class="sidebar-menu-item <?= $tab === 'users' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>
            <a href="?tab=produk" class="sidebar-menu-item <?= $tab === 'produk' ? 'active' : '' ?>">
                <i class="fas fa-boxes"></i>
                <span>Produk</span>
            </a>
            <a href="?tab=pesanan" class="sidebar-menu-item <?= $tab === 'pesanan' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Pesanan</span>
            </a>
            <a href="?tab=testimoni" class="sidebar-menu-item <?= $tab === 'testimoni' ? 'active' : '' ?>">
                <i class="fas fa-star"></i>
                <span>Testimoni</span>
            </a>
            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 1.5rem 1rem;">
            <a href="../../index.php" class="sidebar-menu-item">
                <i class="fas fa-home"></i>
                <span>Ke Beranda</span>
            </a>
            <a href="../../process/logout.php" class="sidebar-menu-item" style="color: var(--danger);">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header glass-card" style="padding: var(--space-lg) var(--space-xl); border-radius: var(--radius-lg); margin-bottom: var(--space-xl);">
            <h1 style="margin: 0; font-weight: 700;">
                <?= match ($tab) {
                    'dashboard' => 'Dashboard Admin',
                    'users'     => 'Manajemen Pengguna',
                    'produk'    => 'Manajemen Produk',
                    'pesanan'   => 'Manajemen Pesanan',
                    'testimoni' => 'Manajemen Testimoni',
                    default     => 'Dashboard Admin'
                } ?>
            </h1>
        </div>

        <?php if ($tab === 'dashboard'): ?>
            <!-- Statistik -->
            <div class="stats-grid" style="margin-bottom: var(--space-2xl);">
                <div class="stat-card" style="border-left: 5px solid var(--primary); padding: var(--space-lg);">
                    <h3 style="font-size: var(--fs-3xl);"><?= $total_users ?></h3>
                    <p style="color: var(--text-light);">Pengguna</p>
                </div>
                <div class="stat-card" style="border-left: 5px solid var(--warning); padding: var(--space-lg);">
                    <h3 style="font-size: var(--fs-3xl);"><?= $total_produk ?></h3>
                    <p style="color: var(--text-light);">Produk</p>
                </div>
                <div class="stat-card" style="border-left: 5px solid var(--info); padding: var(--space-lg);">
                    <h3 style="font-size: var(--fs-3xl);"><?= $total_pesanan ?></h3>
                    <p style="color: var(--text-light);">Pesanan</p>
                </div>
                <div class="stat-card" style="border-left: 5px solid var(--success); padding: var(--space-lg);">
                    <h3 style="font-size: var(--fs-3xl);"><?= $total_testimoni ?></h3>
                    <p style="color: var(--text-light);">Testimoni</p>
                </div>
            </div>

        <?php elseif ($tab === 'users'): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?= (int) $u['user_id'] ?></td>
                                <td><?= htmlspecialchars($u['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="badge badge-primary"><?= $u['role'] ?></span></td>
                                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <?php if ($u['role'] !== 'admin'): ?>
                                        <a href="../../process/hapus-user.php?id=<?= (int) $u['user_id'] ?>"
                                           class="btn btn-sm btn-delete"
                                           style="background: var(--danger); color: var(--white); border: none;"
                                           onclick="return confirm('Hapus pengguna ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-light);">Admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab === 'produk'): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Penjual</th>
                            <th>Harga</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = mysqli_fetch_assoc($produk)): ?>
                            <tr>
                                <td><?= (int) $p['produk_id'] ?></td>
                                <td><?= htmlspecialchars($p['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($p['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>Rp <?= number_format((int) $p['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($p['kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="../../process/hapus-produk.php?id=<?= (int) $p['produk_id'] ?>"
                                       class="btn btn-sm btn-delete"
                                       style="background: var(--danger); color: var(--white); border: none;"
                                       onclick="return confirm('Hapus produk ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab === 'pesanan'): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Pembeli</th>
                            <th>Penjual</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ps = mysqli_fetch_assoc($pesanan)):
                            $status_class = match ($ps['status']) {
                                'diproses'   => 'badge-processing',
                                'selesai'    => 'badge-completed',
                                'dibatalkan' => 'badge-cancelled',
                                default      => 'badge-waiting'
                            };
                        ?>
                            <tr>
                                <td>#<?= (int) $ps['pesanan_id'] ?></td>
                                <td><?= htmlspecialchars($ps['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($ps['nama_pembeli'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($ps['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) $ps['jumlah'] ?></td>
                                <td>Rp <?= number_format((int) $ps['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="badge-status <?= $status_class ?>"><?= $ps['status'] ?></span></td>
                                <td>
                                    <?php if ($ps['status'] !== 'selesai' && $ps['status'] !== 'dibatalkan'): ?>
                                        <form method="POST" action="../../process/update-status-pesanan.php" style="display:inline;">
                                            <input type="hidden" name="pesanan_id" value="<?= (int) $ps['pesanan_id'] ?>">
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="btn btn-sm"
                                                    style="background: var(--success); color: var(--white); border: none;">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab === 'testimoni'): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pengguna</th>
                            <th>Isi</th>
                            <th>Rating</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($t = mysqli_fetch_assoc($testimoni)): ?>
                            <tr>
                                <td><?= (int) $t['testimoni_id'] ?></td>
                                <td><?= htmlspecialchars($t['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($t['isi'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= str_repeat('<i class="fas fa-star" style="color:var(--warning);"></i>', (int) $t['rating']) ?></td>
                                <td>
                                    <a href="../../process/hapus-testimoni.php?id=<?= (int) $t['testimoni_id'] ?>"
                                       class="btn btn-sm btn-delete"
                                       style="background: var(--danger); color: var(--white); border: none;"
                                       onclick="return confirm('Hapus testimoni ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>