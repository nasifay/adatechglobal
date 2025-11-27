<?php
// scripts/import_archived_pages_full.php
// Upsert full archived HTML pages into `content` table as type=filename (without ext)

$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config in includes/config.php\n"; exit(1); }

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "PDO connect failed: " . $e->getMessage() . "\n";
    exit(2);
}

$dir = __DIR__ . '/../archived_html/';
if (!is_dir($dir)) { echo "archived_html not found\n"; exit(3); }

$files = glob($dir . '*.html');
if (!$files) { echo "No archived HTML files found\n"; exit(0); }

$inserted = 0; $updated = 0;
foreach ($files as $file) {
    $name = basename($file);
    $type = pathinfo($name, PATHINFO_FILENAME);
    $html = file_get_contents($file);
    if (empty($html)) continue;

    // parse title and body
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8"?>' . $html);
    libxml_clear_errors();
    $titleNodes = $dom->getElementsByTagName('title');
    $title = $titleNodes->length ? trim($titleNodes->item(0)->textContent) : $type;
    $body = '';
    $bodies = $dom->getElementsByTagName('body');
    if ($bodies->length) {
        $bodyNode = $bodies->item(0);
        // get innerHTML
        $inner = '';
        foreach ($bodyNode->childNodes as $child) {
            $inner .= $dom->saveHTML($child);
        }
        $body = $inner;
    } else {
        // fallback: use whole HTML
        $body = $html;
    }

    // upsert: try update first by type
    $stmt = $pdo->prepare('SELECT id FROM content WHERE type = :type LIMIT 1');
    $stmt->execute([':type' => $type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $u = $pdo->prepare('UPDATE content SET title = :title, body = :body, updated_at = NOW() WHERE id = :id');
        $u->execute([':title' => $title, ':body' => $body, ':id' => $row['id']]);
        $updated++;
    } else {
        $i = $pdo->prepare('INSERT INTO content (type, title, body, created_at, updated_at) VALUES (:type, :title, :body, NOW(), NOW())');
        $i->execute([':type' => $type, ':title' => $title, ':body' => $body]);
        $inserted++;
    }
}

echo "Imported archived HTML pages: inserted=$inserted, updated=$updated\n";

exit(0);
