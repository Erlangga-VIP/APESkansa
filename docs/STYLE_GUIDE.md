```markdown
# APEskansa Style Guide

Panduan lengkap untuk pengembangan tampilan APEskansa. Framework CSS ini dibangun dengan pendekatan **mobile‑first**, **modular**, dan **variabel‑based**. Semua aturan CSS mengikuti urutan properti yang konsisten untuk memudahkan pemeliharaan.

---

## Daftar Isi

1. [Struktur Folder CSS](#struktur-folder-css)
2. [Variabel Global](#variabel-global)
3. [Reset & Tipografi](#reset--tipografi)
4. [Utilitas (Layout)](#utilitas-layout)
5. [Komponen](#komponen)
   - [Tombol (Buttons)](#tombol-buttons)
   - [Kartu (Cards)](#kartu-cards)
   - [Badge Status](#badge-status)
   - [Tab Navigasi](#tab-navigasi)
   - [Form Input](#form-input)
   - [Modal](#modal)
   - [Kategori Scroll](#kategori-scroll)
   - [Daftar Penjual (Seller List)](#daftar-penjual-seller-list)
   - [Banner Selamat Datang](#banner-selamat-datang)
   - [Pagination](#pagination)
6. [Layout Halaman](#layout-halaman)
   - [Header & Navigasi](#header--navigasi)
   - [Hero Section](#hero-section)
   - [Footer](#footer)
   - [Sidebar Dashboard](#sidebar-dashboard)
7. [Halaman Spesifik](#halaman-spesifik)
   - [Beranda (Home)](#beranda-home)
   - [Katalog Produk](#katalog-produk)
   - [Detail Produk](#detail-produk)
   - [Login / Register](#login--register)
8. [Aturan Penulisan CSS](#aturan-penulisan-css)
9. [Responsive Breakpoints](#responsive-breakpoints)

---

## Struktur Folder CSS

```
public_html/assets/css/
├── base/
│   ├── variables.css      # Variabel global (warna, spasi, font)
│   ├── reset.css          # Reset & tipografi dasar
│   └── responsive.css     # Aturan max‑width untuk backward compatibility
├── layout/
│   ├── grid.css           # Grid system & flex helpers
│   ├── header.css         # Navigasi & header
│   ├── hero.css           # Hero section
│   ├── footer.css         # Footer
│   └── sidebar.css        # Sidebar dashboard
├── components/
│   ├── buttons.css        # Tombol
│   ├── cards.css          # Kartu produk, fitur, testimoni
│   ├── badges.css         # Badge status
│   ├── tabs.css           # Tab navigasi
│   ├── forms.css          # Form input
│   ├── modal.css          # Modal popup
│   ├── categories.css     # Kategori scroll
│   ├── seller.css         # Daftar penjual
│   ├── welcome.css        # Banner selamat datang
│   └── pagination.css     # Paginasi
├── pages/
│   ├── home.css           # Halaman beranda
│   ├── product.css        # Halaman katalog produk
│   ├── product-detail.css # Halaman detail produk
│   └── auth.css           # Halaman login/register
└── style.css              # Master impor
```

---

## Variabel Global

Semua variabel didefinisikan di `base/variables.css`.

```css
:root {
    /* Warna */
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --primary-light: #e0e7ff;
    --secondary: #f8fafc;
    --accent: #f1f5f9;
    --text: #334155;
    --text-light: #94a3b8;
    --text-dark: #0f172a;
    --white: #ffffff;
    --border: #e2e8f0;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --info: #3b82f6;

    /* Gradien */
    --gradient-primary: linear-gradient(135deg, #4f46e5, #7c3aed);
    --gradient-hero: linear-gradient(135deg, #f8fafc, #e0e7ff);
    --gradient-testimonial: linear-gradient(135deg, #e2e8f0, #cbd5e1);

    /* Spasi */
    --space-xs: 0.5rem;
    --space-sm: 0.75rem;
    --space-md: 1rem;
    --space-lg: 1.5rem;
    --space-xl: 2rem;
    --space-2xl: 3rem;
    --space-3xl: 4rem;

    /* Border Radius */
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;
    --radius-full: 50%;

    /* Shadow */
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.1);

    /* Font */
    --font: 'Poppins', system-ui, -apple-system, sans-serif;
    --fs-xs: 0.75rem;
    --fs-sm: 0.85rem;
    --fs-base: 0.95rem;
    --fs-lg: 1.15rem;
    --fs-xl: 1.35rem;
    --fs-2xl: 1.6rem;
    --fs-3xl: 2rem;
    --fs-4xl: 2.5rem;

    /* Dimensi */
    --header-height: 70px;
    --container-max: 1200px;
    --transition: 0.2s ease;
}
```

---

## Reset & Tipografi

File: `base/reset.css`

- Semua elemen dimulai dengan `margin: 0; padding: 0; box-sizing: border-box;`
- `body` menggunakan font `Poppins`, warna teks `var(--text)`, background `var(--white)`
- Tautan tidak memiliki dekorasi dan mewarisi warna
- Gambar bersifat block‑level dengan max‑width 100%
- `.container` membatasi lebar ke `1200px` dan memberikan padding horizontal

---

## Utilitas (Layout)

File: `layout/grid.css`

### Container
```html
<div class="container">...</div>
```

### Grid Produk
```html
<div class="products-grid">
    <div class="product-card">...</div>
</div>
```
- Mobile: 1 kolom
- ≥ 576px: 2 kolom
- ≥ 992px: 3 kolom
- ≥ 1200px: 4 kolom

### Stats Grid
```html
<div class="stats-grid">
    <div>...</div>
</div>
```
- Mobile: 1 kolom
- ≥ 576px: 2 kolom
- ≥ 768px: 3 kolom

### Flex Helpers
```html
<div class="flex-between">...</div>   <!-- justify-between + align-center -->
<div class="flex-center">...</div>    <!-- align-center + gap -->
<div class="row">...</div>            <!-- flex wrap -->
<div class="col">...</div>            <!-- flex: 1 1 250px -->
```

---

## Komponen

### Tombol (Buttons)

File: `components/buttons.css`

```html
<a href="#" class="btn btn-primary">Tombol</a>
<a href="#" class="btn btn-outline">Outline</a>
<a href="#" class="btn btn-secondary">Secondary</a>
```

**Ukuran:**
```html
<button class="btn btn-primary btn-sm">Kecil</button>
<button class="btn btn-primary btn-lg">Besar</button>
<button class="btn btn-primary btn-block">Lebar Penuh</button>
```

---

### Kartu (Cards)

File: `components/cards.css`

**Kartu Produk:**
```html
<div class="product-card">
    <div class="product-image">
        <img src="..." alt="...">
        <span class="badge badge-primary" style="position:absolute; top:8px; right:8px;">Kategori</span>
    </div>
    <div class="product-info">
        <h3 class="product-title">Nama Produk</h3>
        <p class="product-seller"><i class="fas fa-store"></i> Penjual</p>
        <p class="product-price">Rp 15.000</p>
        <a href="#" class="btn btn-primary btn-sm btn-block">Beli</a>
    </div>
</div>
```

**Kartu Fitur:**
```html
<div class="feature-card">
    <div class="feature-icon"><i class="fas fa-store"></i></div>
    <h3>Judul</h3>
    <p>Deskripsi</p>
</div>
```

**Kartu Testimoni:**
```html
<div class="testimonial-card">
    <div class="testimonial-stars">...</div>
    <p class="testimonial-text">"..."</p>
    <div class="testimonial-author">...</div>
</div>
```

---

### Badge Status

File: `components/badges.css`

```html
<span class="badge badge-primary">Primary</span>
<span class="badge-status badge-waiting">Menunggu</span>
<span class="badge-status badge-processing">Diproses</span>
<span class="badge-status badge-completed">Selesai</span>
<span class="badge-status badge-cancelled">Dibatalkan</span>
```

---

### Tab Navigasi

File: `components/tabs.css`

```html
<div class="profile-tabs">
    <button class="profile-tab-btn active" data-tab="tab1">Tab 1</button>
    <button class="profile-tab-btn" data-tab="tab2">Tab 2</button>
</div>
<div class="profile-tab-content active" id="tab-tab1">...</div>
<div class="profile-tab-content" id="tab-tab2">...</div>
```

---

### Form Input

File: `components/forms.css`

```html
<div class="form-group">
    <label for="nama">Nama Lengkap</label>
    <input type="text" id="nama" class="form-control" required>
</div>
<div class="form-group">
    <label for="pesan">Pesan</label>
    <textarea id="pesan" class="form-control" rows="4"></textarea>
</div>
```

---

### Modal

File: `components/modal.css`

```html
<div class="modal-overlay active" id="myModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Judul</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">...</div>
        <div class="modal-footer">
            <button class="btn btn-outline">Batal</button>
            <button class="btn btn-primary">Konfirmasi</button>
        </div>
    </div>
</div>
```

---

### Kategori Scroll

File: `components/categories.css`

```html
<div class="categories-scroll">
    <a href="#" class="btn btn-primary">Semua</a>
    <a href="#" class="btn btn-outline">Makanan</a>
    ...
</div>
```

---

### Daftar Penjual (Seller List)

File: `components/seller.css`

```html
<div class="seller-list">
    <div class="seller-item">
        ...
    </div>
</div>
```

---

### Banner Selamat Datang

File: `components/welcome.css`

```html
<div class="welcome-banner">
    <h1>Halo, Nama! 👋</h1>
    <p>Selamat datang kembali...</p>
</div>
```

---

### Pagination

File: `components/pagination.css`

```html
<div class="pagination">
    <a href="#">«</a>
    <a href="#" class="active">1</a>
    <a href="#">2</a>
    <a href="#">»</a>
</div>
```

---

## Layout Halaman

### Header & Navigasi

File: `layout/header.css`

- Mobile: hamburger menu
- Desktop (≥ 992px): menu horizontal penuh

```html
<header class="header">
    <div class="container header-content">
        <div class="logo"><img src="assets/img/LOGOAPE.png" alt="Logo"></div>
        <nav class="nav">
            <ul class="nav-list">
                <li><a href="#" class="nav-link active">Beranda</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <a href="#" class="avatar-circle">A</a>
        </div>
        <button class="mobile-menu-toggle">...</button>
    </div>
</header>
```

---

### Hero Section

File: `layout/hero.css`

```html
<section class="hero">
    <div class="container hero-content">
        <div class="hero-text">
            <span class="hero-badge">Label</span>
            <h1 class="hero-title">Judul <span>Sorotan</span></h1>
            <p class="hero-desc">Deskripsi...</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary btn-lg">CTA</a>
            </div>
        </div>
        <div class="hero-image"><img src="..." alt="..."></div>
    </div>
</section>
```

---

### Footer

File: `layout/footer.css`

```html
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">...</div>
            <div><h3>Navigasi</h3>...</div>
            <div><h3>Kontak</h3>...</div>
            <div><h3>Media Sosial</h3>...</div>
        </div>
        <div class="footer-bottom"><p>&copy; 2026</p></div>
    </div>
</footer>
```

---

### Sidebar Dashboard

File: `layout/sidebar.css`

```html
<div class="sidebar">
    <div class="sidebar-logo"><img src="..." alt="..."></div>
    <div class="sidebar-menu">
        <a href="#" class="sidebar-menu-item active">
            <i class="fas fa-store"></i>
            <span>Toko</span>
        </a>
    </div>
</div>
```

---

## Halaman Spesifik

### Beranda (Home)

File: `pages/home.css`

- **Stats Section**: `.stats-section .stats-card .stats-grid`
- **Features**: `.features .features-title .feature-card .feature-icon`
- **Testimonials**: `.testimonials .testimonials-title .testimonials-slider .testimonial-card`
- **Search Bar**: `.search-bar`
- **Table**: `.table-responsive .table`

### Katalog Produk

File: `pages/product.css`

- **Filter Chips**: `.filter-chips`

### Detail Produk

File: `pages/product-detail.css`

- **Container**: `.product-detail-container`
- **Image**: `.product-detail-image`
- **Info**: `.product-detail-info`
- **Seller**: `.product-detail-seller`
- **Buttons**: `.product-detail-buttons`

### Login / Register

File: `pages/auth.css`

```html
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-logo"><img src="..." alt="..."></div>
        <h1 class="auth-title">Login Akun</h1>
        <form class="auth-form">...</form>
        <div class="auth-links"><a href="#">...</a></div>
    </div>
</body>
```

---

## Aturan Penulisan CSS

1. **Urutan properti yang konsisten**: `display` → `position` → `width/height` → `margin/padding` → `border` → `font` → `color` → `background` → `transition/animation` → sisanya.
2. **Gunakan variabel** untuk warna, spasi, ukuran font.
3. **Mobile‑first**: tulis aturan dasar untuk mobile, tambahkan `@media (min-width: ...)` untuk layar lebih besar.
4. **Satu file, satu komponen** – jangan campur komponen berbeda.
5. **Hindari inline style** – gunakan class yang sudah didefinisikan.
6. **Komentar pemisah** untuk setiap bagian dengan format `/* ---------- Nama Bagian ---------- */`.

---

## Responsive Breakpoints

| Lebar Layar | Keterangan |
|-------------|------------|
| Default | Mobile (< 576px) |
| 576px | Tablet kecil |
| 768px | Tablet |
| 992px | Desktop kecil / laptop |
| 1200px | Desktop besar |
```