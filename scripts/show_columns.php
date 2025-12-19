<?php
// scripts/show_columns.php
// Usage: php show_columns.php table_name
if ($argc < 2) {
    echo "Usage: php show_columns.php table_name\n";
    exit(1);
}
$table = $argv[1];
$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config\n"; exit(2); }
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
$pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->prepare("SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table ORDER BY ORDINAL_POSITION");
$stmt->execute([':schema' => $db['name'], ':table' => $table]);
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$cols) { echo "Table not found or has no columns: $table\n"; exit(3); }
foreach ($cols as $c) {
    echo $c['COLUMN_NAME'] . "\t" . $c['COLUMN_TYPE'] . "\t" . $c['IS_NULLABLE'] . "\t" . ($c['COLUMN_DEFAULT'] ?? 'NULL') . "\n";
}
