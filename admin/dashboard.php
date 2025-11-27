<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/partials/header.php';
?>
    <div class="section-header">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Admin'); ?></strong>. Use the links below to manage your website content.</p>
        <div style="margin-top:8px">
            <a href="preflight.php" class="btn btn-warning">Run Preflight</a>
        </div>
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
        <div class="col-lg-3 col-md-6">
            <div class="card-item admin-card">
                <div class="card-body text-center">
                    <i class="bi bi-chat-left-text" style="font-size:2rem;"></i>
                    <h5 class="card-title mt-2">Feedback</h5>
                    <a href="manage_feedback.php" class="btn btn-primary btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
