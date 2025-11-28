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
        <form action="manage_projects.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
            <input type="hidden" name="form" value="add">
            <div><label>Title<br><input type="text" name="title" required></label></div>
            <div><label>Description<br><textarea name="description" rows="4"></textarea></label></div>
            <div><label>Image<br><input type="file" name="image" accept="image/*"></label></div>
            <div class="d-flex align-items-center" style="gap:8px">
                <label>Existing image: </label>
                <select id="project_image_select" name="existing_image">
                    <option value="">-- none --</option>
                    <?php foreach ($projImages as $img): ?>
                        <option value="<?php echo esc($img['filename']); ?>"><?php echo esc($img['type'] . '/' . $img['filename']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker('project_image_select', 'project-image-preview')">Pick</button>
                <div id="project-image-preview" class="picker-preview"></div>
            </div>
            <div><button type="submit">Add Project</button></div>
        </form>

        <h3>Existing Projects</h3>
        <table>
            <thead><tr><th>ID</th><th>Title</th><th>Description</th><th>Image</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($projects as $p): ?>
                <tr>
                    <td><?php echo (int)$p['id']; ?></td>
                    <td><?php echo esc($p['title']); ?></td>
                    <td><?php echo esc(mb_strimwidth($p['description'], 0, 80, '...')); ?></td>
                    <td><?php if ($p['image']): ?><img src="<?php echo esc(asset('assets/img/projects/' . $p['image'])); ?>" style="height:40px"><?php endif; ?></td>
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
            <form action="manage_projects.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                <input type="hidden" name="form" value="edit">
                <input type="hidden" name="id" value="<?php echo (int)$project['id']; ?>">
                <div><label>Title<br><input type="text" name="title" value="<?php echo esc($project['title']); ?>" required></label></div>
                <div><label>Description<br><textarea name="description" rows="4"><?php echo esc($project['description']); ?></textarea></label></div>
                <div>Current Image: <?php if ($project['image']): ?><img src="<?php echo esc(asset('assets/img/projects/' . $project['image'])); ?>" style="height:40px"><?php else: ?>None<?php endif; ?></div>
                <div><label>Replace Image<br><input type="file" name="image" accept="image/*"></label></div>
                <div><label>Or choose existing image<br>
                    <select name="existing_image">
                        <option value="">-- none --</option>
                        <?php foreach ($projImages as $img): ?>
                            <option value="<?php echo esc($img['filename']); ?>"><?php echo esc($img['type'] . '/' . $img['filename']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label></div>
                <div><button type="submit">Save Changes</button> <a href="manage_projects.php">Cancel</a></div>
            </form>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
