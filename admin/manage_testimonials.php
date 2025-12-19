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
        <?php
        $forms = include __DIR__ . '/forms.php';
        $base = $forms['testimonials'] ?? [];
        $testOptions = ['' => '-- none --'];
        foreach ($testImages as $img) {
            $testOptions[$img['filename']] = $img['type'] . '/' . $img['filename'];
        }
        foreach ($base as &$f) {
            if (($f['name'] ?? '') === 'image') $f['options'] = $testOptions;
        }
        unset($f);
        $form_action = 'manage_testimonials.php';
        $hidden = ['csrf' => $csrf, 'form' => 'add'];
        $fields = $base;
        $submit_label = 'Add Testimonial';
        include __DIR__ . '/partials/admin_form.php';
        ?>

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
                    <td><?php if ($it['image']): ?><img src="/assets/img/testimonials/<?php echo esc($it['image']); ?>" style="height:40px"><?php endif; ?></td>
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
            <?php
            $forms = include __DIR__ . '/forms.php';
            $base = $forms['testimonials'] ?? [];
            $testOptions = ['' => '-- none --'];
            foreach ($testImages as $img) {
                $testOptions[$img['filename']] = $img['type'] . '/' . $img['filename'];
            }
            foreach ($base as &$f) {
                if (($f['name'] ?? '') === 'image') $f['options'] = $testOptions;
                if (!empty($testimonial) && isset($f['name']) && isset($testimonial[$f['name']])) {
                    $f['value'] = $testimonial[$f['name']];
                }
            }
            unset($f);
            // add current image preview
            array_splice($base, 3, 0, [['type'=>'html','html'=>'<div>Current Image: ' . ($testimonial['image'] ? '<img src="/assets/img/testimonials/' . esc($testimonial['image']) . '" style="height:40px">' : 'None') . '</div>']]);
            $form_action = 'manage_testimonials.php';
            $hidden = ['csrf' => $csrf, 'form' => 'edit', 'id' => (int)$testimonial['id']];
            $fields = $base;
            $submit_label = 'Save Changes';
            include __DIR__ . '/partials/admin_form.php';
            ?>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
