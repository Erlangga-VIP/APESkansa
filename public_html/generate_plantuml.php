<?php
/**
 * generate_plantuml.php
 *
 * Simple web interface that lets you paste PlantUML source code
 * and renders the diagram using the public PlantUML server.
 *
 * Simpan file ini di: c:/xampp/htdocs/APEskansa/public_html/generate_plantuml.php
 */

// Helper to encode PlantUML text (see https://plantuml.com/en/code-encoding)
function plantUmlEncode(string $text): string {
    $deflated = gzdeflate($text, 9);
    $base64   = base64_encode($deflated);
    // PlantUML custom alphabet (URL‑safe, no padding)
    $trans = ['+' => '-', '/' => '_', '=' => ''];
    return strtr(rtrim($base64, '='), $trans);
}

$source   = '';
$imageUrl = '';
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = trim($_POST['plantuml'] ?? '');
    if ($source === '') {
        $error = 'Masukkan kode PlantUML di atas.';
    } else {
        // Ensure the source starts with @startuml and ends with @enduml
        if (strpos($source, '@startuml') === false) {
            $source = "@startuml\n" . $source;
        }
        if (strpos($source, '@enduml') === false) {
            $source = $source . "\n@enduml";
        }
        $encoded   = plantUmlEncode($source);
        $imageUrl = "https://www.plantuml.com/plantuml/svg/" . $encoded;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate PlantUML Diagram</title>
    <style>
        body {font-family: 'Inter', sans-serif; background:#f5f7fa; margin:0; padding:2rem;}
        .container {max-width:800px; margin:auto;}
        textarea {width:100%; height:200px; font-family: monospace; font-size:0.95rem; padding:0.8rem; border:1px solid #ccc; border-radius:4px;}
        button {margin-top:0.5rem; padding:0.6rem 1.2rem; background:#4a90e2; color:#fff; border:none; border-radius:4px; cursor:pointer;}
        .error {color:#d93025; margin-top:0.5rem;}
        img {margin-top:1.5rem; max-width:100%; border:2px solid #4a90e2; border-radius:8px;}
    </style>
</head>
<body>
<div class="container">
    <h1>Generate PlantUML Diagram</h1>
    <form method="post">
        <label for="plantuml">PlantUML source code:</label><br>
        <textarea id="plantuml" name="plantuml" placeholder="Enter PlantUML code here..."><?= htmlspecialchars($source, ENT_QUOTES, 'UTF-8') ?></textarea><br>
        <button type="submit">Render Diagram</button>
    </form>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($imageUrl): ?>
        <h2>Result:</h2>
        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="PlantUML Diagram"/>
    <?php endif; ?>
</div>
</body>
</html>
