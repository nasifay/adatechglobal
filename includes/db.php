<?php
// Simple PDO connection (singleton via $pdo)
require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    // In local development we can show a brief message; in production you may want to log this instead
    http_response_code(500);
    echo "Database connection error: " . htmlspecialchars($e->getMessage());
    exit;
}

?>
<?php
// includes/db.php
// Creates a PDO instance and places it in $pdo
if (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
} else {
    throw new RuntimeException('Missing includes/config.php');
}

$db = $config['db'];
$host = $db['host'];
$name = $db['name'];
$user = $db['user'];
$pass = $db['pass'];
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
    // For production, consider logging instead of exposing errors
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
