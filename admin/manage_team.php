<?php
// Manage team members
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Team - Adatech Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Team</h2>
        <ul>
            <li><a href="add_team.php">Add Team Member</a></li>
            <li><a href="edit_team.php">Edit Team Members</a></li>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
