<?php
// scripts/split_services_sections.php
// Create a 'services_main' content row from stored services page HTML (content.type='services')

$cfg = include __DIR__ . '/../includes/config.php';
$db = $cfg['db'] ?? null;
if (!$db) { echo "No DB config\n"; exit(1); }

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
try { $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); }
catch (Exception $e) { echo $e->getMessage(); exit(2); }

$stmt = $pdo->prepare('SELECT body FROM content WHERE type = :t LIMIT 1');
$stmt->execute([':t' => 'services']);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) { echo "No services content found\n"; exit(0); }
$html = $r['body'];

$dom = new DOMDocument(); libxml_use_internal_errors(true); $dom->loadHTML('<?xml encoding="utf-8"?>'.$html); libxml_clear_errors();
$bodyNodes = $dom->getElementsByTagName('body');
$bodyHtml = '';
if ($bodyNodes->length) {
    $b = $bodyNodes->item(0);
    foreach ($b->childNodes as $c) $bodyHtml .= $dom->saveHTML($c);
} else { $bodyHtml = $html; }

$check = $pdo->prepare('SELECT id FROM content WHERE type = :type LIMIT 1');
$check->execute([':type' => 'services_main']);
$found = $check->fetch(PDO::FETCH_ASSOC);
if ($found) {
    $u = $pdo->prepare('UPDATE content SET title = :title, body = :body, updated_at = NOW() WHERE id = :id');
    $u->execute([':title' => 'Services Main', ':body' => $bodyHtml, ':id' => $found['id']]);
    echo "Updated services_main\n";
} else {
    $i = $pdo->prepare('INSERT INTO content (type,title,body,created_at,updated_at) VALUES (:t,:title,:body,NOW(),NOW())');
    $i->execute([':t' => 'services_main', ':title' => 'Services Main', ':body' => $bodyHtml]);
    echo "Created services_main\n";
}

exit(0);
