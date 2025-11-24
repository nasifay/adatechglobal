<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Adatech Admin Dashboard</title>
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header d-flex align-items-center">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
            <a href="dashboard.php" class="logo d-flex align-items-center">
                <h1>Adatech Solutions <span style="font-size:18px;color:#0d6efd;">Admin</span></h1>
            </a>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="manage_content.php">Content</a></li>
                    <li><a href="manage_team.php">Team</a></li>
                    <li><a href="manage_testimonials.php">Testimonials</a></li>
                    <li><a href="manage_services.php">Services</a></li>
                    <li><a href="manage_projects.php">Projects</a></li>
                    <li><a href="manage_images.php">Images</a></li>
                    <li><a href="manage_contact.php">Contact</a></li>
                    <li><a href="logout.php" style="color:#d9534f;">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <!-- End Header -->

    <main id="main" style="margin-top: 80px;">
        <div class="container" data-aos="fade-up">
            <div class="section-header">
                <h2>Admin Dashboard</h2>
                <p>Welcome, <strong>nasifay</strong>. Use the links below to manage your website content.</p>
            </div>
            <div class="row gy-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-text" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Content</h5>
                            <a href="manage_content.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-people" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Team</h5>
                            <a href="manage_team.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-quote" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Testimonials</h5>
                            <a href="manage_testimonials.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-gear" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Services</h5>
                            <a href="manage_services.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-kanban" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Projects</h5>
                            <a href="manage_projects.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-images" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Images</h5>
                            <a href="manage_images.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-item admin-card">
                        <div class="card-body text-center">
                            <i class="bi bi-envelope" style="font-size:2rem;"></i>
                            <h5 class="card-title mt-2">Contact Info</h5>
                            <a href="manage_contact.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer id="footer" class="footer mt-5">
        <div class="footer-content position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="footer-info">
                            <h3>Adatech Solutions Admin</h3>
                            <p>
                                Addis Ababa, Ethiopia<br>
                                <strong>Website:</strong> <a href="https://www.adatechglobal.com">www.adatechglobal.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-legal text-center position-relative">
            <div class="container">
                <div class="copyright">
                    &copy; Copyright <strong><span>Adatech Solutions</span></strong>. All Rights Reserved
                </div>
            </div>
        </div>
    </footer>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
