
---

## 📐 Standar Kode

### PHP (PSR‑12)
- `declare(strict_types=1)` di setiap file
- Prepared statement untuk semua query
- Session *flash message* untuk feedback (tidak ada `alert()` inline)
- Path absolut dengan konstanta `BASE_URL` dari `config.php`

### CSS (Modular Mobile‑First)
- Variabel global di `base/variables.css`
- Pendekatan **mobile‑first** – aturan dasar untuk layar kecil, `min‑width` untuk desktop
- File dipisah per komponen / halaman
- Penamaan class kebab‑case (`.product-card`, `.badge‑status`)

### TypeScript
- Strict mode, target `ESNext`
- Event delegation untuk interaksi (tab, modal, star rating)
- Kompilasi ke `public_html/assets/js/script.js`

### Keamanan
- `config/` dan `includes/` di luar `public_html/`
- `.htaccess` hanya mengizinkan POST ke folder `process/` (kecuali `logout.php` & `update‑status‑pesanan.php`)
- Password di‑hash dengan `password_hash()`

---

## ⚙️ Cara Menjalankan

1. **Clone repositori** ke folder `www/` Laragon atau htdocs XAMPP.
2. **Import database** – jalankan `tools/patch‑db.php` lewat browser, atau impor `tools/database.sql` via phpMyAdmin.
3. **Sesuaikan `config/config.php`** – ubah `DB_USER`, `DB_PASS` jika perlu, dan pastikan `BASE_URL` sesuai dengan nama folder proyek.
4. **Install TypeScript (opsional)** – jika ingin mengedit JS, jalankan `npm install` lalu `npx tsc`.
5. **Buka browser** – akses `http://localhost/APESkansa/public_html`.

---

## Struktur Folder
APESkansa
├── config
│   └── config.php
├── docs
│   └── STYLE_GUIDE.md
├── includes
│   ├── footer.php
│   ├── header.php
│   └── sidebar-penjual.php
├── node_modules
│   ├── .bin
│   │   ├── tsc
│   │   ├── tsc.cmd
│   │   ├── tsc.ps1
│   │   ├── tsserver
│   │   ├── tsserver.cmd
│   │   └── tsserver.ps1
│   ├── .package-lock.json
│   └── typescript
│       ├── bin
│       │   ├── tsc
│       │   └── tsserver
│       ├── lib
│       │   ├── cs
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── de
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── es
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── fr
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── it
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── ja
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── ko
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── lib.d.ts
│       │   ├── lib.decorators.d.ts
│       │   ├── lib.decorators.legacy.d.ts
│       │   ├── lib.dom.asynciterable.d.ts
│       │   ├── lib.dom.d.ts
│       │   ├── lib.dom.iterable.d.ts
│       │   ├── lib.es2015.collection.d.ts
│       │   ├── lib.es2015.core.d.ts
│       │   ├── lib.es2015.d.ts
│       │   ├── lib.es2015.generator.d.ts
│       │   ├── lib.es2015.iterable.d.ts
│       │   ├── lib.es2015.promise.d.ts
│       │   ├── lib.es2015.proxy.d.ts
│       │   ├── lib.es2015.reflect.d.ts
│       │   ├── lib.es2015.symbol.d.ts
│       │   ├── lib.es2015.symbol.wellknown.d.ts
│       │   ├── lib.es2016.array.include.d.ts
│       │   ├── lib.es2016.d.ts
│       │   ├── lib.es2016.full.d.ts
│       │   ├── lib.es2016.intl.d.ts
│       │   ├── lib.es2017.arraybuffer.d.ts
│       │   ├── lib.es2017.d.ts
│       │   ├── lib.es2017.date.d.ts
│       │   ├── lib.es2017.full.d.ts
│       │   ├── lib.es2017.intl.d.ts
│       │   ├── lib.es2017.object.d.ts
│       │   ├── lib.es2017.sharedmemory.d.ts
│       │   ├── lib.es2017.string.d.ts
│       │   ├── lib.es2017.typedarrays.d.ts
│       │   ├── lib.es2018.asyncgenerator.d.ts
│       │   ├── lib.es2018.asynciterable.d.ts
│       │   ├── lib.es2018.d.ts
│       │   ├── lib.es2018.full.d.ts
│       │   ├── lib.es2018.intl.d.ts
│       │   ├── lib.es2018.promise.d.ts
│       │   ├── lib.es2018.regexp.d.ts
│       │   ├── lib.es2019.array.d.ts
│       │   ├── lib.es2019.d.ts
│       │   ├── lib.es2019.full.d.ts
│       │   ├── lib.es2019.intl.d.ts
│       │   ├── lib.es2019.object.d.ts
│       │   ├── lib.es2019.string.d.ts
│       │   ├── lib.es2019.symbol.d.ts
│       │   ├── lib.es2020.bigint.d.ts
│       │   ├── lib.es2020.d.ts
│       │   ├── lib.es2020.date.d.ts
│       │   ├── lib.es2020.full.d.ts
│       │   ├── lib.es2020.intl.d.ts
│       │   ├── lib.es2020.number.d.ts
│       │   ├── lib.es2020.promise.d.ts
│       │   ├── lib.es2020.sharedmemory.d.ts
│       │   ├── lib.es2020.string.d.ts
│       │   ├── lib.es2020.symbol.wellknown.d.ts
│       │   ├── lib.es2021.d.ts
│       │   ├── lib.es2021.full.d.ts
│       │   ├── lib.es2021.intl.d.ts
│       │   ├── lib.es2021.promise.d.ts
│       │   ├── lib.es2021.string.d.ts
│       │   ├── lib.es2021.weakref.d.ts
│       │   ├── lib.es2022.array.d.ts
│       │   ├── lib.es2022.d.ts
│       │   ├── lib.es2022.error.d.ts
│       │   ├── lib.es2022.full.d.ts
│       │   ├── lib.es2022.intl.d.ts
│       │   ├── lib.es2022.object.d.ts
│       │   ├── lib.es2022.regexp.d.ts
│       │   ├── lib.es2022.string.d.ts
│       │   ├── lib.es2023.array.d.ts
│       │   ├── lib.es2023.collection.d.ts
│       │   ├── lib.es2023.d.ts
│       │   ├── lib.es2023.full.d.ts
│       │   ├── lib.es2023.intl.d.ts
│       │   ├── lib.es2024.arraybuffer.d.ts
│       │   ├── lib.es2024.collection.d.ts
│       │   ├── lib.es2024.d.ts
│       │   ├── lib.es2024.full.d.ts
│       │   ├── lib.es2024.object.d.ts
│       │   ├── lib.es2024.promise.d.ts
│       │   ├── lib.es2024.regexp.d.ts
│       │   ├── lib.es2024.sharedmemory.d.ts
│       │   ├── lib.es2024.string.d.ts
│       │   ├── lib.es2025.collection.d.ts
│       │   ├── lib.es2025.d.ts
│       │   ├── lib.es2025.float16.d.ts
│       │   ├── lib.es2025.full.d.ts
│       │   ├── lib.es2025.intl.d.ts
│       │   ├── lib.es2025.iterator.d.ts
│       │   ├── lib.es2025.promise.d.ts
│       │   ├── lib.es2025.regexp.d.ts
│       │   ├── lib.es5.d.ts
│       │   ├── lib.es6.d.ts
│       │   ├── lib.esnext.array.d.ts
│       │   ├── lib.esnext.collection.d.ts
│       │   ├── lib.esnext.d.ts
│       │   ├── lib.esnext.date.d.ts
│       │   ├── lib.esnext.decorators.d.ts
│       │   ├── lib.esnext.disposable.d.ts
│       │   ├── lib.esnext.error.d.ts
│       │   ├── lib.esnext.full.d.ts
│       │   ├── lib.esnext.intl.d.ts
│       │   ├── lib.esnext.sharedmemory.d.ts
│       │   ├── lib.esnext.temporal.d.ts
│       │   ├── lib.esnext.typedarrays.d.ts
│       │   ├── lib.scripthost.d.ts
│       │   ├── lib.webworker.asynciterable.d.ts
│       │   ├── lib.webworker.d.ts
│       │   ├── lib.webworker.importscripts.d.ts
│       │   ├── lib.webworker.iterable.d.ts
│       │   ├── pl
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── pt-br
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── ru
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── tr
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── tsc.js
│       │   ├── tsserver.js
│       │   ├── tsserverlibrary.d.ts
│       │   ├── tsserverlibrary.js
│       │   ├── typescript.d.ts
│       │   ├── typescript.js
│       │   ├── typesMap.json
│       │   ├── typingsInstaller.js
│       │   ├── watchGuard.js
│       │   ├── zh-cn
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── zh-tw
│       │   │   └── diagnosticMessages.generated.json
│       │   ├── _tsc.js
│       │   ├── _tsserver.js
│       │   └── _typingsInstaller.js
│       ├── LICENSE.txt
│       ├── package.json
│       ├── README.md
│       ├── SECURITY.md
│       └── ThirdPartyNoticeText.txt
├── package-lock.json
├── package.json
├── public_html
│   ├── .htaccess
│   ├── assets
│   │   ├── css
│   │   │   ├── base
│   │   │   │   ├── reset.css
│   │   │   │   ├── responsive.css
│   │   │   │   └── variables.css
│   │   │   ├── components
│   │   │   │   ├── badges.css
│   │   │   │   ├── buttons.css
│   │   │   │   ├── cards.css
│   │   │   │   ├── categories.css
│   │   │   │   ├── forms.css
│   │   │   │   ├── modal.css
│   │   │   │   ├── pagination.css
│   │   │   │   ├── seller.css
│   │   │   │   ├── tabs.css
│   │   │   │   └── welcome.css
│   │   │   ├── layout
│   │   │   │   ├── footer.css
│   │   │   │   ├── grid.css
│   │   │   │   ├── header.css
│   │   │   │   ├── hero.css
│   │   │   │   └── sidebar.css
│   │   │   ├── pages
│   │   │   │   ├── auth.css
│   │   │   │   ├── home.css
│   │   │   │   ├── product-detail.css
│   │   │   │   └── product.css
│   │   │   └── style.css
│   │   ├── img
│   │   │   ├── Arill.odt
│   │   │   ├── hero-illustration.svg
│   │   │   ├── logo-white.svg
│   │   │   ├── logo.svg
│   │   │   └── LOGOAPE.png
│   │   └── js
│   │       └── script.js
│   ├── dashboard
│   │   ├── admin
│   │   │   └── dashboard.php
│   │   ├── pembeli
│   │   │   └── profil.php
│   │   └── penjual
│   │       ├── edit-produk.php
│   │       ├── profil.php
│   │       └── tambah-produk.php
│   ├── detail-produk.php
│   ├── index.php
│   ├── login.php
│   ├── penjual.php
│   ├── process
│   │   ├── ambil-produk.php
│   │   ├── beri-testimoni.php
│   │   ├── buat-pesanan.php
│   │   ├── edit-profil.php
│   │   ├── hapus-produk.php
│   │   ├── hapus-testimoni.php
│   │   ├── hapus-user.php
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── register.php
│   │   ├── tambah-produk.php
│   │   ├── update-produk.php
│   │   └── update-status-pesanan.php
│   ├── produk.php
│   ├── register.php
│   └── uploads
│       ├── 68ea1265ee935_Cuplikan layar 2025-09-11 085702.png
│       ├── 68ea14c71d962_Cuplikan layar 2025-09-11 085756.png
│       ├── 68ea632d67391_logoem.jpg
│       ├── 68ef0bd9d27f3_Cuplikan layar 2025-09-11 085702.png
│       ├── 6a055b9295a69_WIN_20260507_10_38_02_Pro.jpg
│       ├── 6a0d7fc425096_download.jpeg
│       ├── avatar_4_1779271618_19713.png
│       ├── avatar_5_1779271544_laravel.png
│       └── testi_4_1779271684_oQuvEDibAlQ5vq3pBABEIe9321ygnCDeAMiVv2~tplv-sdweummd6v-text-logo-v1_QG1haXNhc3Bi_q75.jpeg
├── README.md
├── src
│   └── script.ts
├── tools
│   ├── database.sql
│   ├── patch-db.php
│   ├── seeder-pesanan.php
│   └── uploads
└── tsconfig.json


## 👤 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Pembeli | `anisa@gmail.com` | `pembeli123` |
| Penjual | `budi@gmail.com` | `penjual123` |
| Admin | `admin@smkn1bawang.sch.id` | `admin123` |

---

## 📝 Catatan Pengembangan

- Folder `tools/` dan `node_modules/` tidak perlu di‑upload ke server produksi.