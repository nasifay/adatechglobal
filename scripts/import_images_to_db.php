<?php
/**
 * Scan `assets/img/*` subfolders and register image filenames into `images` table.
 * Non-destructive: does not move or delete files. Avoid running on production database unless intended.
 */
require_once __DIR__ . '/../includes/db.php';

$base = __DIR__ . '/../assets/img';
$allowed = ['jpg','jpeg','png','gif'];

if (!is_dir($base)) {
    echo "assets/img not found\n";
    exit(1);
}

$inserted = 0;
$skipped = 0;

$scan = scandir($base);
foreach ($scan as $entry) {
    if ($entry === '.' || $entry === '..') continue;
    $full = $base . DIRECTORY_SEPARATOR . $entry;
    if (!is_dir($full)) continue; // only subfolders

    // skip uploads/thumbs
    if ($entry === 'uploads') continue;

    $files = scandir($full);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) continue;

        // check if exists
        $stmt = $pdo->prepare('SELECT id FROM images WHERE type = :type AND filename = :filename LIMIT 1');
        $stmt->execute([':type' => $entry, ':filename' => $f]);
        if ($stmt->fetch()) { $skipped++; continue; }

        $ins = $pdo->prepare('INSERT INTO images (type, filename) VALUES (:type, :filename)');
        $ins->execute([':type' => $entry, ':filename' => $f]);
        $inserted++;
        echo "Registered: $entry/$f\n";
    }
}

echo "Done. Inserted: $inserted, Skipped: $skipped\n";

?>
