<?php
// Use the site's shared header so admin pages share the same layout and assets.
if (!isset($pageTitle)) $pageTitle = 'Admin';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/site_images.php';
// Include the site's main header (prints head and top navigation)
include_once __DIR__ . '/../../partials/header.php';

if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
// Add small admin topbar and admin stylesheet when the site header is used
?>
<link rel="stylesheet" href="<?php echo asset('admin/admin.css'); ?>">
<?php // Render a subtle admin banner inside the site's container so admin keeps the site's UI/UX ?>
<div class="container">
  <div class="admin-banner-wrapper">
    <?php if (!empty($_SESSION['admin_logged_in'])): ?>
      <div class="admin-banner d-flex align-items-center">
        <h4 style="margin:0">Admin Panel</h4>
        <div class="ms-auto admin-user">Logged in as <strong><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Admin'); ?></strong>
          &nbsp; &middot; &nbsp; <a href="logout.php">Logout</a>
        </div>
      </div>
    <?php else: ?>
      <div class="admin-banner d-flex align-items-center">
        <h4 style="margin:0">Admin</h4>
        <div class="ms-auto"><a href="index.php">Login</a></div>
      </div>
    <?php endif; ?>
  </div>
</div>

<main class="container dashboard-container">
  <div class="admin-layout">
    <?php
    // include admin sidebar when available
    if (file_exists(__DIR__ . '/sidebar.php')) {
        include_once __DIR__ . '/sidebar.php';
    }
    ?>
