<?php
// scripts/run_sql.php
// Usage: php run_sql.php path/to/file.sql

if ($argc < 2) {
    echo "Usage: php run_sql.php path/to/file.sql\n";
    exit(1);
}

$sqlFile = $argv[1];
if (!file_exists($sqlFile)) {
    echo "SQL file not found: $sqlFile\n";
    exit(2);
}

// Load config (includes/config.php returns array at end)
$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) {
    echo "Database config not found in includes/config.php\n";
    exit(3);
}

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ]);
} catch (Exception $e) {
    echo "PDO connection failed: " . $e->getMessage() . "\n";
    exit(4);
}

$sql = file_get_contents($sqlFile);
$clean = preg_replace('/\/\*.*?\*\//s', "", $sql); // remove block comments
$clean = preg_replace('/--.*?\n/', "\n", $clean); // remove -- single-line comments
// Split statements safely by semicolon when followed by newline — simple splitter
$stmts = preg_split('/;\s*\n/', $clean);
$count = 0;
foreach ($stmts as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '' || stripos($stmt, 'DELIMITER') !== false) continue;
    try {
        $pdo->exec($stmt);
        $count++;
    } catch (Exception $ex) {
        echo "Error executing statement: " . $ex->getMessage() . "\n";
        echo "Statement: " . substr($stmt, 0, 200) . "...\n";
    }
}

echo "Executed approximately $count statements from $sqlFile\n";

exit(0);
