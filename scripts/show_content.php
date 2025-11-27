<?php
// scripts/show_content.php
$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config\n"; exit(1); }
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
$pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query('SELECT id,type,title,CHAR_LENGTH(body) as len, LEFT(body,200) as sample, created_at, updated_at FROM content ORDER BY id LIMIT 20');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) { echo "No content rows\n"; exit(0); }
foreach ($rows as $r) {
    echo "ID: {$r['id']} | type: {$r['type']} | title: {$r['title']} | len: {$r['len']} | created: {$r['created_at']} | updated: {$r['updated_at']}\n";
    echo "---- sample ----\n";
    echo strip_tags($r['sample']) . "\n\n";
}
exit(0);
