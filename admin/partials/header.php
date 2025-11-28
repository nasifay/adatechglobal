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
<?php if (!empty($_SESSION['admin_logged_in'])): ?>
  <div class="admin-topbar" style="background:#111;color:#fff;padding:8px 12px;display:flex;justify-content:flex-end;align-items:center;gap:8px;font-size:14px;">
    <span style="margin-right:auto;font-weight:600">Admin</span>
    <a href="dashboard.php" style="color:#111;background:#fff;padding:6px 10px;border-radius:4px;text-decoration:none;font-weight:600;margin-right:8px">Dashboard</a>
    <a href="logout.php" style="color:#111;background:#fff;padding:6px 10px;border-radius:4px;text-decoration:none;font-weight:600">Logout</a>
  </div>
<?php else: ?>
  <div class="admin-topbar" style="background:#111;color:#fff;padding:8px 12px;display:flex;justify-content:flex-end;align-items:center;gap:8px;font-size:14px;">
    <span style="margin-right:auto;font-weight:600">Admin</span>
    <a href="index.php" style="color:#111;background:#fff;padding:6px 10px;border-radius:4px;text-decoration:none;font-weight:600;margin-right:8px">Login</a>
  </div>
<?php endif; ?>

<main class="container dashboard-container">
  <div class="admin-layout">
    <?php
    // include admin sidebar when available
    if (file_exists(__DIR__ . '/sidebar.php')) {
        include_once __DIR__ . '/sidebar.php';
    }
    ?>
