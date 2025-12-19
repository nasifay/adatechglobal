<?php
require_once __DIR__ . '/../includes/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../feedback.php'); exit;
}
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? null);
$company = trim($_POST['company'] ?? null);
$message = trim($_POST['message'] ?? '');
if ($name === '' || $message === '') {
    $_SESSION['feedback_error'] = 'Name and message are required.';
    header('Location: ../feedback.php'); exit;
}
try {
    // ensure feedback table exists
    $tbl = $pdo->query("SHOW TABLES LIKE 'feedback'")->fetch();
    if (!$tbl) {
        // try create
        $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(128) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            company VARCHAR(128) DEFAULT NULL,
            message TEXT NOT NULL,
            visible TINYINT(1) DEFAULT 1,
            image VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    $stmt = $pdo->prepare('INSERT INTO feedback (name,email,company,message,visible,created_at) VALUES (?,?,?,?,1,NOW())');
    $stmt->execute([$name,$email,$company,$message]);
    $_SESSION['feedback_success'] = 'Thank you for your feedback.';
    header('Location: ../feedback.php'); exit;
} catch (Exception $e) {
    $_SESSION['feedback_error'] = 'Unable to save feedback. Please try later.';
    header('Location: ../feedback.php'); exit;
}
