<?php
// Copy this file to `includes/config.local.php` and fill in your production/dev DB credentials.
// Do NOT commit `includes/config.local.php` to your git repository.

// Option A: define constants (some parts of the app use these)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'adatech');
define('DB_USER', 'nasifay');
define('DB_PASS', 'replace_with_password');

// Option B: return configuration array (other parts of the app expect this)
return [
    'db' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name' => 'Adatech Solutions',
    ],
];
