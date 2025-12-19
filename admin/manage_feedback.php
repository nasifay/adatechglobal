<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php'); exit;
}
$pageTitle = 'Manage Feedback';
include __DIR__ . '/partials/header.php';
// includes/db.php provides PDO in $pdo
$db = $pdo ?? null;
if (!$db) {
  echo '<div class="alert alert-danger">Database unavailable.</div>';
  include __DIR__ . '/partials/footer.php';
  exit;
}

// ensure CSRF token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

// ensure feedback table exists (try to create if missing)
try {
  $t = $db->query("SHOW TABLES LIKE 'feedback'")->fetch();
  if (!$t) {
    $db->exec("CREATE TABLE IF NOT EXISTS feedback (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(128) NOT NULL,
      email VARCHAR(255) DEFAULT NULL,
      company VARCHAR(128) DEFAULT NULL,
      message TEXT NOT NULL,
      visible TINYINT(1) DEFAULT 1,
      image VARCHAR(255) DEFAULT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    // create index
    $db->exec("CREATE INDEX idx_feedback_visible ON feedback(visible)");
  }
} catch (Exception $e) {
  echo '<div class="alert alert-warning">Warning: feedback table is missing and automatic creation failed. Use the <a href="preflight.php">Preflight</a> page to attempt a safe fix, or import <code>admin/all_in_one.sql</code> manually.</div>';
}

// handle actions: add, edit, delete
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // basic CSRF check
    $csrf = $_POST['csrf'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
        $errors[] = 'Invalid request.';
    } else {
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $visible = isset($_POST['visible']) ? 1 : 0;
            $image = trim($_POST['image'] ?? '');
            if ($name === '' || $message === '') {
                $errors[] = 'Name and message are required.';
            } else {
                $stmt = $db->prepare('INSERT INTO feedback (name,email,company,message,visible,image) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$name,$email,$company,$message,$visible,$image]);
                header('Location: manage_feedback.php'); exit;
            }
        } elseif ($action === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $visible = isset($_POST['visible']) ? 1 : 0;
            $image = trim($_POST['image'] ?? '');
            if ($id <= 0 || $name === '' || $message === '') {
                $errors[] = 'Invalid data.';
            } else {
                $stmt = $db->prepare('UPDATE feedback SET name=?, email=?, company=?, message=?, visible=?, image=? WHERE id=?');
                $stmt->execute([$name,$email,$company,$message,$visible,$image,$id]);
                header('Location: manage_feedback.php'); exit;
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $db->prepare('DELETE FROM feedback WHERE id=?');
                $stmt->execute([$id]);
                header('Location: manage_feedback.php'); exit;
            }
        }
    }
}

// fetch list
$stmt = $db->query('SELECT * FROM feedback ORDER BY created_at DESC');
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// render page
?>
<div class="d-flex" style="justify-content:space-between;align-items:center;margin-bottom:12px">
  <h2>Feedback</h2>
  <button class="btn btn-primary" onclick="document.getElementById('addForm').style.display='block'">Add Feedback</button>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
<?php endif; ?>

<div id="addForm" style="display:none;margin-bottom:12px;border:1px solid #eee;padding:12px;border-radius:6px">
    <?php
    $forms = include __DIR__ . '/forms.php';
    $base = $forms['feedback'] ?? [];
    $imgRows = $db->query("SELECT id, type, filename, path FROM images ORDER BY uploaded_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
    $imgOptions = ['' => '-- none --'];
    foreach ($imgRows as $ir) {
      $imgOptions[$ir['filename']] = $ir['filename'] . ' (' . $ir['type'] . ')';
    }
    foreach ($base as &$f) {
      if (($f['name'] ?? '') === 'image') {
        $f['options'] = $imgOptions;
      }
    }
    unset($f);
    $form_action = '';
    $hidden = ['csrf' => $_SESSION['csrf_token'], 'action' => 'add'];
    $fields = $base;
    $submit_label = 'Save';
    include __DIR__ . '/partials/admin_form.php';
    ?>
</div>

<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Message</th><th>Visible</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($items as $it): ?>
    <tr>
      <td><?php echo $it['id']; ?></td>
      <td><?php echo htmlspecialchars($it['name']); ?><br><small><?php echo htmlspecialchars($it['company']); ?></small></td>
      <td><?php echo nl2br(htmlspecialchars(substr($it['message'],0,200))); ?></td>
      <td><?php echo $it['visible'] ? 'Yes' : 'No'; ?></td>
      <td>
        <button class="btn btn-sm btn-outline-primary" onclick="showEdit(<?php echo $it['id']; ?>)">Edit</button>
        <form method="post" style="display:inline" onsubmit="return confirm('Delete this feedback?');">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo $it['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
      </td>
    </tr>
    <tr id="edit-row-<?php echo $it['id']; ?>" style="display:none"><td colspan="5">
      <?php
      $forms = include __DIR__ . '/forms.php';
      $base = $forms['feedback'] ?? [];
      $imgOptions = ['' => '-- none --'];
      foreach ($imgRows as $ir) {
        $imgOptions[$ir['filename']] = $ir['filename'] . ' (' . $ir['type'] . ')';
      }
      foreach ($base as &$f) {
        if (($f['name'] ?? '') === 'image') {
          $f['options'] = $imgOptions;
        }
        if (!empty($it) && isset($f['name']) && isset($it[$f['name']])) {
          $f['value'] = $it[$f['name']];
        }
      }
      unset($f);
      $form_action = '';
      $hidden = ['csrf' => $_SESSION['csrf_token'], 'action' => 'edit', 'id' => $it['id']];
      $fields = $base;
      $submit_label = 'Save';
      include __DIR__ . '/partials/admin_form.php';
      ?>
    </td></tr>
  <?php endforeach; ?>
  </tbody>
</table>

<!-- Feedback page scripts moved to admin/admin.js -->

<?php include __DIR__ . '/partials/footer.php'; ?>
