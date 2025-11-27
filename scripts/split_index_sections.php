<?php
// scripts/split_index_sections.php
// Parse existing `content` type=index and create section-level content rows (index_hero, index_services, index_projects, index_testimonials)

$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config\n"; exit(1); }

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "PDO connect failed: " . $e->getMessage() . "\n";
    exit(2);
}

$stmt = $pdo->prepare('SELECT body FROM content WHERE type = :type LIMIT 1');
$stmt->execute([':type' => 'index']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { echo "No content row with type=index found\n"; exit(0); }
$html = $row['body'];

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="utf-8"?>' . $html);
libxml_clear_errors();

$x = new DOMXPath($dom);

$sections = [
    'index_hero' => "//section[@id='hero']",
    'index_solutions' => "//section[@id='constructions' or @id='solutions' or contains(@class,'hero') ]",
    'index_services' => "//section[@id='services']",
    'index_projects' => "//section[@id='projects']",
    'index_testimonials' => "//section[@id='testimonials']",
];

$created = 0; $skipped = 0;
foreach ($sections as $key => $xpath) {
    $nodes = $x->query($xpath);
    if ($nodes && $nodes->length) {
        $node = $nodes->item(0);
        $inner = '';
        foreach ($node->childNodes as $c) { $inner .= $dom->saveHTML($c); }
        // upsert into content table
        $check = $pdo->prepare('SELECT id FROM content WHERE type = :type LIMIT 1');
        $check->execute([':type' => $key]);
        $found = $check->fetch(PDO::FETCH_ASSOC);
        if ($found) {
            $u = $pdo->prepare('UPDATE content SET title = :title, body = :body, updated_at = NOW() WHERE id = :id');
            $u->execute([':title' => ucfirst(str_replace('_',' ',$key)), ':body' => $inner, ':id' => $found['id']]);
            $skipped++;
        } else {
            $i = $pdo->prepare('INSERT INTO content (type, title, body, created_at, updated_at) VALUES (:type, :title, :body, NOW(), NOW())');
            $i->execute([':type' => $key, ':title' => ucfirst(str_replace('_',' ',$key)), ':body' => $inner]);
            $created++;
        }
    }
}

echo "Split complete: created=$created, updated/skipped=$skipped\n";

exit(0);
