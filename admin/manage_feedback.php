<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
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
  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <input type="hidden" name="action" value="add">
    <div class="mb-2"><input class="form-control" name="name" placeholder="Name" required></div>
    <div class="mb-2"><input class="form-control" name="email" placeholder="Email (optional)"></div>
    <div class="mb-2"><input class="form-control" name="company" placeholder="Company (optional)"></div>
    <div class="mb-2"><textarea class="form-control" name="message" placeholder="Message" rows="4" required></textarea></div>
    <div class="mb-2">
      <label>Image (pick existing)</label>
      <select class="form-control" name="image" id="feedback-image-select">
        <option value="">-- none --</option>
        <?php
          $imgRows = $db->query("SELECT id, type, filename, path FROM images ORDER BY uploaded_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
          foreach ($imgRows as $ir) {
            $val = htmlspecialchars($ir['filename']);
            if (!empty($ir['path'])) {
              $p = esc(asset(ltrim($ir['path'], '/\\')));
            } else {
              $p = esc(asset('assets/img/' . $ir['type'] . '/' . $ir['filename']));
            }
            echo '<option data-path="' . htmlspecialchars($p, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" value="' . $val . '">' . $val . ' (' . htmlspecialchars($ir['type']) . ')</option>';
          }
        ?>
      </select>
      <div style="margin-top:8px;"><img id="feedback-image-preview" src="" style="max-width:200px;display:none"></div>
    </div>
    <label><input type="checkbox" name="visible" checked> Visible</label>
    <div style="margin-top:8px"><button class="btn btn-success">Save</button> <button type="button" class="btn btn-secondary" onclick="document.getElementById('addForm').style.display='none'">Cancel</button></div>
  </form>
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
      <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $it['id']; ?>">
        <div class="mb-2"><input class="form-control" name="name" value="<?php echo htmlspecialchars($it['name']); ?>" required></div>
        <div class="mb-2"><input class="form-control" name="email" value="<?php echo htmlspecialchars($it['email']); ?>"></div>
        <div class="mb-2"><input class="form-control" name="company" value="<?php echo htmlspecialchars($it['company']); ?>"></div>
        <div class="mb-2"><textarea class="form-control" name="message" rows="4" required><?php echo htmlspecialchars($it['message']); ?></textarea></div>
        <div class="mb-2">
          <label>Image (pick existing)</label>
          <select class="form-control" name="image" id="feedback-image-select-<?php echo $it['id']; ?>">
            <option value="">-- none --</option>
            <?php
              foreach ($imgRows as $ir) {
                $val = htmlspecialchars($ir['filename']);
                if (!empty($ir['path'])) {
                  $p = esc(asset(ltrim($ir['path'], '/\\')));
                } else {
                  $p = esc(asset('assets/img/' . $ir['type'] . '/' . $ir['filename']));
                }
                $sel = ($it['image'] && $it['image'] === $ir['filename']) ? ' selected' : '';
                echo '<option data-path="' . htmlspecialchars($p) . '" value="' . $val . '"' . $sel . '>' . $val . ' (' . htmlspecialchars($ir['type']) . ')</option>';
              }
            ?>
          </select>
          <div style="margin-top:8px;"><img id="feedback-image-preview-<?php echo $it['id']; ?>" src="" style="max-width:200px;display:<?php echo $it['image'] ? 'block' : 'none'; ?>"></div>
        </div>
        <label><input type="checkbox" name="visible" <?php echo $it['visible'] ? 'checked' : ''; ?>> Visible</label>
        <div style="margin-top:8px"><button class="btn btn-success">Save</button> <button type="button" class="btn btn-secondary" onclick="document.getElementById('edit-row-<?php echo $it['id']; ?>').style.display='none'">Cancel</button></div>
      </form>
    </td></tr>
  <?php endforeach; ?>
  </tbody>
</table>

<script>
function showEdit(id){
  var el = document.getElementById('edit-row-'+id);
  if(el.style.display==='none') el.style.display='table-row'; else el.style.display='none';
}
// image preview handlers for feedback select
(function(){
  var sel = document.getElementById('feedback-image-select');
  if (sel) {
    sel.addEventListener('change', function(){
      var opt = this.selectedOptions[0];
      var img = document.getElementById('feedback-image-preview');
      if (opt && opt.dataset && opt.dataset.path) { img.src = opt.dataset.path; img.style.display = 'block'; } else { img.style.display = 'none'; }
    });
  }
  // per-row preview hookup
  <?php foreach ($items as $it): ?>
    (function(){
      var s = document.getElementById('feedback-image-select-<?php echo $it['id']; ?>');
      var p = document.getElementById('feedback-image-preview-<?php echo $it['id']; ?>');
      if (!s || !p) return;
      s.addEventListener('change', function(){
        var o = this.selectedOptions[0]; if (o && o.dataset && o.dataset.path) { p.src = o.dataset.path; p.style.display = 'block'; } else { p.style.display = 'none'; }
      });
      // set initial preview if selected
      try { var init = s.selectedOptions[0]; if (init && init.dataset && init.dataset.path) { p.src = init.dataset.path; } } catch(e){}
    })();
  <?php endforeach; ?>
})();
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
