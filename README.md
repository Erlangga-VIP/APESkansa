# APEskansa

Marketplace wirausaha siswa **SMKN 1 Bawang** — platform jual beli produk dan jasa antar siswa dengan peran **pembeli**, **penjual**, dan **admin**.

Stack: **PHP native** + **MySQL**, tanpa framework. Frontend **mobile-first** dengan CSS modular dan TypeScript (opsional) yang dikompilasi ke `public_html/assets/js/script.js`.

---

## Fitur Utama

| Peran | Kemampuan |
|-------|-----------|
| **Publik** | Beranda, katalog produk, detail produk, halaman toko penjual |
| **Pembeli** | Profil, pesanan, testimoni |
| **Penjual** | Ringkasan toko, profil toko, daftar/tambah/edit produk, kelola pesanan masuk |
| **Admin** | Dashboard pengguna, produk, pesanan, testimoni |

---

## Cara Menjalankan

1. Clone repositori ke folder `www/` Laragon (atau `htdocs` XAMPP).
2. Buat database dan tabel:
   - Buka `http://localhost/APESkansa/tools/patch-db.php` (hanya dari localhost), **atau**
   - Impor `tools/database.sql` lewat phpMyAdmin.
3. Sesuaikan `config/config.php`:
   - `DB_USER`, `DB_PASS` jika perlu
   - `BASE_URL` harus sesuai path proyek Anda (default: `/APEskansa/public_html/`)
4. Akses aplikasi: `http://localhost/APESkansa/public_html/`
5. *(Opsional)* Edit TypeScript: `npm install` lalu `npx tsc`

---

## Akun Demo (setelah `patch-db.php` pada DB kosong)

| Email | Password | Role |
|-------|----------|------|
| `anisa@gmail.com` | `pembeli123` | pembeli |
| `budi@gmail.com` | `penjual123` | penjual |
| `admin@smkn1bawang.sch.id` | `admin123` | admin |

---

## Struktur Folder

```
APESkansa/
├── config/
│   └── config.php          # DB, BASE_URL, helper URL & kategori
├── includes/
│   ├── header.php, footer.php
│   ├── csrf.php, flash.php
│   ├── penjual-init.php    # Auth + statistik dashboard penjual
│   ├── sidebar-penjual.php
│   └── penjual-dashboard-top.php
├── public_html/            # Document root
│   ├── index.php, produk.php, detail-produk.php, penjual.php
│   ├── login.php, register.php
│   ├── dashboard/
│   │   ├── pembeli/profil.php
│   │   ├── penjual/
│   │   │   ├── index.php      # Ringkasan toko
│   │   │   ├── profil.php     # Edit profil toko
│   │   │   ├── produk.php     # Daftar produk
│   │   │   ├── pesanan.php    # Pesanan masuk
│   │   │   ├── tambah-produk.php
│   │   │   └── edit-produk.php
│   │   └── admin/dashboard.php
│   ├── process/            # Endpoint aksi (POST + CSRF)
│   ├── assets/css/         # CSS modular
│   ├── assets/js/script.js
│   └── uploads/
├── src/
│   └── script.ts
├── tools/
│   ├── patch-db.php
│   └── database.sql
└── docs/
    └── STYLE_GUIDE.md
```

`config/` dan `includes/` berada **di luar** `public_html/` agar tidak bisa diakses langsung dari browser.

---

## Dashboard Penjual

Sidebar penjual mengarah ke halaman terpisah (bukan tab JavaScript):

| Menu | File |
|------|------|
| Ringkasan | `dashboard/penjual/index.php` |
| Profil Toko | `dashboard/penjual/profil.php` |
| Daftar Produk | `dashboard/penjual/produk.php` |
| Tambah Produk | `dashboard/penjual/tambah-produk.php` |
| Pesanan Masuk | `dashboard/penjual/pesanan.php` |
| Lihat Toko Saya | `produk.php?penjual_id={id}` |

URL lama `profil.php?tab=produk` dan `?tab=pesanan` dialihkan otomatis ke halaman baru.

---

## Keamanan

- CSRF token pada form POST di `process/`
- Prepared statements untuk query database
- `.htaccess` di `process/` — hanya metode POST (kecuali `logout.php`)
- `.htaccess` di `uploads/` — blok eksekusi PHP
- Password di-hash dengan `password_hash()`
- Validasi upload gambar berdasarkan MIME, bukan ekstensi nama file

---

## Standar Kode

- PHP: `declare(strict_types=1)`, PSR-12, helper `page_url()` / `upload_url()`
- CSS: mobile-first, variabel di `assets/css/base/variables.css`, komponen terpisah
- TypeScript: strict mode, output ke `public_html/assets/js/script.js`

Detail gaya UI: lihat `docs/STYLE_GUIDE.md`.

---

## Lisensi

Proyek pembelajaran internal SMKN 1 Bawang.
