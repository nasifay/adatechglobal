<?php
if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];
?>
<!-- Signup Modal -->
<div id="signupModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:10000;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:18px;border-radius:6px;max-width:420px;width:100%;box-shadow:0 6px 20px rgba(0,0,0,.25);position:relative;">
    <button id="signupModalClose" style="position:absolute;top:8px;right:8px;border:0;background:transparent;font-size:18px;">&times;</button>
    <h3 style="margin-top:0">Create Admin Account</h3>
    <form method="post" action="index.php">
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
      <input type="hidden" name="action" value="signup">
      <div style="display:flex;flex-direction:column;gap:8px">
        <input type="text" name="username" placeholder="Choose username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirm" placeholder="Confirm password" required>
        <input type="text" name="creation_code" placeholder="Creation code (required)" required>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:6px">
          <button type="button" id="signupModalCancel" style="background:#ddd;padding:8px 12px;border-radius:4px;border:0">Cancel</button>
          <button type="submit" style="background:#111;color:#fff;padding:8px 12px;border-radius:4px;border:0">Create Admin</button>
        </div>
      </div>
    </form>
    <p style="font-size:12px;color:#666;margin-top:8px">Sign up allowed only if no admin account exists. You must provide the secret creation code.</p>
  </div>
</div>

<!-- Signup modal behavior moved to admin/admin.js -->
