// cleanup-misc.js – Pindahkan isi misc.css ke file yang sesuai
const fs = require('fs');
const path = require('path');

const ROOT = __dirname;
const MISC_FILE = path.join(ROOT, 'public_html', 'assets', 'css', '_uncategorized_orphan.css');
const TARGET_DIR = path.join(ROOT, 'public_html', 'assets', 'css');

// Mapping selector (kata kunci) -> [folder, filename]
const TARGET_MAP = [
// Tambahan mapping untuk menangkap selector yang lolos
{ keywords: ['.header-content'], folder: 'layout', file: 'header.css' },
{ keywords: ['.nav', '.auth-buttons'], folder: 'layout', file: 'header.css' },
{ keywords: ['.nav.active', '.nav-list', '.auth-buttons.active'], folder: 'layout', file: 'header.css' },
{ keywords: ['.section-title'], folder: 'base', file: 'reset.css' },
{ keywords: ['.hero-text h1', '.hero-container', '.hero-content', '.hero-image'], folder: 'layout', file: 'hero.css' },
{ keywords: ['.testimonials-container', '.testimonial-card'], folder: 'pages', file: 'home.css' },
{ keywords: ['.footer-container', '.footer-logo', '.footer-links', '.footer-contact'], folder: 'layout', file: 'footer.css' },
{ keywords: ['.product-detail-container', '.product-detail-image', '.product-detail-info'], folder: 'pages', file: 'product-detail.css' },
{ keywords: ['.auth-container'], folder: 'pages', file: 'auth.css' },
{ keywords: ['.hero-buttons'], folder: 'layout', file: 'hero.css' },
{ keywords: ['.feature-card'], folder: 'pages', file: 'home.css' },
{ keywords: ['.footer-content'], folder: 'layout', file: 'footer.css' },
{ keywords: ['.product-grid'], folder: 'pages', file: 'product.css' },
{ keywords: ['.stats-container'], folder: 'pages', file: 'home.css' },
{ keywords: ['.btn'], folder: 'components', file: 'buttons.css' },
{ keywords: ['.admin-profile'], folder: 'layout', file: 'sidebar.css' },
{ keywords: ['.pagination'], folder: 'components', file: 'pagination.css' }
];

function ensureDir(dir) {
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
}

function appendToFile(filePath, content) {
    ensureDir(path.dirname(filePath));
    fs.appendFileSync(filePath, '\n' + content, 'utf8');
}

function getTarget(selector) {
    for (const item of TARGET_MAP) {
        if (item.keywords.some(k => selector.includes(k))) {
            return item;
        }
    }
    return null;
}

if (!fs.existsSync(MISC_FILE)) {
    console.log('✅ misc.css tidak ditemukan, sudah bersih.');
    process.exit(0);
}

let miscContent = fs.readFileSync(MISC_FILE, 'utf8');

// Ekstrak blok CSS (selector + deklarasi) dan @media
const blocks = [];
const regex = /(@media[^{]+\{)|([^{}]+\{)/g;
let match;
while ((match = regex.exec(miscContent)) !== null) {
    const startBrace = match.index + match[0].length - 1;
    let braceCount = 1;
    let endIndex = startBrace + 1;
    while (braceCount > 0 && endIndex < miscContent.length) {
        if (miscContent[endIndex] === '{') braceCount++;
        if (miscContent[endIndex] === '}') braceCount--;
        endIndex++;
    }
    blocks.push(miscContent.substring(match.index, endIndex).trim());
}

for (const block of blocks) {
    if (block.startsWith('@media')) {
        appendToFile(path.join(TARGET_DIR, 'base', 'responsive.css'), block);
        console.log('✔ Pindahkan @media → base/responsive.css');
        continue;
    }

    const selectorMatch = block.match(/^([^{]+)\{/);
    if (!selectorMatch) continue;
    const selector = selectorMatch[1].trim();
    const target = getTarget(selector);

    if (target) {
        const filePath = path.join(TARGET_DIR, target.folder, target.file);
        if (target.warn) {
            appendToFile(filePath, `/* TODO: Periksa apakah aturan berikut perlu media query */\n${block}`);
        } else {
            appendToFile(filePath, block);
        }
        console.log(`✔ ${selector} → ${target.folder}/${target.file}`);
    } else {
        // Fallback
        appendToFile(path.join(TARGET_DIR, '_uncategorized_orphan.css'), block);
        console.log(`⚠ ${selector} tidak dikenal → _uncategorized_orphan.css`);
    }
}

// Hapus misc.css dan folder _uncategorized
fs.unlinkSync(MISC_FILE);
try { fs.rmdirSync(path.dirname(MISC_FILE)); } catch(e) {}
console.log('🗑 Folder _uncategorized dan misc.css dihapus.');
console.log('✅ Selesai. Silakan periksa file yang diperbarui, terutama aturan dengan TODO.');