<?php
// partials/header.php - single, clean header partial
// Ensure helpers are available when this header is included
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/site_images.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Adatech Solutions | Software, Embedded Systems &amp; IoT — Ethiopia</title>
  <meta content="We deliver innovative software, embedded systems, and digital solutions that empower businesses, communities, and innovators to thrive in the digital era." name="description">
  <meta content="Adatech, Ada Technology, software development Ethiopia, IoT Ethiopia, smart devices, embedded systems, website design Ethiopia, mobile apps" name="keywords">
  <link href="<?php echo site_image('favicon'); ?>" rel="icon">
  <link href="<?php echo site_image('apple_touch_icon'); ?>" rel="apple-touch-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
  <link href="<?php echo asset('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo asset('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
  <link href="<?php echo asset('assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo asset('assets/vendor/aos/aos.css'); ?>" rel="stylesheet">
  <link href="<?php echo asset('assets/vendor/glightbox/css/glightbox.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo asset('assets/vendor/swiper/swiper-bundle.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo asset('assets/css/main.css'); ?>" rel="stylesheet">
</head>
<body>
<header id="header" class="header d-flex align-items-center">
  <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
    <a href="index.php" class="logo d-flex align-items-center">
      <h1>Adatech Solutions</h1>
    </a>
    <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
    <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
    <nav id="navbar" class="navbar">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About us</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="projects.php">Projects</a></li>
        <li><a href="team.php">Team</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </nav>
  </div>
</header>
