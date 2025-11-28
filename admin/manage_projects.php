<?php
// admin/manage_projects.php - CRUD projects
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/upload.php';

$action = $_GET['action'] ?? 'list';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || !hash_equals($_POST['csrf'], $csrf)) {
        die('Invalid CSRF token');
    }

    try {
        if ($_POST['form'] === 'add') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $imageName = null;
            if (!empty($_POST['existing_image'])) {
                $imageName = basename($_POST['existing_image']);
            } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // upload to assets/img/projects/ and store filename only
                $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/projects');
                $imageName = basename($uploaded);
            }
            $stmt = $pdo->prepare('INSERT INTO projects (title, description, image) VALUES (:title, :description, :image)');
            $stmt->execute([':title' => $title, ':description' => $description, ':image' => $imageName]);
            header('Location: manage_projects.php?msg=added');
            exit;
        }

        if ($_POST['form'] === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $imageName = null;
            if (!empty($_POST['existing_image'])) {
                $imageName = basename($_POST['existing_image']);
            } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/projects');
                $imageName = basename($uploaded);
            }
            if ($imageName) {
                $stmt = $pdo->prepare('UPDATE projects SET title = :title, description = :description, image = :image WHERE id = :id');
                $stmt->execute([':title' => $title, ':description' => $description, ':image' => $imageName, ':id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE projects SET title = :title, description = :description WHERE id = :id');
                $stmt->execute([':title' => $title, ':description' => $description, ':id' => $id]);
            }
            header('Location: manage_projects.php?msg=updated');
            exit;
        }

        if ($_POST['form'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            // optional: remove image file
            $stmt = $pdo->prepare('SELECT image FROM projects WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if ($row && !empty($row['image'])) {
                $file = __DIR__ . '/../assets/img/projects/' . $row['image'];
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            $stmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
            $stmt->execute([':id' => $id]);
            header('Location: manage_projects.php?msg=deleted');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $project = $stmt->fetch();
}

$stmt = $pdo->query('SELECT * FROM projects ORDER BY id DESC');
$projects = $stmt->fetchAll();

// fetch images relevant to projects
$projImagesStmt = $pdo->prepare("SELECT * FROM images WHERE type IN ('projects','uploads','other') ORDER BY uploaded_at DESC");
$projImagesStmt->execute();
$projImages = $projImagesStmt->fetchAll();

?>
<?php $pageTitle = 'Manage Projects'; require_once __DIR__ . '/partials/header.php'; ?>
    <style>table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px}</style>
<?php require_once __DIR__ . '/partials/image_picker.php'; ?>
        <h2>Manage Projects</h2>
        <?php if (!empty($error)): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>
        <?php if (!empty($_GET['msg'])): ?><div class="msg"><?php echo esc($_GET['msg']); ?></div><?php endif; ?>

        <h3>Add New Project</h3>
        <?php
        $forms = include __DIR__ . '/forms.php';
        $base = $forms['projects'] ?? [];
        // attach existing images as options for picker/select
        $opts = [];
        foreach ($projImages as $img) {
            $val = $img['filename'];
            $label = $img['type'] . '/' . $img['filename'];
            // compute preview path
            $path = !empty($img['path']) ? ltrim($img['path'], '/\\') : 'assets/img/' . $img['type'] . '/' . $img['filename'];
            $opts[$val] = ['value' => $val, 'label' => $label, 'path' => $path];
        }
        $base[] = ['type' => 'picker', 'name' => 'existing_image', 'label' => 'Or choose existing image', 'options' => $opts];
        $form_action = 'manage_projects.php';
        $hidden = ['csrf' => $csrf, 'form' => 'add'];
        $fields = $base;
        $submit_label = 'Add Project';
        include __DIR__ . '/partials/admin_form.php';
        ?>

        <h3>Existing Projects</h3>
        <table>
            <thead><tr><th>ID</th><th>Title</th><th>Description</th><th>Image</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($projects as $p): ?>
                <tr>
                    <td><?php echo (int)$p['id']; ?></td>
                    <td><?php echo esc($p['title']); ?></td>
                    <td><?php echo esc(mb_strimwidth($p['description'], 0, 80, '...')); ?></td>
                    <td><?php if ($p['image']): ?><img src="/assets/img/projects/<?php echo esc($p['image']); ?>" style="height:40px"><?php endif; ?></td>
                    <td>
                        <a href="manage_projects.php?action=edit&id=<?php echo (int)$p['id']; ?>">Edit</a>
                        <form action="manage_projects.php" method="post" style="display:inline" onsubmit="return confirm('Delete project?');">
                            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                            <input type="hidden" name="form" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($action === 'edit' && !empty($project)): ?>
            <h3>Edit Project #<?php echo (int)$project['id']; ?></h3>
            <?php
            $forms = include __DIR__ . '/forms.php';
            $base = $forms['projects'] ?? [];
            foreach ($base as &$f) {
                if (!empty($project) && isset($f['name']) && isset($project[$f['name']])) {
                    $f['value'] = $project[$f['name']];
                }
            }
            unset($f);
            // inject current image preview
            $preview = '<div>Current Image: ' . ($project['image'] ? '<img src="/assets/img/projects/' . esc($project['image']) . '" style="height:40px">' : 'None') . '</div>';
            array_splice($base, 2, 0, [['type' => 'html', 'html' => $preview]]);
            // attach existing images picker
            $opts = [];
            foreach ($projImages as $img) {
                $val = $img['filename'];
                $label = $img['type'] . '/' . $img['filename'];
                $path = !empty($img['path']) ? ltrim($img['path'], '/\\') : 'assets/img/' . $img['type'] . '/' . $img['filename'];
                $opts[$val] = ['value' => $val, 'label' => $label, 'path' => $path];
            }
            $base[] = ['type' => 'picker', 'name' => 'existing_image', 'label' => 'Or choose existing image', 'options' => $opts];
            $form_action = 'manage_projects.php';
            $hidden = ['csrf' => $csrf, 'form' => 'edit', 'id' => (int)$project['id']];
            $fields = $base;
            $submit_label = 'Save Changes';
            include __DIR__ . '/partials/admin_form.php';
            ?>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
