<?php
// admin/manage_testimonials.php - CRUD testimonials
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
            $name = $_POST['name'] ?? '';
            $role = $_POST['role'] ?? '';
            $message = $_POST['message'] ?? '';
            $imageName = null;
                if (!empty($_POST['existing_image'])) {
                    $imageName = basename($_POST['existing_image']);
                } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/testimonials');
                    $imageName = basename($uploaded);
                }
            $stmt = $pdo->prepare('INSERT INTO testimonials (name, role, message, image) VALUES (:name, :role, :message, :image)');
            $stmt->execute([':name' => $name, ':role' => $role, ':message' => $message, ':image' => $imageName]);
            header('Location: manage_testimonials.php?msg=added');
            exit;
        }

        if ($_POST['form'] === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $role = $_POST['role'] ?? '';
            $message = $_POST['message'] ?? '';
            $imageName = null;
            if (!empty($_POST['existing_image'])) {
                $imageName = basename($_POST['existing_image']);
            } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/testimonials');
                $imageName = basename($uploaded);
            }
            if ($imageName) {
                $stmt = $pdo->prepare('UPDATE testimonials SET name = :name, role = :role, message = :message, image = :image WHERE id = :id');
                $stmt->execute([':name' => $name, ':role' => $role, ':message' => $message, ':image' => $imageName, ':id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE testimonials SET name = :name, role = :role, message = :message WHERE id = :id');
                $stmt->execute([':name' => $name, ':role' => $role, ':message' => $message, ':id' => $id]);
            }
            header('Location: manage_testimonials.php?msg=updated');
            exit;
        }

        if ($_POST['form'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT image FROM testimonials WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if ($row && !empty($row['image'])) {
                $file = __DIR__ . '/../assets/img/testimonials/' . $row['image'];
                if (is_file($file)) {@unlink($file);}            
            }
            $stmt = $pdo->prepare('DELETE FROM testimonials WHERE id = :id');
            $stmt->execute([':id' => $id]);
            header('Location: manage_testimonials.php?msg=deleted');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $testimonial = $stmt->fetch();
}

$stmt = $pdo->query('SELECT * FROM testimonials ORDER BY id DESC');
$items = $stmt->fetchAll();

// images for testimonials
$testImagesStmt = $pdo->prepare("SELECT * FROM images WHERE type IN ('testimonials','uploads','other') ORDER BY uploaded_at DESC");
$testImagesStmt->execute();
$testImages = $testImagesStmt->fetchAll();

?>
<?php $pageTitle = 'Manage Testimonials'; require_once __DIR__ . '/partials/header.php'; ?>
    <style>table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px}</style>
<?php require_once __DIR__ . '/partials/image_picker.php'; ?>
        <h2>Manage Testimonials</h2>
        <?php if (!empty($error)): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>
        <?php if (!empty($_GET['msg'])): ?><div class="msg"><?php echo esc($_GET['msg']); ?></div><?php endif; ?>

        <h3>Add Testimonial</h3>
        <form action="manage_testimonials.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
            <input type="hidden" name="form" value="add">
            <div><label>Name<br><input type="text" name="name" required></label></div>
            <div><label>Role<br><input type="text" name="role"></label></div>
            <div><label>Message<br><textarea name="message" rows="4"></textarea></label></div>
            <div><label>Image<br><input type="file" name="image" accept="image/*"></label></div>
            <div class="d-flex align-items-center" style="gap:8px">
                <label>Existing image: </label>
                <select id="test_image_select" name="existing_image">
                    <option value="">-- none --</option>
                    <?php foreach ($testImages as $img): ?>
                        <option value="<?php echo esc($img['filename']); ?>"><?php echo esc($img['type'] . '/' . $img['filename']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker('test_image_select', 'test-image-preview')">Pick</button>
                <div id="test-image-preview" class="picker-preview"></div>
            </div>
            <div><button type="submit">Add Testimonial</button></div>
        </form>

        <h3>Existing Testimonials</h3>
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Message</th><th>Image</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td><?php echo (int)$it['id']; ?></td>
                    <td><?php echo esc($it['name']); ?></td>
                    <td><?php echo esc($it['role']); ?></td>
                    <td><?php echo esc(mb_strimwidth($it['message'], 0, 80, '...')); ?></td>
                    <td><?php if ($it['image']): ?><img src="<?php echo esc(asset('assets/img/testimonials/' . $it['image'])); ?>" style="height:40px"><?php endif; ?></td>
                    <td>
                        <a href="manage_testimonials.php?action=edit&id=<?php echo (int)$it['id']; ?>">Edit</a>
                        <form action="manage_testimonials.php" method="post" style="display:inline" onsubmit="return confirm('Delete testimonial?');">
                            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                            <input type="hidden" name="form" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int)$it['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($action === 'edit' && !empty($testimonial)): ?>
            <h3>Edit Testimonial #<?php echo (int)$testimonial['id']; ?></h3>
            <form action="manage_testimonials.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                <input type="hidden" name="form" value="edit">
                <input type="hidden" name="id" value="<?php echo (int)$testimonial['id']; ?>">
                <div><label>Name<br><input type="text" name="name" value="<?php echo esc($testimonial['name']); ?>" required></label></div>
                <div><label>Role<br><input type="text" name="role" value="<?php echo esc($testimonial['role']); ?>"></label></div>
                <div><label>Message<br><textarea name="message" rows="4"><?php echo esc($testimonial['message']); ?></textarea></label></div>
                <div>Current Image: <?php if ($testimonial['image']): ?><img src="<?php echo esc(asset('assets/img/testimonials/' . $testimonial['image'])); ?>" style="height:40px"><?php else: ?>None<?php endif; ?></div>
                <div><label>Replace Image<br><input type="file" name="image" accept="image/*"></label></div>
                <div><label>Or choose existing image<br>
                    <select name="existing_image">
                        <option value="">-- none --</option>
                        <?php foreach ($testImages as $img): ?>
                            <option value="<?php echo esc($img['filename']); ?>"><?php echo esc($img['type'] . '/' . $img['filename']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label></div>
                <div><button type="submit">Save Changes</button> <a href="manage_testimonials.php">Cancel</a></div>
            </form>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
