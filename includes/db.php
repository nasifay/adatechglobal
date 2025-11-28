<?php
// includes/db.php
// Creates a PDO instance and places it in $pdo. Prefers `includes/config.local.php` if present.

// Prefer local config (not committed) for secrets
if (file_exists(__DIR__ . '/config.local.php')) {
    $config = include __DIR__ . '/config.local.php';
} elseif (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
} else {
    throw new RuntimeException('Missing includes/config.php or includes/config.local.php');
}

$db = $config['db'] ?? [];
$host = $db['host'] ?? '127.0.0.1';
$name = $db['name'] ?? 'adatech_cms';
$user = $db['user'] ?? 'root';
$pass = $db['pass'] ?? '';
$charset = $db['charset'] ?? 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Show a friendly message in development. In production, consider logging.
    if (php_sapi_name() === 'cli' || (getenv('APP_ENV') && getenv('APP_ENV') !== 'production')) {
        echo "Database connection error: " . htmlspecialchars($e->getMessage());
    }
    throw $e;
}

