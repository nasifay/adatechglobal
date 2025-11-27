<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Basic admin page to manage 'content' entries
$message = '';
$schema_warnings = [];

// helper: check whether a column exists
function column_exists($pdo, $table, $column) {
  try {
    $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$db, str_replace('`','', $table), $column]);
    return (bool)$stmt->fetchColumn();
  } catch (Exception $e) {
    return false;
  }
}

// ensure 'image' column exists in content table or try to create it
$imageColumnExists = column_exists($pdo, 'content', 'image');
if (!$imageColumnExists) {
  try {
    $pdo->exec("ALTER TABLE content ADD COLUMN image VARCHAR(255) DEFAULT NULL");
    $imageColumnExists = true;
    $schema_warnings[] = 'Created missing column `content.image`.';
  } catch (Exception $e) {
    $err = $e->getMessage();
    if (stripos($err, 'Duplicate column name') !== false || stripos($err, 'SQLSTATE[42S21]') !== false) {
      // re-check via information_schema
      if (column_exists($pdo, 'content', 'image')) {
        $imageColumnExists = true;
        // no warning needed, column exists
      } else {
        $schema_warnings[] = 'content.image creation reported duplicate but column presence could not be verified.';
        $imageColumnExists = false;
      }
    } else {
      $schema_warnings[] = 'Missing column `content.image` (automatic ALTER failed). Use the Preflight page (admin/preflight.php) to attempt a safe fix, or import admin/all_in_one.sql manually.';
      $imageColumnExists = false;
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $type = $_POST['type'] ?? '';
    $title = $_POST['title'] ?? '';
    $body = $_POST['body'] ?? '';
  $image = $_POST['image'] ?? null;

    if ($id) {
      if ($imageColumnExists) {
          $stmt = $pdo->prepare('UPDATE content SET type = ?, title = ?, body = ?, image = ? WHERE id = ?');
          $stmt->execute([$type, $title, $body, $image, $id]);
      } else {
          $stmt = $pdo->prepare('UPDATE content SET type = ?, title = ?, body = ? WHERE id = ?');
          $stmt->execute([$type, $title, $body, $id]);
      }
      $message = 'Content updated.';
    } else {
      if ($imageColumnExists) {
          $stmt = $pdo->prepare('INSERT INTO content (type, title, body, image) VALUES (?, ?, ?, ?)');
          $stmt->execute([$type, $title, $body, $image]);
      } else {
          $stmt = $pdo->prepare('INSERT INTO content (type, title, body) VALUES (?, ?, ?)');
          $stmt->execute([$type, $title, $body]);
      }
      $message = 'Content created.';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $delId = (int)$_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM content WHERE id = ?');
    $stmt->execute([$delId]);
    $message = 'Content deleted.';
}

$items = $pdo->query('SELECT * FROM content ORDER BY updated_at DESC')->fetchAll();
// ensure items have 'image' key for compatibility with UI
foreach ($items as &$it) {
  if (!array_key_exists('image', $it)) $it['image'] = '';
}
unset($it);

?>
<?php $pageTitle = 'Manage Content'; require_once __DIR__ . '/partials/header.php'; ?>
  <div class="container">
    <h1>Manage Content</h1>
    <?php if (!empty($schema_warnings)): ?>
      <?php foreach($schema_warnings as $w): ?>
        <div class="alert alert-warning"><?php echo htmlspecialchars($w); ?></div>
      <?php endforeach; ?>
      <div class="alert alert-info">You can attempt an automatic fix using the <a href="preflight.php">Preflight</a> page which will create missing columns safely, or import <code>admin/all_in_one.sql</code> manually.</div>
    <?php endif; ?>
    <?php if ($message): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-6">
        <h3>Create / Edit</h3>
        <form method="post">
          <input type="hidden" name="id" id="content-id">
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-control" name="type" id="content-type" required>
              <option value="landing">landing</option>
              <option value="about">about</option>
              <option value="contact">contact</option>
              <option value="other">other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="title" id="content-title">
          </div>
          <div class="mb-3">
            <label class="form-label">Body (HTML allowed)</label>
            <textarea class="form-control" name="body" id="content-body" rows="8"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Featured Image (pick existing)</label>
            <select class="form-control" name="image" id="content-image">
              <option value="">-- none --</option>
              <?php
              $imgRows = $pdo->query("SELECT id, type, filename, path FROM images ORDER BY uploaded_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
              $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
              foreach ($imgRows as $ir) {
                $val = htmlspecialchars($ir['filename']);
                $rel = !empty($ir['path']) ? ltrim($ir['path'], '/\\') : 'assets/img/' . $ir['type'] . '/' . $ir['filename'];
                $p = $base . '/' . $rel;
                echo '<option data-path="' . htmlspecialchars($p) . '" value="' . $val . '">' . $val . ' (' . htmlspecialchars($ir['type']) . ')</option>';
              }
              ?>
            </select>
            <div style="margin-top:8px;"><img id="content-image-preview" src="" style="max-width:200px;display:none"></div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>
      </div>

      <div class="col-md-6">
        <h3>Existing Content</h3>
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>Type</th><th>Title</th><th>Featured Image</th><th>Updated</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?php echo $it['id']; ?></td>
                <td><?php echo htmlspecialchars($it['type']); ?></td>
                <td><?php echo htmlspecialchars($it['title']); ?></td>
                <td>
                    <?php if (!empty($it['image'])):
                    $imgPath = '';
                    try {
                      $r = $pdo->prepare("SELECT path, type, filename FROM images WHERE filename = ? LIMIT 1");
                      $r->execute([$it['image']]);
                      $row = $r->fetch(PDO::FETCH_ASSOC);
                      if ($row && !empty($row['path'])) $rel = ltrim($row['path'], '/\\');
                      elseif ($row) $rel = 'assets/img/' . $row['type'] . '/' . $row['filename'];
                      else $rel = ltrim($it['image'], '/\\');
                    } catch (Exception $e) { $rel = ltrim($it['image'], '/\\'); }
                    $filePath = __DIR__ . '/../' . $rel;
                    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                    $url = $base . '/' . $rel;
                    if ($rel && is_file($filePath)) { echo '<img src="' . htmlspecialchars($url) . '" style="max-height:60px">'; } else { echo htmlspecialchars($it['image']); }
                  else: echo '(none)'; endif; ?>
                </td>
                <td><?php echo $it['updated_at']; ?></td>
                <td>
                  <a class="btn btn-sm btn-secondary" href="#" onclick="editItem(<?php echo $it['id']; ?>, '<?php echo addslashes($it['type']); ?>', '<?php echo addslashes($it['title']); ?>', `<?php echo addslashes($it['body']); ?>`, '<?php echo addslashes($it['image'] ?? ''); ?>'); return false;">Edit</a>
                  <a class="btn btn-sm btn-danger" href="?action=delete&id=<?php echo $it['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function editItem(id, type, title, body, image) {
      document.getElementById('content-id').value = id;
      document.getElementById('content-type').value = type;
      document.getElementById('content-title').value = title;
      document.getElementById('content-body').value = body;
      if (image) {
        document.getElementById('content-image').value = image;
        var opt = document.getElementById('content-image').selectedOptions[0];
        if (opt && opt.dataset && opt.dataset.path) {
          var img = document.getElementById('content-image-preview'); img.src = opt.dataset.path; img.style.display = 'block';
        }
      } else {
        document.getElementById('content-image').value = '';
        document.getElementById('content-image-preview').style.display = 'none';
      }
      window.scrollTo(0,0);
    }
    document.getElementById('content-image').addEventListener('change', function(e){
      var opt = this.selectedOptions[0];
      var img = document.getElementById('content-image-preview');
      if (opt && opt.dataset && opt.dataset.path) { img.src = opt.dataset.path; img.style.display = 'block'; } else { img.style.display = 'none'; }
    });
  </script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
