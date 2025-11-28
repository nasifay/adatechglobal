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
<?php
// Fetch quick stats
$counts = [];
$tables = [
    'content' => 'content',
    'projects' => 'projects',
    'services' => 'services',
    'team' => 'team',
    'images' => 'images',
    'feedback' => 'feedback'
];
foreach ($tables as $k => $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM `" . $t . "`");
        $row = $stmt->fetch();
        $counts[$k] = $row ? (int)$row['c'] : 0;
    } catch (Exception $e) {
        $counts[$k] = 0;
    }
}
// Recent items
$recentProjects = [];
try {
    $stmt = $pdo->query('SELECT id,title,created_at FROM projects ORDER BY id DESC LIMIT 5');
    $recentProjects = $stmt->fetchAll();
} catch (Exception $e) { $recentProjects = []; }
?>

    <div class="admin-welcome">
        <h2>Admin Dashboard</h2>
        <p class="admin-small">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Admin'); ?></strong>. Quick links and recent activity are shown below.</p>
        <div class="admin-actions">
            <a href="preflight.php" class="btn btn-warning">Run Preflight</a>
            <a href="manage_content.php" class="btn btn-outline-primary">Manage Content</a>
            <a href="manage_projects.php" class="btn btn-outline-primary">Manage Projects</a>
            <a href="manage_services.php" class="btn btn-outline-primary">Manage Services</a>
        </div>
    </div>

    <div class="admin-stats">
        <div class="admin-stat">
            <div class="count"><?php echo (int)$counts['content']; ?></div>
            <div class="label">Content Pages</div>
            <div style="margin-top:8px"><a href="manage_content.php" class="btn btn-sm btn-primary">Open</a></div>
        </div>
        <div class="admin-stat">
            <div class="count"><?php echo (int)$counts['projects']; ?></div>
            <div class="label">Projects</div>
            <div style="margin-top:8px"><a href="manage_projects.php" class="btn btn-sm btn-primary">Open</a></div>
        </div>
        <div class="admin-stat">
            <div class="count"><?php echo (int)$counts['services']; ?></div>
            <div class="label">Services</div>
            <div style="margin-top:8px"><a href="manage_services.php" class="btn btn-sm btn-primary">Open</a></div>
        </div>
        <div class="admin-stat">
            <div class="count"><?php echo (int)$counts['team']; ?></div>
            <div class="label">Team Members</div>
            <div style="margin-top:8px"><a href="manage_team.php" class="btn btn-sm btn-primary">Open</a></div>
        </div>
        <div class="admin-stat">
            <div class="count"><?php echo (int)$counts['images']; ?></div>
            <div class="label">Images</div>
            <div style="margin-top:8px"><a href="manage_images.php" class="btn btn-sm btn-primary">Open</a></div>
        </div>
        <div class="admin-stat">
            <div class="count"><?php echo (int)$counts['feedback']; ?></div>
            <div class="label">Feedback Messages</div>
            <div style="margin-top:8px"><a href="manage_feedback.php" class="btn btn-sm btn-primary">Open</a></div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card-item admin-card">
                <div class="card-body">
                    <h5 class="card-title">Recent Projects</h5>
                    <div class="admin-recent">
                        <?php if (empty($recentProjects)): ?>
                            <div class="recent-item">No recent projects</div>
                        <?php else: ?>
                            <?php foreach ($recentProjects as $rp): ?>
                                <div class="recent-item">
                                    <div>
                                        <strong><?php echo esc($rp['title']); ?></strong><br>
                                        <small class="admin-small">#<?php echo (int)$rp['id']; ?> &middot; <?php echo esc($rp['created_at'] ?? ''); ?></small>
                                    </div>
                                    <div><a class="btn btn-sm btn-outline-primary" href="manage_projects.php?action=edit&id=<?php echo (int)$rp['id']; ?>">Edit</a></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-item admin-card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <p class="admin-small">Use the links below for common admin tasks.</p>
                    <div class="d-flex flex-column" style="gap:8px">
                        <a class="btn btn-outline-secondary" href="manage_images.php">Manage Site Images</a>
                        <a class="btn btn-outline-secondary" href="manage_users.php">Manage Users</a>
                        <a class="btn btn-outline-secondary" href="manage_feedback.php">Review Feedback</a>
                        <a class="btn btn-outline-secondary" href="preflight.php">Run Preflight</a>
                    </div>
                </div>
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
