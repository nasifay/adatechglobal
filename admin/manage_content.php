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
        <?php
        $forms = include __DIR__ . '/forms.php';
        $base = $forms['content'] ?? [];
        // prepare image options with data-path
        $imgRows = $pdo->query("SELECT id, type, filename, path FROM images ORDER BY uploaded_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $opts = [];
        foreach ($imgRows as $ir) {
            $val = $ir['filename'];
            $label = $ir['filename'] . ' (' . $ir['type'] . ')';
            $rel = !empty($ir['path']) ? ltrim($ir['path'], '/\\') : 'assets/img/' . $ir['type'] . '/' . $ir['filename'];
            $p = $basePath . '/' . $rel;
            $opts[$val] = ['value' => $val, 'label' => $label, 'path' => $p];
        }
        // attach picker attrs and ids so JS editItem interacts with these elements
        foreach ($base as &$f) {
            if (!empty($f['name']) && $f['name'] === 'type') { $f['attrs'] = ['id' => 'content-type']; }
            if (!empty($f['name']) && $f['name'] === 'title') { $f['attrs'] = ['id' => 'content-title']; }
            if (!empty($f['name']) && $f['name'] === 'body') { $f['attrs'] = ['id' => 'content-body']; }
            if (!empty($f['name']) && $f['name'] === 'image') { $f['type'] = 'picker'; $f['attrs'] = ['id' => 'content-image']; $f['options'] = $opts; }
        }
        unset($f);
        // add id hidden as a field so it has an element id for JS
        array_unshift($base, ['type' => 'hidden', 'name' => 'id', 'value' => '', 'attrs' => ['id' => 'content-id']]);
        $hidden = [];
        $form_action = '';
        $fields = $base;
        $submit_label = 'Save';
        include __DIR__ . '/partials/admin_form.php';
        ?>
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

  <!-- Content page scripts moved to admin/admin.js -->

<?php require_once __DIR__ . '/partials/footer.php'; ?>
