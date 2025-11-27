<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit; }
$pageTitle = 'Edit Contact';
include __DIR__ . '/partials/header.php';

$db = $pdo ?? null;
if (!$db) {
    echo '<div class="alert alert-danger">Database not available.</div>';
    include __DIR__ . '/partials/footer.php';
    exit;
}

// ensure csrf token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$errors = [];
// load existing contact content (type=contact)
$stmt = $db->prepare('SELECT * FROM content WHERE type = ? LIMIT 1');
$stmt->execute(['contact']);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $errors[] = 'Invalid request.';
    } else {
        $body = $_POST['body'] ?? '';
        $title = $_POST['title'] ?? 'Contact';
        if ($contact) {
            $u = $db->prepare('UPDATE content SET title=?, body=?, updated_at = NOW() WHERE id=?');
            $u->execute([$title, $body, $contact['id']]);
        } else {
            $i = $db->prepare('INSERT INTO content (type,title,body,created_at) VALUES (?,?,?,NOW())');
            $i->execute(['contact',$title,$body]);
        }
        header('Location: manage_contact.php'); exit;
    }
}

?>
<h2>Edit Contact Page</h2>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>', array_map('htmlspecialchars',$errors)); ?></div><?php endif; ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <div class="mb-2">
    <label>Title</label>
    <input class="form-control" name="title" value="<?php echo htmlspecialchars($contact['title'] ?? 'Contact'); ?>">
  </div>
  <div class="mb-2">
    <label>Content (HTML allowed)</label>
    <textarea class="form-control" name="body" rows="10"><?php echo htmlspecialchars($contact['body'] ?? '<div class="info-item">\n  <h3>Our Address</h3>\n  <p>Addis Ababa, Ethiopia</p>\n</div>'); ?></textarea>
  </div>
  <div><button class="btn btn-primary">Save</button> <a class="btn btn-secondary" href="manage_contact.php">Cancel</a></div>
</form>

<?php include __DIR__ . '/partials/footer.php'; ?>
