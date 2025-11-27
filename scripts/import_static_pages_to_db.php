<?php
// scripts/import_static_pages_to_db.php
// Non-destructive importer: reads archived_html/*.html and upserts into `content` table
// Usage: php scripts/import_static_pages_to_db.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$dir = __DIR__ . '/../archived_html';
$files = glob($dir . '/*.html');
if (!$files) {
    echo "No HTML files found in archived_html/.\n";
    exit(0);
}

$inserted = 0;
$updated = 0;
$skipped = 0;

foreach ($files as $file) {
    $basename = basename($file);
    $name = pathinfo($basename, PATHINFO_FILENAME); // e.g., index, about
    $type = strtolower($name);

    $html = file_get_contents($file);
    if ($html === false) {
        echo "Failed to read $file\n";
        continue;
    }

    // Try to extract a <title> and main content body heuristically
    $title = null;
    if (preg_match('/<title>(.*?)<\/title>/si', $html, $m)) {
        $title = trim(html_entity_decode($m[1]));
    }

    // Extract body inner HTML
    $body = null;
    if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m)) {
        $body = trim($m[1]);
    }

    // Fallbacks
    if (!$title) $title = ucfirst($type);
    if (!$body) $body = $html;

    // Upsert into content table by `type`
    $stmt = $pdo->prepare('SELECT id FROM content WHERE type = ? LIMIT 1');
    $stmt->execute([$type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $upd = $pdo->prepare('UPDATE content SET title = ?, body = ?, updated_at = NOW() WHERE id = ?');
        $res = $upd->execute([$title, $body, $row['id']]);
        if ($res) $updated++;
        else echo "Failed to update content for $type\n";
    } else {
        $ins = $pdo->prepare('INSERT INTO content (type, title, body, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $res = $ins->execute([$type, $title, $body]);
        if ($res) $inserted++;
        else echo "Failed to insert content for $type\n";
    }
}

echo "Import complete. Inserted: $inserted, Updated: $updated\n";

?>