<?php
if (!isset($pageTitle)) $pageTitle = 'Admin';
// Ensure helpers are available for asset() when rendering admin header links
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/site_images.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link href="<?php echo site_image('favicon'); ?>" rel="icon">
  <link href="<?php echo site_image('apple_touch_icon'); ?>" rel="apple-touch-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo asset('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset('assets/vendor/fontawesome-free/css/all.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset('assets/css/main.css'); ?>">
  <link rel="stylesheet" href="admin.css">
  <style>body{padding:18px;background:#f8f9fa;font-family: 'Open Sans', sans-serif} .dashboard-container{max-width:1100px;margin:0 auto;background:#fff;padding:18px;border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,.06)}</style>
</head>
<body>
  <?php if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); } ?>
  <?php if (!empty($_SESSION['admin_logged_in'])): ?>
    <div style="position:fixed;top:0;left:0;right:0;background:#111;color:#fff;padding:8px 12px;z-index:9999;display:flex;justify-content:flex-end;align-items:center;gap:8px;font-size:14px;">
      <span style="margin-right:auto;font-weight:600">Admin</span>
      <a href="dashboard.php" style="color:#111;background:#fff;padding:6px 10px;border-radius:4px;text-decoration:none;font-weight:600;margin-right:8px">Dashboard</a>
      <a href="logout.php" style="color:#111;background:#fff;padding:6px 10px;border-radius:4px;text-decoration:none;font-weight:600">Logout</a>
    </div>
    <div style="height:44px"></div>
  <?php else: ?>
    <div style="position:fixed;top:0;left:0;right:0;background:#111;color:#fff;padding:8px 12px;z-index:9999;display:flex;justify-content:flex-end;align-items:center;gap:8px;font-size:14px;">
      <span style="margin-right:auto;font-weight:600">Admin</span>
      <a href="index.php" style="color:#111;background:#fff;padding:6px 10px;border-radius:4px;text-decoration:none;font-weight:600;margin-right:8px">Login</a>
      <button class="show-signup-btn" style="background:#0d6efd;color:#fff;padding:6px 10px;border-radius:4px;border:0;font-weight:600;">Sign Up</button>
    </div>
    <div style="height:44px"></div>
    <?php include __DIR__ . '/signup_modal.php'; ?>
  <?php endif; ?>
  <!-- Header/navigation removed per request; head assets remain loaded -->
  <main class="container dashboard-container">
