<?php
// Temporary admin password reset script.
// USAGE: place on local dev only, open once in browser, then delete the file.
if (php_sapi_name() === 'cli') {
    echo "This script is for web use only.\n";
    exit;
}
// Restrict to local requests for safety
$allowed = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, $allowed, true)) {
    http_response_code(403);
    echo 'Forbidden: reset allowed from localhost only.';
    exit;
}
require_once __DIR__ . '/../includes/auth.php';
// Change these values before loading the script in your browser
$username = 'nasifay';
$newPassword = 'UpConstructAdmin!2025';

// Perform the update
try {
    $ok = update_admin_password($username, $newPassword);
    if ($ok) {
        echo '<h2>Admin password updated</h2>';
        echo '<p>Username: <strong>' . htmlspecialchars($username) . '</strong></p>';
        echo '<p>New password: <strong>' . htmlspecialchars($newPassword) . '</strong></p>';
        echo '<p>IMPORTANT: Delete this file now: <code>admin/reset_admin_password.php</code></p>';
    } else {
        echo '<p>Failed to update password. Check file permissions.</p>';
    }
} catch (Exception $e) {
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

?>
