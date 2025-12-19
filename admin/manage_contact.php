<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Manage Contact Info';
require_once __DIR__ . '/partials/header.php';
?>
    <h2>Manage Contact Info</h2>
    <ul>
        <li><a href="edit_contact.php" class="btn btn-sm btn-outline-primary">Edit Contact Details</a></li>
    </ul>
    <p><a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a></p>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
