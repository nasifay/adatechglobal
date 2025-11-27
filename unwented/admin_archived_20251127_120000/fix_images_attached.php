<?php
// Localhost-only helper to add images.attached_type and images.attached_id safely.
// USAGE: open this file in your browser on the machine running XAMPP, then delete it.

// Restrict to localhost
$allowed = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, $allowed, true)) {
    http_response_code(403);
    echo 'Forbidden: run this script from localhost only.';
    exit;
}

require_once __DIR__ . '/../includes/db.php';

if (!isset($pdo) || !$pdo) {
    echo '<p>Database connection not available. Check includes/db.php</p>';
    exit;
}

function col_exists_info($pdo, $db, $table, $col) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$db, $table, $col]);
    return (bool)$stmt->fetchColumn();
}

try {
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
} catch (Exception $e) {
    echo '<p>Failed to detect database name: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

$cols = [
    'attached_type' => "ALTER TABLE images ADD COLUMN attached_type VARCHAR(32) DEFAULT NULL",
    'attached_id' => "ALTER TABLE images ADD COLUMN attached_id INT DEFAULT NULL",
];

echo '<h2>Fix images.attached columns</h2>';
foreach ($cols as $col => $alter) {
    try {
        if (col_exists_info($pdo, $dbName, 'images', $col)) {
            echo '<div style="color:green">Column <strong>' . htmlspecialchars($col) . '</strong> already exists.</div>';
            continue;
        }
        $pdo->exec($alter);
        // verify
        if (col_exists_info($pdo, $dbName, 'images', $col)) {
            echo '<div style="color:green">Added column <strong>' . htmlspecialchars($col) . '</strong>.</div>';
        } else {
            echo '<div style="color:orange">Attempted to add <strong>' . htmlspecialchars($col) . '</strong> but verification failed.</div>';
        }
    } catch (Exception $e) {
        // Report error but continue
        echo '<div style="color:red">Error adding <strong>' . htmlspecialchars($col) . '</strong>: ' . htmlspecialchars($e->getMessage()) . '</div>';
        // Re-check whether column exists despite the error
        if (col_exists_info($pdo, $dbName, 'images', $col)) {
            echo '<div style="color:green">Column <strong>' . htmlspecialchars($col) . '</strong> exists (verified after error).</div>';
        }
    }
}

echo '<p><a href="preflight.php">Back to Preflight</a> | <a href="dashboard.php">Back to Dashboard</a></p>';
echo '<p><strong>Security:</strong> delete this file after use:</p>';
echo '<pre>Remove-Item "d:\\Xampp\\htdocs\\UpConstruction-1.0.0\\admin\\fix_images_attached.php"</pre>';

?>