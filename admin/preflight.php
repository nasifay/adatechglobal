<?php
// Admin preflight/migration status page
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit; }
$pageTitle = 'Preflight Checks';
include __DIR__ . '/partials/header.php';

$checks = [];
$pdo_ok = false;
try {
    $pdo->query('SELECT 1');
    $pdo_ok = true;
    $checks['db'] = ['ok' => true, 'msg' => 'Database connection OK'];
} catch (Exception $e) {
    $checks['db'] = ['ok' => false, 'msg' => 'DB connection failed: ' . $e->getMessage()];
}

// helper
function col_exists($pdo, $table, $col) {
    try {
        // Use information_schema which is more reliable across environments
        $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $stmt->execute([$db, str_replace('`','',$table), $col]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}

if ($pdo_ok) {
    // content.image
    if (col_exists($pdo, 'content', 'image')) {
        $checks['content.image'] = ['ok' => true, 'msg' => 'content.image exists'];
    } else {
        try {
            $pdo->exec("ALTER TABLE content ADD COLUMN image VARCHAR(255) DEFAULT NULL");
            $checks['content.image'] = ['ok' => true, 'msg' => 'content.image created'];
        } catch (Exception $e) {
                // If the error is a duplicate-column error, treat as OK (column already exists)
                $err = $e->getMessage();
                if (stripos($err, 'Duplicate column name') !== false || stripos($err, 'SQLSTATE[42S21]') !== false) {
                    // double-check via information schema
                    if (col_exists($pdo, 'content', 'image')) {
                        $checks['content.image'] = ['ok' => true, 'msg' => 'content.image exists (already present)'];
                    } else {
                        $checks['content.image'] = ['ok' => true, 'msg' => 'content.image creation reported duplicate but column presence could not be verified.'];
                    }
                } else {
                    $checks['content.image'] = ['ok' => false, 'msg' => 'Missing content.image; use the Preflight page to attempt a fix or import admin/all_in_one.sql. Error: ' . $e->getMessage()];
                }
        }
    }

    // images.attached_type / attached_id
    if (col_exists($pdo, 'images', 'attached_type') && col_exists($pdo, 'images', 'attached_id')) {
        $checks['images.attached'] = ['ok' => true, 'msg' => 'images.attached_type/attached_id exist'];
    } else {
        try {
            $pdo->exec("ALTER TABLE images ADD COLUMN attached_type VARCHAR(32) DEFAULT NULL, ADD COLUMN attached_id INT DEFAULT NULL");
            $checks['images.attached'] = ['ok' => true, 'msg' => 'images.attached_type/attached_id created'];
        } catch (Exception $e) {
                $err = $e->getMessage();
                if (stripos($err, 'Duplicate column name') !== false || stripos($err, 'SQLSTATE[42S21]') !== false) {
                    // Re-check individually
                    $haveType = col_exists($pdo, 'images', 'attached_type');
                    $haveId = col_exists($pdo, 'images', 'attached_id');
                    if ($haveType && $haveId) {
                        $checks['images.attached'] = ['ok' => true, 'msg' => 'images.attached_type/attached_id exist (already present)'];
                    } else {
                        $missing = [];
                        if (!$haveType) $missing[] = 'attached_type';
                        if (!$haveId) $missing[] = 'attached_id';
                        $checks['images.attached'] = ['ok' => false, 'msg' => 'Partial success: missing ' . implode(', ', $missing) . '. Error: ' . $e->getMessage()];
                    }
                } else {
                    $checks['images.attached'] = ['ok' => false, 'msg' => 'Missing images.attached_* columns; run migration or add manually. Error: ' . $e->getMessage()];
                }
        }
    }

    // feedback table
    try {
        $tbl = $pdo->query("SHOW TABLES LIKE 'feedback'")->fetch();
        if ($tbl) {
            $checks['feedback.table'] = ['ok' => true, 'msg' => 'feedback table exists'];
        } else {
            // attempt to create
            $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(128) NOT NULL,
                email VARCHAR(255) DEFAULT NULL,
                company VARCHAR(128) DEFAULT NULL,
                message TEXT NOT NULL,
                visible TINYINT(1) DEFAULT 1,
                image VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_feedback_visible ON feedback(visible)");
            $checks['feedback.table'] = ['ok' => true, 'msg' => 'feedback table created'];
        }
    } catch (Exception $e) {
        $checks['feedback.table'] = ['ok' => false, 'msg' => 'feedback table missing and creation failed: ' . $e->getMessage()];
    }
}

// upload dir
require_once __DIR__ . '/../includes/config.php';
$uploadDir = UPLOAD_DIR ?? (__DIR__ . '/../assets/img/uploads/');
if (is_dir($uploadDir) && is_writable($uploadDir)) {
    $checks['upload.dir'] = ['ok' => true, 'msg' => 'Upload dir exists and writable: ' . $uploadDir];
} else {
    $ok = false;
    try { @mkdir($uploadDir, 0755, true); $ok = is_writable($uploadDir); } catch (Exception $e) { $ok = false; }
    if ($ok) $checks['upload.dir'] = ['ok' => true, 'msg' => 'Upload dir created and writable: ' . $uploadDir]; else $checks['upload.dir'] = ['ok' => false, 'msg' => 'Upload dir missing or not writable: ' . $uploadDir];
}

?>
<div class="container">
  <h2>Preflight & Migration Status</h2>
  <p>Checks below attempt to ensure the database and upload directories are ready. If an automatic fix was applied it will be reported; if not, follow the suggested action.</p>
  <table class="table table-bordered">
    <thead><tr><th>Check</th><th>Status</th><th>Message / Action</th></tr></thead>
    <tbody>
    <?php foreach ($checks as $k => $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($k); ?></td>
        <td><?php echo $r['ok'] ? '<span style="color:green">OK</span>' : '<span style="color:orange">Issue</span>'; ?></td>
        <td><?php echo htmlspecialchars($r['msg']); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

        <div style="margin-top:12px">
        <a href="all_in_one.sql" class="btn btn-outline-secondary">Download all-in-one SQL</a>
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
