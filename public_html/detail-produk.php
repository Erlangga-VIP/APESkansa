<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

// Ambil ID produk
$product_id = (int) ($_GET['id'] ?? 0);

if ($product_id <= 0) {
    die('Error: Produk tidak valid.');
}

// Query detail produk
$stmt = mysqli_prepare($conn, '
    SELECT p.*, u.nama AS nama_penjual, u.no_hp AS no_hp_penjual,
           u.foto_profil AS foto_penjual, u.email AS email_penjual
    FROM produk p
    JOIN users u ON p.user_id = u.user_id
    WHERE p.produk_id = ?
');
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    die('Produk tidak ditemukan.');
}

$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Generate WhatsApp link
$no_hp = trim($product['no_hp_penjual'] ?? '');
$has_wa = false;
$wa_link = '';
if ($no_hp !== '') {
    $has_wa = true;
    $clean_no = preg_replace('/[^0-9]/', '', $no_hp);
    if (str_starts_with($clean_no, '0')) {
        $clean_no = '62' . substr($clean_no, 1);
    }
    $pesan = 'Halo ' . $product['nama_penjual'] .
             ', saya tertarik dengan produk *' . $product['nama_produk'] .
             '* Anda yang dijual di APEskansa. Apakah masih tersedia?';
    $wa_link = 'https://wa.me/' . $clean_no . '?text=' . urlencode($pesan);
}
?>

<main class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-2xl);">
    <div class="product-detail-container">
        <!-- Gambar -->
        <div class="product-detail-image">
            <img src="uploads/<?= htmlspecialchars($product['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($product['nama_produk'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <!-- Info -->
        <div class="product-detail-info">
            <span class="badge badge-primary">
                <?= htmlspecialchars($product['kategori'] ?: 'Lainnya', ENT_QUOTES, 'UTF-8') ?>
            </span>

            <h1><?= htmlspecialchars($product['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h1>

            <p class="product-detail-price">
                Rp <?= number_format((int) $product['harga'], 0, ',', '.') ?>
            </p>

            <!-- Info Penjual -->
            <div class="product-detail-seller">
                <div class="flex-center">
                    <?php if (!empty($product['foto_penjual'])): ?>
                        <img src="uploads/<?= htmlspecialchars($product['foto_penjual'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="Foto Penjual"
                             style="width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid var(--primary);">
                    <?php else: ?>
                        <div class="avatar-circle" style="width:48px; height:48px;">
                            <?= strtoupper(mb_substr($product['nama_penjual'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <p style="font-size: var(--fs-xs); color: var(--text-light);">Penjual / Toko</p>
                        <h3 style="font-weight: 600;"><?= htmlspecialchars($product['nama_penjual'], ENT_QUOTES, 'UTF-8') ?></h3>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="product-detail-description">
                <h2>Deskripsi Produk</h2>
                <p><?= nl2br(htmlspecialchars($product['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>

            <!-- Tombol Aksi -->
            <div class="product-detail-buttons">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pembeli'): ?>
                    <button id="open-checkout-btn" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag"></i> Pesan Sekarang
                    </button>
                <?php endif; ?>

                <?php if ($has_wa): ?>
                    <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="btn btn-outline btn-lg">
                        <i class="fab fa-whatsapp"></i> Hubungi Penjual
                    </a>
                <?php else: ?>
                    <button id="open-contact-btn" class="btn btn-outline btn-lg">
                        <i class="fas fa-envelope"></i> Hubungi Penjual
                    </button>
                <?php endif; ?>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login untuk Memesan
                    </a>
                <?php elseif ($_SESSION['role'] !== 'pembeli'): ?>
                    <p style="color: var(--text-light); font-style: italic;">
                        <i class="fas fa-info-circle"></i>
                        Anda masuk sebagai <?= $_SESSION['role'] ?>.
                        Fitur pemesanan hanya untuk pembeli.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Checkout -->
<div class="modal-overlay" id="checkout-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-shopping-cart"></i> Konfirmasi Pemesanan</h3>
            <button class="modal-close-btn" id="close-checkout-btn">&times;</button>
        </div>
        <form action="process/buat-pesanan.php" method="POST">
            <input type="hidden" name="produk_id" value="<?= $product_id ?>">
            <div class="modal-body">
                <div class="flex-center" style="gap: var(--space-md); border-bottom: 1px solid var(--border); padding-bottom: var(--space-md); margin-bottom: var(--space-md);">
                    <img src="uploads/<?= htmlspecialchars($product['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="Produk"
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius-sm);">
                    <div>
                        <h4 style="font-weight: 600;"><?= htmlspecialchars($product['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h4>
                        <p style="color: var(--primary); font-weight: 700;">
                            Rp <?= number_format((int) $product['harga'], 0, ',', '.') ?>
                        </p>
                        <p style="font-size: var(--fs-xs); color: var(--text-light);">
                            <i class="fas fa-store"></i> <?= htmlspecialchars($product['nama_penjual'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="jumlah">Jumlah Pembelian</label>
                    <div class="flex-center" style="gap: var(--space-sm);">
                        <button type="button" id="btn-minus" class="btn btn-outline btn-sm">-</button>
                        <input type="number" id="jumlah" name="jumlah" value="1" min="1"
                               class="form-control" style="max-width: 80px; text-align: center;"
                               readonly>
                        <button type="button" id="btn-plus" class="btn btn-outline btn-sm">+</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catatan">Catatan Tambahan (Opsional)</label>
                    <textarea id="catatan" name="catatan" class="form-control" rows="3"
                              placeholder="Contoh: COD di Kelas XI RPL 2 jam istirahat pertama, atau varian rasa..."></textarea>
                </div>

                <div style="background: var(--primary-light); padding: var(--space-md); border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: var(--text);">Total Pembayaran:</span>
                    <span style="font-size: var(--fs-xl); font-weight: 700; color: var(--primary);" id="total-bayar-display">
                        Rp <?= number_format((int) $product['harga'], 0, ',', '.') ?>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancel-checkout-btn">Batal</button>
                <button type="submit" class="btn btn-primary">Konfirmasi & Pesan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kontak (jika tidak ada WA) -->
<?php if (!$has_wa): ?>
<div class="modal-overlay" id="contact-modal">
    <div class="modal-box" style="max-width: 420px;">
        <div class="modal-header">
            <h3><i class="fas fa-address-book"></i> Kontak Penjual</h3>
            <button class="modal-close-btn" id="close-contact-btn">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(245,158,11,0.1); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto var(--space-md);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 style="font-weight: 700; margin-bottom: var(--space-sm);">Nomor WhatsApp Belum Diatur</h4>
            <p style="color: var(--text-light); font-size: var(--fs-sm); margin-bottom: var(--space-md);">
                Penjual ini belum menyetel nomor WhatsApp di profil toko mereka.
                Anda dapat menghubungi penjual melalui email berikut:
            </p>
            <div style="background: var(--accent); padding: var(--space-sm) var(--space-md); border-radius: var(--radius-sm); font-weight: 600; color: var(--primary); word-break: break-all;">
                <i class="fas fa-envelope"></i>
                <?= htmlspecialchars($product['email_penjual'] ?: '-', ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>
        <div class="modal-footer" style="justify-content: center; border-top: none;">
            <button type="button" class="btn btn-primary" id="ok-contact-btn">Baik, Saya Mengerti</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkoutModal = document.getElementById('checkout-modal');
    const contactModal = document.getElementById('contact-modal');

    // Checkout modal
    const openCheckoutBtn = document.getElementById('open-checkout-btn');
    const closeCheckoutBtn = document.getElementById('close-checkout-btn');
    const cancelCheckoutBtn = document.getElementById('cancel-checkout-btn');

    if (openCheckoutBtn) {
        openCheckoutBtn.addEventListener('click', () => checkoutModal.classList.add('active'));
    }
    const closeCheckout = () => checkoutModal.classList.remove('active');
    if (closeCheckoutBtn) closeCheckoutBtn.addEventListener('click', closeCheckout);
    if (cancelCheckoutBtn) cancelCheckoutBtn.addEventListener('click', closeCheckout);

    // Contact modal
    const openContactBtn = document.getElementById('open-contact-btn');
    const closeContactBtn = document.getElementById('close-contact-btn');
    const okContactBtn = document.getElementById('ok-contact-btn');

    if (openContactBtn) {
        openContactBtn.addEventListener('click', () => contactModal.classList.add('active'));
    }
    const closeContact = () => contactModal.classList.remove('active');
    if (closeContactBtn) closeContactBtn.addEventListener('click', closeContact);
    if (okContactBtn) okContactBtn.addEventListener('click', closeContact);

    // Close on overlay click
    window.addEventListener('click', function (e) {
        if (e.target === checkoutModal) closeCheckout();
        if (e.target === contactModal) closeContact();
    });

    // Quantity selector
    const inputJumlah = document.getElementById('jumlah');
    const btnMinus = document.getElementById('btn-minus');
    const btnPlus = document.getElementById('btn-plus');
    const totalDisplay = document.getElementById('total-bayar-display');
    const hargaSatuan = <?= (int) $product['harga'] ?>;

    if (btnMinus && btnPlus && inputJumlah) {
        btnMinus.addEventListener('click', () => {
            let val = parseInt(inputJumlah.value);
            if (val > 1) {
                val--;
                inputJumlah.value = val;
                updateTotal(val);
            }
        });
        btnPlus.addEventListener('click', () => {
            let val = parseInt(inputJumlah.value);
            val++;
            inputJumlah.value = val;
            updateTotal(val);
        });
    }

    function updateTotal(qty) {
        const total = qty * hargaSatuan;
        totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>