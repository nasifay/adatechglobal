<?php
// scripts/migrate_static_to_db.php
// Insert selected static content from backups into the `content` table

require_once __DIR__ . '/../includes/db.php';

// Items to insert: type, title, body
$items = [
    [
        'type' => 'landing',
        'title' => "We deliver innovative software, embedded systems, and digital solutions.",
        'body' => "We empower businesses, communities and innovators with web & mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market."
    ],
    [
        'type' => 'get_started',
        'title' => "Let’s build something innovative together.",
        'body' => "Contact Adatech for tailored software and hardware solutions. Tell us about your idea and we'll help turn it into a working product or platform."
    ],
    [
        'type' => 'solutions_intro',
        'title' => "Our Solutions",
        'body' => "ADA Technology Solutions builds software and hardware products: IoT systems, embedded controllers, web platforms and mobile apps designed for Ethiopian businesses and industry."
    ],
    [
        'type' => 'about',
        'title' => "About Adatech Solutions",
        'body' => ""
    ]
];

// Insert each item. We insert new rows so admin's manage_content.php (which shows latest by type)
// will display these values. If you prefer updating existing rows instead, run with --replace
$replace = in_array('--replace', $argv ?? []);

if ($replace) {
    echo "Running in replace mode: existing content rows for these types will be deleted first.\n";
}

try {
    $pdo->beginTransaction();

    $insertStmt = $pdo->prepare("INSERT INTO content (`type`, `title`, `body`) VALUES (:type, :title, :body)");

    foreach ($items as $it) {
        if ($replace) {
            $del = $pdo->prepare("DELETE FROM content WHERE `type` = :type");
            $del->execute([':type' => $it['type']]);
        }

        $insertStmt->execute([
            ':type' => $it['type'],
            ':title' => $it['title'],
            ':body' => $it['body']
        ]);

        echo "Inserted content[type={$it['type']}], title='" . substr($it['title'], 0, 40) . "...'\n";
    }

    $pdo->commit();
    echo "Migration completed. Visit admin/manage_content.php to review and edit the inserted entries.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Usage note printed on direct run
if (php_sapi_name() === 'cli') {
    echo "\nUsage: php scripts/migrate_static_to_db.php [--replace]\n";
}
