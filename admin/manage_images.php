<?php
// Manage images (logo, team, projects, etc.)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Images - Adatech Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Images</h2>
        <ul>
            <li><a href="upload_logo.php">Upload Logo</a></li>
            <li><a href="upload_team.php">Upload Team Images</a></li>
            <li><a href="upload_projects.php">Upload Project Images</a></li>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
