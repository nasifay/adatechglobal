<?php
if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
?>
<aside class="admin-sidebar">
  <h4 style="margin-top:0">Admin Menu</h4>
  <ul>
    <li><a href="dashboard.php" class="<?php echo ($_SERVER['PHP_SELF'] ?? '') === '/admin/dashboard.php' ? 'active':''; ?>">Dashboard</a></li>
    <li><a href="manage_content.php">Content</a></li>
    <li><a href="manage_projects.php">Projects</a></li>
    <li><a href="manage_services.php">Services</a></li>
    <li><a href="manage_team.php">Team</a></li>
    <li><a href="manage_posts.php">Blog Posts</a></li>
    <li><a href="manage_images.php">Images</a></li>
    <li><a href="manage_feedback.php">Feedback</a></li>
    <li><a href="manage_users.php">Users</a></li>
    <li><a href="manage_site_images.php">Site Images</a></li>
  </ul>
</aside>
