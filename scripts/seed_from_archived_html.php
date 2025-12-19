<?php
// scripts/seed_from_archived_html.php
// Parse archived_html/*.html and assets/img/* to seed DB tables:
// - content (index/about/etc)
// - services
// - projects
// - team
// - images
// Non-destructive: inserts when title/type not found, otherwise updates.

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

libxml_use_internal_errors(true);

$archived = __DIR__ . '/../archived_html';
$assetsImg = __DIR__ . '/../assets/img';

$stats = [
    'content_inserted' => 0,
    'content_updated' => 0,
    'services_inserted' => 0,
    'services_updated' => 0,
    'projects_inserted' => 0,
    'projects_updated' => 0,
    'team_inserted' => 0,
    'team_updated' => 0,
    'images_registered' => 0,
    'images_skipped' => 0,
];

// Helper to upsert content by type
function upsert_content($pdo, $type, $title, $body)
{
    global $stats;
    $stmt = $pdo->prepare('SELECT id FROM content WHERE type = ? LIMIT 1');
    $stmt->execute([$type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $upd = $pdo->prepare('UPDATE content SET title = ?, body = ?, updated_at = NOW() WHERE id = ?');
        $res = $upd->execute([$title, $body, $row['id']]);
        if ($res) $stats['content_updated']++;
    } else {
        $ins = $pdo->prepare('INSERT INTO content (type, title, body, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $res = $ins->execute([$type, $title, $body]);
        if ($res) $stats['content_inserted']++;
    }
}

// Register image path into images table if not exists
function register_image($pdo, $path)
{
    global $stats;
    $path = trim($path);
    if ($path === '') return false;
    // Normalize: remove leading ./ or / if present
    $norm = ltrim($path, '/\\');

    // If file not exists relative to project root, skip
    $full = __DIR__ . '/../' . $norm;
    if (!file_exists($full)) {
        // try with assets/img prefix
        $full = __DIR__ . '/../' . ltrim($path, '/');
        if (!file_exists($full)) {
            $stats['images_skipped']++;
            return false;
        }
    }

    // Check existing by filename/path
    $stmt = $pdo->prepare('SELECT id FROM images WHERE filename = ? OR path = ? LIMIT 1');
    $filename = basename($norm);
    $stmt->execute([$filename, $norm]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) return true;

    // get image metadata
    $size = @getimagesize($full);
    $width = $size[0] ?? null;
    $height = $size[1] ?? null;
    $filesize = filesize($full);
    $folder = basename(dirname($norm));

    $ins = $pdo->prepare('INSERT INTO images (type, filename, path, folder, width, height, filesize, registered, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())');
    $type = $folder ?: 'image';
    $res = $ins->execute([$type, $filename, $norm, $folder, $width, $height, $filesize]);
    if ($res) {
        $stats['images_registered']++;
        return true;
    }
    return false;
}

// Parse content pages: index.html, about.html, contact.html, services.html, projects.html, team.html
$pages = glob($archived . '/*.html');
foreach ($pages as $page) {
    $name = pathinfo($page, PATHINFO_FILENAME);
    $html = file_get_contents($page);
    if ($html === false) continue;

    // load into DOM
    $dom = new DOMDocument();
    // hack to preserve utf-8
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
    $xpath = new DOMXPath($dom);

    // Extract <title>
    $title = null;
    $nodes = $xpath->query('//head/title');
    if ($nodes->length) $title = trim($nodes->item(0)->textContent);
    if (!$title) $title = ucfirst($name);

    // Extract main container: prefer <main id="main"> else <body>
    $mainNode = $xpath->query('//*[@id="main"]');
    if ($mainNode->length) {
        $bodyHtml = '';
        foreach ($mainNode->item(0)->childNodes as $child) {
            $bodyHtml .= $dom->saveHTML($child);
        }
    } else {
        $bodyNodes = $xpath->query('//body');
        $bodyHtml = $bodyNodes->length ? $dom->saveHTML($bodyNodes->item(0)) : $html;
    }

    upsert_content($pdo, $name, $title, $bodyHtml);

    // For specific pages, extract structured data
    if (in_array($name, ['services'])) {
        // find service items
        $items = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " service-item ")]');
        foreach ($items as $it) {
            $h3 = $xpath->query('.//h3', $it);
            $p = $xpath->query('.//p', $it);
            $titleS = $h3->length ? trim($h3->item(0)->textContent) : null;
            $desc = $p->length ? trim($p->item(0)->textContent) : null;
            if (!$titleS) continue;

            // upsert services by title
            $sstmt = $pdo->prepare('SELECT id FROM services WHERE title = ? LIMIT 1');
            $sstmt->execute([$titleS]);
            $srow = $sstmt->fetch(PDO::FETCH_ASSOC);
            if ($srow) {
                $u = $pdo->prepare('UPDATE services SET description = ?, updated_at = NOW() WHERE id = ?');
                $u->execute([$desc, $srow['id']]);
                $stats['services_updated']++;
            } else {
                $i = $pdo->prepare('INSERT INTO services (title, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
                $i->execute([$titleS, $desc]);
                $stats['services_inserted']++;
            }

            // register images inside the item
            $imgs = $xpath->query('.//img', $it);
            foreach ($imgs as $img) {
                $src = $img->getAttribute('src');
                register_image($pdo, $src);
            }
        }
    }

    if (in_array($name, ['projects'])) {
        $items = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " portfolio-item ")]');
        foreach ($items as $it) {
            $h4 = $xpath->query('.//h4', $it);
            $p = $xpath->query('.//p', $it);
            $img = $xpath->query('.//img', $it);
            $titleP = $h4->length ? trim($h4->item(0)->textContent) : null;
            $desc = $p->length ? trim($p->item(0)->textContent) : null;
            $imgsrc = $img->length ? $img->item(0)->getAttribute('src') : null;
            if (!$titleP) continue;

            $pstmt = $pdo->prepare('SELECT id FROM projects WHERE title = ? LIMIT 1');
            $pstmt->execute([$titleP]);
            $prow = $pstmt->fetch(PDO::FETCH_ASSOC);
            if ($prow) {
                $u = $pdo->prepare('UPDATE projects SET description = ?, image = ?, updated_at = NOW() WHERE id = ?');
                $u->execute([$desc, $imgsrc, $prow['id']]);
                $stats['projects_updated']++;
            } else {
                $i = $pdo->prepare('INSERT INTO projects (title, description, image, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                $i->execute([$titleP, $desc, $imgsrc]);
                $stats['projects_inserted']++;
            }
            if ($imgsrc) register_image($pdo, $imgsrc);
        }
    }

    if (in_array($name, ['team'])) {
        $items = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " member ")]');
        foreach ($items as $it) {
            $h4 = $xpath->query('.//h4', $it);
            $span = $xpath->query('.//span', $it);
            $p = $xpath->query('.//p', $it);
            $img = $xpath->query('.//img', $it);
            $nameT = $h4->length ? trim($h4->item(0)->textContent) : null;
            $role = $span->length ? trim($span->item(0)->textContent) : null;
            $bio = $p->length ? trim($p->item(0)->textContent) : null;
            $imgsrc = $img->length ? $img->item(0)->getAttribute('src') : null;
            if (!$nameT) continue;

            $tstmt = $pdo->prepare('SELECT id FROM team WHERE name = ? LIMIT 1');
            $tstmt->execute([$nameT]);
            $trow = $tstmt->fetch(PDO::FETCH_ASSOC);
            if ($trow) {
                $u = $pdo->prepare('UPDATE team SET role = ?, bio = ?, image = ?, updated_at = NOW() WHERE id = ?');
                $u->execute([$role, $bio, $imgsrc, $trow['id']]);
                $stats['team_updated']++;
            } else {
                $i = $pdo->prepare('INSERT INTO team (name, role, bio, image, updated_at) VALUES (?, ?, ?, ?, NOW())');
                $i->execute([$nameT, $role, $bio, $imgsrc]);
                $stats['team_inserted']++;
            }
            if ($imgsrc) register_image($pdo, $imgsrc);
        }
    }

    // extract testimonials if present
    if (in_array($name, ['index', 'about'])) {
        $items = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " testimonial-item ")]');
        foreach ($items as $it) {
            $h3 = $xpath->query('.//h3', $it);
            $h4 = $xpath->query('.//h4', $it);
            $p = $xpath->query('.//p', $it);
            $img = $xpath->query('.//img', $it);
            $author = $h3->length ? trim($h3->item(0)->textContent) : null;
            $company = $h4->length ? trim($h4->item(0)->textContent) : null;
            $quote = $p->length ? trim($p->item(0)->textContent) : null;
            $imgsrc = $img->length ? $img->item(0)->getAttribute('src') : null;
            if (!$quote) continue;

            $tstmt = $pdo->prepare('SELECT id FROM testimonials WHERE quote = ? LIMIT 1');
            $tstmt->execute([$quote]);
            $trow = $tstmt->fetch(PDO::FETCH_ASSOC);
            if ($trow) {
                $u = $pdo->prepare('UPDATE testimonials SET author = ?, company = ?, image = ?, visible = 1, updated_at = NOW() WHERE id = ?');
                $u->execute([$author, $company, $imgsrc, $trow['id']]);
            } else {
                $i = $pdo->prepare('INSERT INTO testimonials (author, company, quote, image, visible, updated_at) VALUES (?, ?, ?, ?, 1, NOW())');
                $i->execute([$author, $company, $quote, $imgsrc]);
            }
            if ($imgsrc) register_image($pdo, $imgsrc);
        }
    }

}

// Finally, scan assets/img recursively and register files
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($assetsImg));
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) continue;
    $rel = 'assets/img/' . str_replace('\\', '/', substr($file->getPathname(), strlen(__DIR__ . '/../assets/img/') ));
    // normalize to assets/img/filename or subfolder
    $relPath = 'assets/img/' . ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen(__DIR__ . '/../'))), '/');
    // try to create relative path like assets/img/..., but easier: get path relative to project root
    $projectRelative = substr($file->getPathname(), strlen(__DIR__ . '/../'));
    register_image($pdo, $projectRelative);
}

// Report
echo "Seeding complete:\n";
foreach ($stats as $k => $v) {
    echo " - $k: $v\n";
}

libxml_clear_errors();

?>