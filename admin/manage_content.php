<?php
// Manage landing page, about us, contact info
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Content - Adatech Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Content</h2>
        <ul>
            <li><a href="edit_landing.php">Edit Landing Page</a></li>
            <li><a href="edit_about.php">Edit About Us</a></li>
            <li><a href="edit_contact.php">Edit Contact Info</a></li>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
