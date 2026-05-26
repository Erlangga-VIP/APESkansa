<?php
/**
 * use_case.php
 *
 * Menampilkan diagram Use‑Case APEskansa menggunakan PlantUML.
 *
 * Simpan file ini di: c:/xampp/htdocs/APEskansa/public_html/use_case.php
 */

// PlantUML source (text)
$plantumlSource = <<<'EOD'
@startuml
' Actors
actor Guest as "Pengunjung"
actor User as "Pengguna Terdaftar"
actor Seller as "Penjual"
actor Admin as "Admin"

' Use‑case definitions
Guest --> (Registrasi)
Guest --> (Login)
Guest --> (Melihat Produk)
Guest --> (Mencari Produk)
Guest --> (Melihat Detail Produk)
Guest --> (Membaca Testimoni)

User --> (Registrasi)
User --> (Login)
User --> (Melihat Produk)
User --> (Mencari Produk)
User --> (Melihat Detail Produk)
User --> (Membeli Produk)
User --> (Menulis Testimoni)

Seller --> (Mengelola Toko)
Seller --> (Mengelola Produk)
Seller --> (Melihat Penjualan)

Admin --> (Mengelola Akun)
Admin --> (Melihat Statistik)
@enduml
EOD;

/**
 * Encode PlantUML text to the format used by the public server.
 * https://plantuml.com/en/code-encoding
 */
function plantUmlEncode(string $text): string {
    // 1. Encode to UTF‑8 bytes
    $data = gzdeflate($text, 9);
    // 2. Encode to a modified base64 (URL safe) without padding
    $base64 = base64_encode($data);
    // 3. Replace characters for PlantUML's custom alphabet
    $trans = array(
        '+' => '-',
        '/' => '_',
        '=' => ''
    );
    return strtr(rtrim($base64, '='), $trans);
}

$encoded = plantUmlEncode($plantumlSource);
$imageUrl = "https://www.plantuml.com/plantuml/svg/" . $encoded;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Diagram Use‑Case APEskansa</title>
    <style>
        body {font-family: 'Inter', sans-serif; background:#f9fafb; margin:0; display:flex; justify-content:center; align-items:center; height:100vh;}
        .container {text-align:center;}
        img {max-width:100%; height:auto; border:2px solid #4a90e2; border-radius:8px;}
    </style>
</head>
<body>
<div class="container">
    <h1>Diagram Use‑Case APEskansa</h1>
    <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Use‑Case Diagram"/>
    <p>Generated lewat PlantUML public server.</p>
</div>
</body>
</html>
