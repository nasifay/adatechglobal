<?php
// Manage services
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services - Adatech Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Services</h2>
        <ul>
            <li><a href="add_service.php">Add Service</a></li>
            <li><a href="edit_services.php">Edit Services</a></li>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
