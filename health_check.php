<?php
// Simple DB health check for Adatech project
// Access: http://localhost/UpConstruction-1.0.0/health_check.php

require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/html; charset=utf-8');

echo '<h2>Adatech DB Health Check</h2>';

try {
    $pdo->query('SELECT 1');
    echo '<p style="color:green">Database: connected</p>';
} catch (Exception $e) {
    echo '<p style="color:red">Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

$tables = ['content','images','feedback','services','projects','team','testimonials','posts'];
echo '<table border="1" cellpadding="6" style="border-collapse:collapse">';
echo '<tr><th>Table</th><th>Exists</th><th>Row count</th></tr>';
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '" . $t . "'");
        $exists = (bool)$stmt->fetch();
        $count = 'N/A';
        if ($exists) {
            $c = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
            $count = (int)$c;
        }
        echo '<tr>';
        echo '<td>' . htmlspecialchars($t) . '</td>';
        echo '<td>' . ($exists ? '<span style="color:green">Yes</span>' : '<span style="color:orange">No</span>') . '</td>';
        echo '<td>' . htmlspecialchars($count) . '</td>';
        echo '</tr>';
    } catch (Exception $e) {
        echo '<tr><td>' . htmlspecialchars($t) . '</td><td colspan="2">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    }
}
echo '</table>';

// Show DB connection details (friendly) - mask password
$config = [];
if (file_exists(__DIR__ . '/includes/config.local.php')) {
    $config = include __DIR__ . '/includes/config.local.php';
} elseif (file_exists(__DIR__ . '/includes/config.php')) {
    $config = include __DIR__ . '/includes/config.php';
}
$db = $config['db'] ?? [];
$host = $db['host'] ?? 'unknown';
$name = $db['name'] ?? 'unknown';
$user = $db['user'] ?? 'unknown';

echo '<h3>Connection Info</h3>';
echo '<ul>';
echo '<li>Host: ' . htmlspecialchars($host) . '</li>';
echo '<li>Database: ' . htmlspecialchars($name) . '</li>';
echo '<li>User: ' . htmlspecialchars($user) . '</li>';
echo '<li>Password: <em>hidden</em></li>';
echo '</ul>';

echo '<p><a href="admin/index.php">Open Admin Login</a> | <a href="/">Open site root</a></p>';

?>