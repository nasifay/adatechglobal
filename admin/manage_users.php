<?php
// admin/manage_users.php - change admin username/password
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || !hash_equals($_POST['csrf'], $csrf)) {
        die('Invalid CSRF token');
    }
    $username = trim($_POST['username'] ?? '');
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // verify current password
    if (!verify_admin_credentials($username, $current)) {
        $error = 'Current credentials are incorrect.';
    } elseif ($new === '') {
        $error = 'New password cannot be empty.';
    } elseif ($new !== $confirm) {
        $error = 'New password and confirmation do not match.';
    } else {
        // update stored admin
        update_admin_password($username, $new);
        $msg = 'Admin credentials updated.';
        // optionally update session user
        $_SESSION['admin_user'] = $username;
    }
}

$stored = load_stored_admin();
if (!$stored) {
    $cfg = load_config_admin();
    $stored_username = $cfg['username'] ?? '';
} else {
    $stored_username = $stored['username'];
}

$pageTitle = 'Admin User';
require_once __DIR__ . '/partials/header.php'; ?>
    <style>label{display:block;margin:8px 0}</style>
    <h2>Admin User</h2>
    <?php if ($msg): ?><div class="msg"><?php echo esc($msg); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
        <label>Username
            <input type="text" name="username" value="<?php echo esc($stored_username); ?>" required>
        </label>
        <label>Current Password
            <input type="password" name="current_password" required>
        </label>
        <label>New Password
            <input type="password" name="new_password" required>
        </label>
        <label>Confirm New Password
            <input type="password" name="confirm_password" required>
        </label>
        <div><button type="submit">Update Credentials</button> <a href="dashboard.php">Cancel</a></div>
    </form>

    <p>Note: After updating, the plaintext credentials in `includes/config.php` are no longer used; the hashed file `includes/admin_user.php` will be updated.</p>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
