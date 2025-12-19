<?php
// includes/auth.php
// Simple admin authentication helper.
// It migrates a plain password in includes/config.php to a hashed file includes/admin_user.php on first successful login.

function get_admin_user_file()
{
    return __DIR__ . '/admin_user.php';
}

function load_config_admin()
{
    $cfg = include __DIR__ . '/config.php';
    return $cfg['admin'] ?? null;
}

function load_stored_admin()
{
    $file = get_admin_user_file();
    if (!file_exists($file)) return null;
    return include $file;
}

function verify_admin_credentials($username, $password)
{
    $stored = load_stored_admin();
    if ($stored && isset($stored['username'], $stored['password_hash'])) {
        if ($username === $stored['username'] && password_verify($password, $stored['password_hash'])) {
            return true;
        }
    }

    // fallback: check config plain credentials (one-time migration)
    $cfg = load_config_admin();
    if ($cfg && isset($cfg['username'], $cfg['password'])) {
        if ($username === $cfg['username'] && hash_equals($cfg['password'], $password)) {
            // migrate to hashed file
            migrate_plain_admin_to_hashed($cfg['username'], $password);
            return true;
        }
    }
    return false;
}

function migrate_plain_admin_to_hashed($username, $password)
{
    $file = get_admin_user_file();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $content = "<?php\nreturn [\n    'username' => '" . addslashes($username) . "',\n    'password_hash' => '" . addslashes($hash) . "',\n];\n";
    file_put_contents($file, $content, LOCK_EX);
    @chmod($file, 0600);
}

function update_admin_password($username, $newPassword)
{
    $file = get_admin_user_file();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $content = "<?php\nreturn [\n    'username' => '" . addslashes($username) . "',\n    'password_hash' => '" . addslashes($hash) . "',\n];\n";
    file_put_contents($file, $content, LOCK_EX);
    @chmod($file, 0600);
    return true;
}
