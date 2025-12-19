<?php
// scripts/db_counts.php - show counts for core tables
$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config\n"; exit(1); }
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
$pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$tables = ['content','services','projects','team','testimonials','images','posts'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM `$t`");
        $c = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
        echo str_pad($t, 15) . ": $c\n";
    } catch (Exception $e) {
        echo str_pad($t, 15) . ": error - " . $e->getMessage() . "\n";
    }
}
