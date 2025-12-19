<?php
// scripts/fix_schema.php
// Safely add missing columns required by seed scripts using INFORMATION_SCHEMA checks

// Load DB config
$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) {
    echo "Database config not found in includes/config.php\n";
    exit(1);
}

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "PDO connect failed: " . $e->getMessage() . "\n";
    exit(2);
}

$schema = $db['name'];

$required = [
    'content' => [
        "slug VARCHAR(255) DEFAULT NULL",
        "meta_title VARCHAR(255) DEFAULT NULL",
        "meta_description VARCHAR(255) DEFAULT NULL",
        "status ENUM('published','draft') DEFAULT 'published'",
        "created_at TIMESTAMP NULL DEFAULT NULL",
    ],
    'images' => [
        "path VARCHAR(255) DEFAULT NULL",
        "alt VARCHAR(255) DEFAULT NULL",
        "caption TEXT DEFAULT NULL",
        "folder VARCHAR(128) DEFAULT NULL",
        "width INT DEFAULT NULL",
        "height INT DEFAULT NULL",
        "filesize BIGINT DEFAULT NULL",
        "registered TINYINT(1) DEFAULT 1",
        "uploaded_by VARCHAR(128) DEFAULT NULL",
    ],
    'team' => [
        "image_alt VARCHAR(255) DEFAULT NULL",
    ],
    'testimonials' => [
        "author VARCHAR(128) DEFAULT NULL",
        "company VARCHAR(128) DEFAULT NULL",
        "quote TEXT DEFAULT NULL",
        "visible TINYINT(1) DEFAULT 1",
    ],
    'projects' => [
        "excerpt TEXT DEFAULT NULL",
        "image_alt VARCHAR(255) DEFAULT NULL",
        "category VARCHAR(128) DEFAULT NULL",
        "created_at TIMESTAMP NULL DEFAULT NULL",
    ],
    'services' => [
        "excerpt TEXT DEFAULT NULL",
        "image VARCHAR(255) DEFAULT NULL",
        "image_alt VARCHAR(255) DEFAULT NULL",
        "created_at TIMESTAMP NULL DEFAULT NULL",
    ],
    'posts' => [
        "status ENUM('published','draft') DEFAULT 'published'",
        "published_at TIMESTAMP NULL DEFAULT NULL",
    ],
];

foreach ($required as $table => $columns) {
    // fetch existing columns
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table");
    $stmt->execute([':schema' => $schema, ':table' => $table]);
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $toAdd = [];
    foreach ($columns as $colDef) {
        // column name is first token
        $name = preg_split('/\s+/', trim($colDef))[0];
        if (!in_array($name, $existing)) {
            $toAdd[] = $colDef;
        }
    }
    if (count($toAdd) > 0) {
        foreach ($toAdd as $colDef) {
            try {
                $sql = sprintf('ALTER TABLE `%s` ADD COLUMN %s', $table, $colDef);
                $pdo->exec($sql);
                echo "Added column to $table: $colDef\n";
            } catch (Exception $e) {
                echo "Failed to add column to $table: $colDef => " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "Table $table already has required columns.\n";
    }
}

echo "Schema check/patch finished.\n";
