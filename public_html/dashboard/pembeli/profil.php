<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/header.php';

// Pastikan hanya pembeli yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header('Location: ../../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil info pengguna
$stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$user_initial = mb_substr($user_data['nama'] ?? '', 0, 1);
$foto_profil = !empty($user_data['foto_profil'])
    ? '../../uploads/' . htmlspecialchars($user_data['foto_profil'], ENT_QUOTES, 'UTF-8')
    : null;
$no_hp = htmlspecialchars($user_data['no_hp'] ?? '', ENT_QUOTES, 'UTF-8');

// Statistik
$total_pesanan  = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE pembeli_id = $user_id"))['total'];
$pesanan_proses = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE pembeli_id = $user_id AND status = 'diproses'"))['total'];
$pesanan_selesai = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE pembeli_id = $user_id AND status = 'selesai'"))['total'];
?>

<main class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-2xl);">

    <!-- Profile Header -->
    <div class="flex-between" style="background: var(--white); padding: var(--space-xl); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-lg);">
        <div class="flex-center">
            <?php if ($foto_profil): ?>
                <img src="<?= $foto_profil ?>" alt="Foto Profil" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
            <?php else: ?>
                <div class="avatar-circle" style="width: 80px; height: 80px; font-size: var(--fs-2xl);">
                    <?= strtoupper(htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8')) ?>
                </div>
            <?php endif; ?>
            <div>
                <h1 style="font-size: var(--fs-2xl); font-weight: 700;">
                    <?= htmlspecialchars($user_data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p style="color: var(--text-light);">
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($user_data['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <span class="badge badge-primary">
                    <i class="fas fa-user-check"></i> <?= ucfirst($user_data['role'] ?? 'pembeli') ?>
                </span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div style="text-align: center; background: var(--primary-light); padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md);">
                <h3 style="font-size: var(--fs-2xl); font-weight: 700; color: var(--primary);"><?= $total_pesanan ?></h3>
                <p style="font-size: var(--fs-xs); color: var(--text-light);">Total Belanja</p>
            </div>
            <div style="text-align: center; background: rgba(59,130,246,0.1); padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md);">
                <h3 style="font-size: var(--fs-2xl); font-weight: 700; color: var(--info);"><?= $pesanan_proses ?></h3>
                <p style="font-size: var(--fs-xs); color: var(--text-light);">Diproses</p>
            </div>
            <div style="text-align: center; background: rgba(16,185,129,0.1); padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md);">
                <h3 style="font-size: var(--fs-2xl); font-weight: 700; color: var(--success);"><?= $pesanan_selesai ?></h3>
                <p style="font-size: var(--fs-xs); color: var(--text-light);">Selesai</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div style="background: var(--white); padding: var(--space-xl); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <div class="profile-tabs">
            <button class="profile-tab-btn active" data-tab="profil"><i class="fas fa-id-card"></i> Profil Saya</button>
            <button class="profile-tab-btn" data-tab="pesanan"><i class="fas fa-shopping-bag"></i> Pesanan Saya</button>
            <button class="profile-tab-btn" data-tab="testimoni"><i class="fas fa-star"></i> Tulis Testimoni</button>
        </div>

        <!-- Tab 1: Profil -->
        <div class="profile-tab-content active" id="tab-profil">
            <h3 style="font-size: var(--fs-xl); font-weight: 600; margin-bottom: var(--space-lg);">Informasi Akun</h3>
            <form action="../../process/edit-profil.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                           value="<?= htmlspecialchars($user_data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($user_data['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="no_hp">No. WhatsApp (Aktif)</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control"
                           placeholder="Contoh: 081234567890" value="<?= $no_hp ?>">
                    <small style="color: var(--text-light);">Gunakan format nomor HP biasa (dimulai dengan 08 / 62).</small>
                </div>
                <div class="form-group">
                    <label for="foto_profil">Foto Profil</label>
                    <input type="file" id="foto_profil" name="foto_profil" class="form-control" accept="image/*">
                    <small style="color: var(--text-light);">Pilih file gambar jika ingin mengganti foto profil (Max 2MB).</small>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
            </form>
        </div>

        <!-- Tab 2: Pesanan Saya -->
        <div class="profile-tab-content" id="tab-pesanan">
            <h3 style="font-size: var(--fs-xl); font-weight: 600; margin-bottom: var(--space-lg);">Riwayat Belanja Saya</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Penjual</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($conn, '
                            SELECT p.*, pr.nama_produk, pr.gambar, u.nama AS nama_penjual
                            FROM pesanan p
                            JOIN produk pr ON p.produk_id = pr.produk_id
                            JOIN users u ON p.penjual_id = u.user_id
                            WHERE p.pembeli_id = ?
                            ORDER BY p.pesanan_id DESC
                        ');
                        mysqli_stmt_bind_param($stmt, 'i', $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                                $status_class = match ($row['status']) {
                                    'diproses'   => 'badge-processing',
                                    'selesai'    => 'badge-completed',
                                    'dibatalkan' => 'badge-cancelled',
                                    default      => 'badge-waiting'
                                };
                        ?>
                            <tr>
                                <td>
                                    <div class="flex-center">
                                        <img src="../../uploads/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                                             width="50" height="50" style="object-fit:cover; border-radius: var(--radius-sm);" alt="Gambar">
                                        <span style="font-weight: 600;"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="text-align: center;"><?= (int) $row['jumlah'] ?></td>
                                <td style="font-weight: 700; color: var(--primary);">Rp <?= number_format((int) $row['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="badge-status <?= $status_class ?>"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td style="font-size: var(--fs-xs); color: var(--text-light); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    title="<?= htmlspecialchars($row['catatan'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($row['catatan'] ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                        <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: var(--space-xl); color: var(--text-light);">
                                    Anda belum memesan produk apa pun.
                                </td>
                            </tr>
                        <?php endif;
                        mysqli_stmt_close($stmt);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: Tulis Testimoni -->
        <div class="profile-tab-content" id="tab-testimoni">
            <h3 style="font-size: var(--fs-xl); font-weight: 600; margin-bottom: var(--space-sm);">Beri Testimoni & Penilaian</h3>
            <p style="color: var(--text-light); font-size: var(--fs-sm); margin-bottom: var(--space-lg);">
                Bagikan pengalaman belanja Anda di APEskansa.
            </p>
            <form action="../../process/beri-testimoni.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                <div class="form-group">
                    <label>Nilai Kualitas Pelayanan / Aplikasi</label>
                    <div style="display: flex; gap: var(--space-xs); font-size: 1.75rem; color: var(--warning);" id="star-selector">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star" data-value="<?= $i ?>" style="cursor: pointer;"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating-value" value="5">
                </div>
                <div class="form-group">
                    <label for="isi">Isi Ulasan Testimoni</label>
                    <textarea id="isi" name="isi" class="form-control" rows="4"
                              placeholder="Tulis pendapat atau kritik saran Anda di sini..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="gambar">Foto Ulasan / Pendukung (Opsional)</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Kirim Testimoni
                </button>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tab switcher
    const tabs = document.querySelectorAll('.profile-tab-btn');
    const contents = document.querySelectorAll('.profile-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('tab-' + targetTab).classList.add('active');
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        const targetTabBtn = document.querySelector(`.profile-tab-btn[data-tab="${tabParam}"]`);
        if (targetTabBtn) targetTabBtn.click();
    }

    // Star rating
    const stars = document.querySelectorAll('#star-selector i');
    const ratingInput = document.getElementById('rating-value');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = parseInt(star.getAttribute('data-value'));
            ratingInput.value = value;
            stars.forEach((s, idx) => {
                s.className = idx < value ? 'fas fa-star' : 'far fa-star';
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>