// reorganize.js – Perbaikan EPERM dengan mkdirSync sebelum rename
const fs = require('fs');
const path = require('path');

const ROOT = __dirname;

// ========================
// DEFINISI STRUKTUR BARU
// ========================
const DIRS = {
    public: path.join(ROOT, 'public_html'),
    assets: path.join(ROOT, 'public_html', 'assets'),
    css: path.join(ROOT, 'public_html', 'assets', 'css'),
    js: path.join(ROOT, 'public_html', 'assets', 'js'),
    img: path.join(ROOT, 'public_html', 'assets', 'img'),
    uploads: path.join(ROOT, 'public_html', 'uploads'),
    process: path.join(ROOT, 'public_html', 'process'),
    config: path.join(ROOT, 'config'),
    includes: path.join(ROOT, 'includes'),
    tools: path.join(ROOT, 'tools'),
};

// ========================
// FUNGSI BANTU
// ========================
function ensureDir(dirPath) {
    if (!fs.existsSync(dirPath)) {
        fs.mkdirSync(dirPath, { recursive: true });
        console.log(`✓ Dibuat: ${path.relative(ROOT, dirPath)}`);
    }
}

function move(oldPath, newPath) {
    if (fs.existsSync(oldPath)) {
        // Pastikan folder tujuan sudah ada
        ensureDir(path.dirname(newPath));
        try {
            fs.renameSync(oldPath, newPath);
            console.log(`↪ Dipindahkan: ${path.relative(ROOT, oldPath)} → ${path.relative(ROOT, newPath)}`);
        } catch (err) {
            // Fallback: copy lalu hapus
            console.log(`⚠ Gagal rename, lakukan copy...`);
            copyRecursive(oldPath, newPath);
            fs.rmSync(oldPath, { recursive: true, force: true });
            console.log(`↪ Dicopy & dihapus: ${path.relative(ROOT, oldPath)} → ${path.relative(ROOT, newPath)}`);
        }
    }
}

function copyRecursive(src, dest) {
    const stat = fs.statSync(src);
    if (stat.isDirectory()) {
        ensureDir(dest);
        const entries = fs.readdirSync(src);
        for (const entry of entries) {
            copyRecursive(path.join(src, entry), path.join(dest, entry));
        }
    } else {
        fs.copyFileSync(src, dest);
    }
}

function updateFile(filePath, replacements) {
    if (!fs.existsSync(filePath) || !filePath.endsWith('.php')) return;
    let content = fs.readFileSync(filePath, 'utf8');
    let changed = false;
    for (const [search, replace] of Object.entries(replacements)) {
        if (content.includes(search)) {
            content = content.replace(new RegExp(search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), replace);
            changed = true;
        }
    }
    if (changed) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`✎ Diperbarui: ${path.relative(ROOT, filePath)}`);
    }
}

function updateAllPHP(dir, replacements) {
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
        const fullPath = path.join(dir, entry.name);
        if (entry.isFile() && entry.name.endsWith('.php')) {
            updateFile(fullPath, replacements);
        } else if (entry.isDirectory() && !fullPath.startsWith(DIRS.public) && entry.name !== 'node_modules') {
            updateAllPHP(fullPath, replacements);
        }
    }
}

// ========================
// EKSEKUSI
// ========================
console.log('🔧 Memulai restrukturisasi APEskansa...\n');

// 1. Buat semua folder tujuan
Object.values(DIRS).forEach(ensureDir);

// 2. Pindahkan halaman utama ke public_html
const pages = [
    'index.php', 'login.php', 'register.php', 'produk.php', 'detail-produk.php',
    'profil.php', 'penjual.php', 'penjual-dashboard.php', 'penjual-profil.php',
    'penjual-tambah-produk.php', 'penjual-edit-produk.php', 'admin-dashboard.php'
];
pages.forEach(page => move(path.join(ROOT, page), path.join(DIRS.public, page)));

// 3. Pindahkan folder aset (css, js, img, uploads)
// Pastikan parent folder ada sebelum memindahkan
ensureDir(DIRS.assets);
move(path.join(ROOT, 'css'), DIRS.css);
move(path.join(ROOT, 'js'), DIRS.js);
move(path.join(ROOT, 'img'), DIRS.img);
move(path.join(ROOT, 'uploads'), DIRS.uploads);

// 4. Pindahkan file proses dari api/ ke process/ dan hapus folder api/
if (fs.existsSync(path.join(ROOT, 'api'))) {
    ensureDir(DIRS.process);
    const apiFiles = fs.readdirSync(path.join(ROOT, 'api'));
    apiFiles.forEach(file => {
        move(path.join(ROOT, 'api', file), path.join(DIRS.process, file));
    });
    fs.rmSync(path.join(ROOT, 'api'), { recursive: true, force: true });
    console.log('✕ Dihapus: folder api/');
}

// 5. Pindahkan config.php dari process/ ke config/
move(path.join(DIRS.process, 'config.php'), path.join(DIRS.config, 'config.php'));

// 6. Hapus file sampah
['APEskansa.zip', 'detail-produk.html'].forEach(file => {
    const target = path.join(ROOT, file);
    if (fs.existsSync(target)) {
        fs.unlinkSync(target);
        console.log(`✕ Dihapus: ${file}`);
    }
});

// 7. Pemindahan patch-db.php dan database.sql ke tools/
if (fs.existsSync(path.join(ROOT, 'patch-db.php'))) {
    move(path.join(ROOT, 'patch-db.php'), path.join(DIRS.tools, 'patch-db.php'));
}
if (fs.existsSync(path.join(ROOT, 'database.sql'))) {
    move(path.join(ROOT, 'database.sql'), path.join(DIRS.tools, 'database.sql'));
}

// 8. Update path di semua file PHP yang tersisa (public_html dan process)
// Halaman di public_html
updateAllPHP(DIRS.public, {
    // Include config
    "include 'api/config.php'": "include '../config/config.php'",
    'include "api/config.php"': 'include "../config/config.php"',
    "include 'js/php/config.php'": "include '../config/config.php'",
    'include "js/php/config.php"': 'include "../config/config.php"',
    // Ubah aksi form
    "action='api/": "action='process/",
    'action="api/': 'action="process/',
    "href='api/": "href='process/",
    'href="api/': 'href="process/',
    // Asset paths
    "css/": "assets/css/",
    "js/": "assets/js/",
    "img/": "assets/img/",
    "uploads/": "uploads/",
    // Redirect internal (../../ -> ../)
    "../../index.php": "../index.php",
    "../../produk.php": "../produk.php",
    "../../profil.php": "../profil.php",
    "../../penjual-profil.php": "../penjual-profil.php",
    "../../penjual-tambah-produk.php": "../penjual-tambah-produk.php",
    "../../penjual-edit-produk.php": "../penjual-edit-produk.php",
    "../../admin-dashboard.php": "../admin-dashboard.php",
    "../../login.php": "../login.php",
    "../../register.php": "../register.php",
});

// File di process/ (endpoint)
updateAllPHP(DIRS.process, {
    "include 'config.php'": "include '../../config/config.php'",
    'include "config.php"': 'include "../../config/config.php"',
    "include '../config.php'": "include '../../config/config.php'",
    // Redirects
    "../../index.php": "../index.php",
    "../../produk.php": "../produk.php",
    "../../profil.php": "../profil.php",
    "../../penjual-profil.php": "../penjual-profil.php",
    "../../penjual-dashboard.php": "../penjual-dashboard.php",
    "../../penjual-tambah-produk.php": "../penjual-tambah-produk.php",
    "../../penjual-edit-produk.php": "../penjual-edit-produk.php",
    "../../admin-dashboard.php": "../admin-dashboard.php",
    "../../login.php": "../login.php",
    "../../register.php": "../register.php",
});

// 9. Buat .htaccess di public_html untuk keamanan
const htaccessContent = `# APEskansa - Basic Security
Options -Indexes
<FilesMatch "\.(php|inc)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
<FilesMatch "^(index|login|register|produk|detail-produk|profil|penjual|penjual-dashboard|penjual-profil|penjual-tambah-produk|penjual-edit-produk|admin-dashboard)\.php$">
    Allow from all
</FilesMatch>

# Lindungi folder process hanya untuk POST
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} !POST
    RewriteRule ^process/ - [F]
</IfModule>
`;
fs.writeFileSync(path.join(DIRS.public, '.htaccess'), htaccessContent);
console.log('🔒 File .htaccess dibuat di public_html/');

console.log('\n✅ Restrukturisasi selesai. Struktur baru:');
console.log(`
APESkansa/
├── public_html/          # Document root
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   ├── uploads/
│   ├── process/          # Endpoint pemrosesan
│   ├── *.php
│   └── .htaccess
├── config/
│   └── config.php
├── includes/
├── tools/
│   ├── patch-db.php
│   └── database.sql
└── reorganize.js
`);