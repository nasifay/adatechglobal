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
        <?php
        $forms = include __DIR__ . '/forms.php';
        $base = $forms['services'] ?? [];
        $imgOptions = ['' => '-- none --'];
        foreach ($images as $img) {
            $imgOptions[$img['type'] . '/' . $img['filename']] = $img['type'] . '/' . $img['filename'];
        }
        foreach ($base as &$f) {
            if (($f['name'] ?? '') === 'image') {
                // services form uses 'image' file input; we'll also allow choosing existing images
            }
        }
        unset($f);
        // attach existing images select into fields so it submits with the form
        $opts = [];
        foreach ($images as $img) {
            $val = $img['type'] . '/' . $img['filename'];
            $opts[$val] = $val;
        }
        $base[] = ['type' => 'select', 'name' => 'existing_image', 'label' => 'Or choose existing image', 'options' => $opts];
        $base[] = ['type' => 'html', 'html' => '<div class="d-flex align-items-center" style="gap:8px;margin-top:6px"><button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker(\'existing_image\', \'service-image-preview\')">Pick</button><div id="service-image-preview" class="picker-preview" style="margin-left:8px"></div></div>'];

        $form_action = 'manage_services.php';
        $hidden = ['csrf' => $csrf, 'form' => 'add'];
        $fields = $base;
        $submit_label = 'Add Service';
        include __DIR__ . '/partials/admin_form.php';
        ?>

        <h3>Existing Services</h3>
        <table>
            <thead><tr><th>ID</th><th>Title</th><th>Description</th><th>Icon</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($services as $s): ?>
                <tr>
                    <td><?php echo (int)$s['id']; ?></td>
                    <td><?php echo esc($s['title']); ?></td>
                    <td><?php echo esc(mb_strimwidth($s['description'], 0, 80, '...')); ?></td>
                    <td><?php if ($s['icon']): ?><img src="/<?php echo esc($s['icon']); ?>" style="height:40px"><?php endif; ?></td>
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
            <?php
            $forms = include __DIR__ . '/forms.php';
            $base = $forms['services'] ?? [];
            foreach ($base as &$f) {
                if (!empty($service) && isset($f['name']) && isset($service[$f['name']])) {
                    $f['value'] = $service[$f['name']];
                }
            }
            unset($f);
            // inject current icon preview
            $previewHtml = '<div>Current Icon: ' . ($service['icon'] ? '<img src="/' . esc($service['icon']) . '" style="height:40px">' : 'None') . '</div>';
            // fix escaped quotes if any
            $previewHtml = str_replace('\\"', '"', $previewHtml);
            array_splice($base, 2, 0, [['type'=>'html','html'=>$previewHtml]]);
            $form_action = 'manage_services.php';
            $hidden = ['csrf' => $csrf, 'form' => 'edit', 'id' => (int)$service['id']];
            $fields = $base;
            $submit_label = 'Save Changes';
            // add existing images select into fields so it submits with form
            $opts = [];
            foreach ($images as $img) {
                $val = $img['type'] . '/' . $img['filename'];
                $opts[$val] = $val;
            }
            $base[] = ['type' => 'select', 'name' => 'existing_image', 'label' => 'Or choose existing image', 'options' => $opts];
            $base[] = ['type' => 'html', 'html' => '<div class="d-flex align-items-center" style="gap:8px;margin-top:6px"><button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker(\'existing_image\', \'service-image-preview\')">Pick</button><div id="service-image-preview" class="picker-preview" style="margin-left:8px"></div></div>'];

            include __DIR__ . '/partials/admin_form.php';
            ?>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
