<?php
// admin/manage_services.php - CRUD services
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/upload.php';

$action = $_GET['action'] ?? 'list';

// CSRF token simple implementation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || !hash_equals($_POST['csrf'], $csrf)) {
        die('Invalid CSRF token');
    }

    try {
            if ($_POST['form'] === 'add') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $imagePath = null;
                // prefer existing selected image
                if (!empty($_POST['existing_image'])) {
                    $parts = explode('/', $_POST['existing_image'], 2);
                    $imagePath = 'assets/img/' . $_POST['existing_image'];
                } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $imagePath = handle_image_upload('image');
                }
            $stmt = $pdo->prepare('INSERT INTO services (title, description, icon) VALUES (:title, :description, :icon)');
            $stmt->execute([':title' => $title, ':description' => $description, ':icon' => $imagePath]);
            header('Location: manage_services.php?msg=added');
            exit;
        }

        if ($_POST['form'] === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            // handle optional new image
            $imagePath = null;
            if (!empty($_POST['existing_image'])) {
                $imagePath = 'assets/img/' . $_POST['existing_image'];
            } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $imagePath = handle_image_upload('image');
            }
            if ($imagePath) {
                $stmt = $pdo->prepare('UPDATE services SET title = :title, description = :description, icon = :icon WHERE id = :id');
                $stmt->execute([':title' => $title, ':description' => $description, ':icon' => $imagePath, ':id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE services SET title = :title, description = :description WHERE id = :id');
                $stmt->execute([':title' => $title, ':description' => $description, ':id' => $id]);
            }
            header('Location: manage_services.php?msg=updated');
            exit;
        }

        if ($_POST['form'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('DELETE FROM services WHERE id = :id');
            $stmt->execute([':id' => $id]);
            header('Location: manage_services.php?msg=deleted');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch for list or edit
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM services WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $service = $stmt->fetch();
}

$stmt = $pdo->query('SELECT * FROM services ORDER BY id DESC');
$services = $stmt->fetchAll();

// fetch registered images for selection
$images = $pdo->query('SELECT * FROM images ORDER BY uploaded_at DESC')->fetchAll();

?>
<?php $pageTitle = 'Manage Services'; require_once __DIR__ . '/partials/header.php'; ?>
    <style>table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px}</style>
<?php require_once __DIR__ . '/partials/image_picker.php'; ?>
        <h2>Manage Services</h2>
        <?php if (!empty($error)): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>
        <?php if (!empty($_GET['msg'])): ?><div class="msg"><?php echo esc($_GET['msg']); ?></div><?php endif; ?>

        <h3>Add New Service</h3>
        <form action="manage_services.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
            <input type="hidden" name="form" value="add">
            <div><label>Title<br><input type="text" name="title" required></label></div>
            <div><label>Description<br><textarea name="description" rows="4"></textarea></label></div>
            <div><label>Icon/Image<br><input type="file" name="image" accept="image/*"></label></div>
                <div class="d-flex align-items-center" style="gap:8px">
                    <label>Existing image: </label>
                    <select id="existing_image_select" name="existing_image">
                        <option value="">-- none --</option>
                        <?php foreach ($images as $img): ?>
                            <option value="<?php echo esc($img['type'] . '/' . $img['filename']); ?>"><?php echo esc($img['type'] . '/' . $img['filename']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker('existing_image_select', 'service-image-preview')">Pick</button>
                    <div id="service-image-preview" class="picker-preview"></div>
                </div>
            <div><button type="submit">Add Service</button></div>
        </form>

        <h3>Existing Services</h3>
        <table>
            <thead><tr><th>ID</th><th>Title</th><th>Description</th><th>Icon</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($services as $s): ?>
                <tr>
                    <td><?php echo (int)$s['id']; ?></td>
                    <td><?php echo esc($s['title']); ?></td>
                    <td><?php echo esc(mb_strimwidth($s['description'], 0, 80, '...')); ?></td>
                    <td><?php if ($s['icon']): ?><img src="<?php echo esc(asset(ltrim($s['icon'], '/\\'))); ?>" style="height:40px"><?php endif; ?></td>
                    <td>
                        <a href="manage_services.php?action=edit&id=<?php echo (int)$s['id']; ?>">Edit</a>
                        <form action="manage_services.php" method="post" style="display:inline" onsubmit="return confirm('Delete service?');">
                            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                            <input type="hidden" name="form" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($action === 'edit' && !empty($service)): ?>
            <h3>Edit Service #<?php echo (int)$service['id']; ?></h3>
            <form action="manage_services.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                <input type="hidden" name="form" value="edit">
                <input type="hidden" name="id" value="<?php echo (int)$service['id']; ?>">
                <div><label>Title<br><input type="text" name="title" value="<?php echo esc($service['title']); ?>" required></label></div>
                <div><label>Description<br><textarea name="description" rows="4"><?php echo esc($service['description']); ?></textarea></label></div>
                <div>Current Icon: <?php if ($service['icon']): ?><img src="<?php echo esc(asset(ltrim($service['icon'], '/\\'))); ?>" style="height:40px"><?php else: ?>None<?php endif; ?></div>
                    <div><label>Replace Icon/Image<br><input type="file" name="image" accept="image/*"></label></div>
                    <div><label>Or choose existing image<br>
                        <select name="existing_image">
                            <option value="">-- none --</option>
                            <?php foreach ($images as $img): ?>
                                <option value="<?php echo esc($img['type'] . '/' . $img['filename']); ?>"><?php echo esc($img['type'] . '/' . $img['filename']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label></div>
                <div><button type="submit">Save Changes</button> <a href="manage_services.php">Cancel</a></div>
            </form>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
