<?php
// scripts/print_content_type.php
// Usage: php print_content_type.php index_hero
if ($argc < 2) {
    echo "Usage: php print_content_type.php <type>\n";
    exit(1);
}
$type = $argv[1];
$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config\n"; exit(2); }
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
$pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->prepare('SELECT title, body FROM content WHERE type = :type LIMIT 1');
$stmt->execute([':type' => $type]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { echo "No content for type=$type\n"; exit(0); }
echo "--- title: " . ($row['title'] ?? '') . " ---\n";
echo $row['body'] . "\n";
