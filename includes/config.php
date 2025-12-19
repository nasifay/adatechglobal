<?php
// includes/config.php
// Central config (returns array) and defines sane defaults used across the app.

// Define upload-related constants so code that expects constants will not fail.
if (!defined('UPLOAD_MAX_SIZE')) {
    // default 5 MB
    define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
}
if (!defined('UPLOAD_ALLOWED')) {
    define('UPLOAD_ALLOWED', ['jpg','jpeg','png','gif','webp','avif']);
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../assets/img/uploads/');
}

return [
    'db' => [
        'host' => 'localhost:3306',             // Your MySQL host with port
        'name' => 'adatecmu_adatech_cms',      // Your database name
        'user' => 'your_db_username',          // Replace with your cPanel DB user
        'pass' => 'your_db_password',          // Replace with your DB password
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name' => 'Adatech Solutions',
    ],
];
