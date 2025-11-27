<?php
// Database configuration - update these values if your MySQL uses different credentials
// Prefer environment variables when available; fall back to these defaults.
$default_db_host = getenv('DB_HOST') ?: '127.0.0.1';
$default_db_name = getenv('DB_NAME') ?: 'adatech_cms';
$default_db_user = getenv('DB_USER') ?: 'root';
$default_db_pass = getenv('DB_PASS') ?: '';

if (!defined('DB_HOST')) {
    define('DB_HOST', $default_db_host);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $default_db_name);
}
if (!defined('DB_USER')) {
    define('DB_USER', $default_db_user);
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $default_db_pass);
}

// Upload settings
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../assets/img/uploads/');
}
if (!defined('UPLOAD_MAX_SIZE')) {
    define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024); // 2 MB
}
if (!defined('UPLOAD_ALLOWED')) {
    define('UPLOAD_ALLOWED', ['jpg','jpeg','png','gif']);
}

// Ensure upload dir exists
if (!file_exists(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

?>
<?php
// includes/config.php
// Central configuration for the site
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'adatech_cms',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name' => 'Adatech Solutions',
    ],
    // Initial admin credentials (will be migrated to a hashed file on first successful login)
    'admin' => [
        'username' => 'nasifay',
        'password' => '1234'
    ],
];
