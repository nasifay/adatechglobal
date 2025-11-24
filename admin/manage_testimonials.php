<?php
// Manage testimonials
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials - Adatech Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Testimonials</h2>
        <ul>
            <li><a href="add_testimonial.php">Add Testimonial</a></li>
            <li><a href="edit_testimonials.php">Edit Testimonials</a></li>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
