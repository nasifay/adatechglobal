<?php
// Admin login + signup page
session_start();
require_once __DIR__ . '/../includes/auth.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// basic rate limiting: allow 5 attempts then 15min cooldown
if (empty($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['first_attempt_time'] = time();
}

$error = '';
$message = '';

// CSRF token for forms
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

// Helper: check whether an admin user already exists (either migrated file or config)
function admin_exists()
{
    $stored = load_stored_admin();
    if ($stored) return true;
    $cfg = load_config_admin();
    return !empty($cfg['username']) && !empty($cfg['password']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validate CSRF
    if (empty($_POST['csrf']) || !hash_equals($csrf, $_POST['csrf'])) {
        $error = 'Invalid request.';
    } else {
        $action = $_POST['action'] ?? 'login';
        if ($action === 'login') {
            // check cooldown
            if ($_SESSION['login_attempts'] >= 5 && (time() - ($_SESSION['first_attempt_time'] ?? 0)) < 900) {
                $error = 'Too many attempts. Try again later.';
            } else {
                $user = trim($_POST['username'] ?? '');
                $pass = $_POST['password'] ?? '';
                if (verify_admin_credentials($user, $pass)) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user'] = $user;
                    // reset attempts
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['first_attempt_time'] = time();
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                    if (empty($_SESSION['first_attempt_time'])) {
                        $_SESSION['first_attempt_time'] = time();
                    }
                    $error = 'Invalid credentials';
                }
            }
        } elseif ($action === 'signup') {
            // only allow signup if no admin exists
            if (admin_exists()) {
                $error = 'An admin account already exists. Please login.';
            } else {
                $user = trim($_POST['username'] ?? '');
                $pass = $_POST['password'] ?? '';
                $pass2 = $_POST['password_confirm'] ?? '';
                $creation_code = trim($_POST['creation_code'] ?? '');

                // require secret creation code format: bignadatech + digits (case-insensitive)
                if (!preg_match('/^bignadatech\d+$/i', $creation_code)) {
                    $error = 'Invalid creation code.';
                } elseif ($user === '' || $pass === '') {
                    $error = 'Username and password are required.';
                } elseif ($pass !== $pass2) {
                    $error = 'Passwords do not match.';
                } else {
                    // create admin user file
                    update_admin_password($user, $pass);
                    // log in the new admin
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user'] = $user;
                    header('Location: dashboard.php');
                    exit;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adatech Admin - Login / Sign Up</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="login-container">
        <h2>Adatech Admin</h2>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($message): ?><div class="msg"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

        <div class="login-forms" style="display:flex;gap:24px;flex-wrap:wrap;justify-content:center;">
            <div class="box" style="min-width:260px">
                <h3>Login</h3>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                    <input type="hidden" name="action" value="login">
                    <input type="text" name="username" placeholder="Username" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit">Login</button>
                </form>
            </div>

            <div class="box" style="min-width:260px">
                <h3>Sign Up</h3>
                <p style="color:#666">If no admin account exists you can create one. Click the button to open the secure signup dialog.</p>
                                <div style="margin-top:8px">
                                    <button class="show-signup-btn" style="background:#0d6efd;color:#fff;padding:8px 12px;border-radius:4px;border:0;font-weight:600">Sign Up</button>
                                </div>
            </div>
        </div>
    </div>
</body>
<?php
// include modal partial (shared with header) so the signup dialog is available on this page
if (file_exists(__DIR__ . '/partials/signup_modal.php')) {
    include __DIR__ . '/partials/signup_modal.php';
}
?>
</html>
