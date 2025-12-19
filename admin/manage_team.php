<?php
// admin/manage_team.php - CRUD team members
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
            $bio = $_POST['bio'] ?? '';
            $imageName = null;
                if (!empty($_POST['existing_image'])) {
                    $imageName = basename($_POST['existing_image']);
                } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/team');
                    $imageName = basename($uploaded);
                }
            $stmt = $pdo->prepare('INSERT INTO team (name, role, bio, image) VALUES (:name, :role, :bio, :image)');
            $stmt->execute([':name' => $name, ':role' => $role, ':bio' => $bio, ':image' => $imageName]);
            header('Location: manage_team.php?msg=added');
            exit;
        }

        if ($_POST['form'] === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $role = $_POST['role'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $imageName = null;
            if (!empty($_POST['existing_image'])) {
                $imageName = basename($_POST['existing_image']);
            } elseif (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/team');
                $imageName = basename($uploaded);
            }
            if ($imageName) {
                $stmt = $pdo->prepare('UPDATE team SET name = :name, role = :role, bio = :bio, image = :image WHERE id = :id');
                $stmt->execute([':name' => $name, ':role' => $role, ':bio' => $bio, ':image' => $imageName, ':id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE team SET name = :name, role = :role, bio = :bio WHERE id = :id');
                $stmt->execute([':name' => $name, ':role' => $role, ':bio' => $bio, ':id' => $id]);
            }
            header('Location: manage_team.php?msg=updated');
            exit;
        }

        if ($_POST['form'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT image FROM team WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if ($row && !empty($row['image'])) {
                $file = __DIR__ . '/../assets/img/team/' . $row['image'];
                if (is_file($file)) {@unlink($file);}            
            }
            $stmt = $pdo->prepare('DELETE FROM team WHERE id = :id');
            $stmt->execute([':id' => $id]);
            header('Location: manage_team.php?msg=deleted');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM team WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $member = $stmt->fetch();
}

$stmt = $pdo->query('SELECT * FROM team ORDER BY id DESC');
$members = $stmt->fetchAll();

// images for team
$teamImagesStmt = $pdo->prepare("SELECT * FROM images WHERE type IN ('team','uploads','other') ORDER BY uploaded_at DESC");
$teamImagesStmt->execute();
$teamImages = $teamImagesStmt->fetchAll();

?>
<?php $pageTitle = 'Manage Team'; require_once __DIR__ . '/partials/header.php'; ?>
    <style>table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px}</style>
<?php require_once __DIR__ . '/partials/image_picker.php'; ?>
        <h2>Manage Team</h2>
        <?php if (!empty($error)): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>
        <?php if (!empty($_GET['msg'])): ?><div class="msg"><?php echo esc($_GET['msg']); ?></div><?php endif; ?>

        <h3>Add Team Member</h3>
        <?php
        // Load central form defs and populate dynamic options
        $forms = include __DIR__ . '/forms.php';
        $teamForm = $forms['team'] ?? [];
        $teamOptions = ['' => '-- none --'];
        foreach ($teamImages as $img) {
            $teamOptions[$img['filename']] = $img['type'] . '/' . $img['filename'];
        }
        // replace placeholder options
        foreach ($teamForm as &$f) {
            if (($f['name'] ?? '') === 'existing_image') {
                $f['options'] = $teamOptions;
            }
        }
        unset($f);
        $form_action = 'manage_team.php';
        $hidden = ['csrf' => $csrf, 'form' => 'add'];
        $fields = $teamForm;
        $submit_label = 'Add Member';
        include __DIR__ . '/partials/admin_form.php';
        ?>
        <div style="margin-top:6px">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker('team_image_select', 'team-image-preview')">Pick</button>
            <div id="team-image-preview" class="picker-preview" style="display:inline-block;margin-left:8px"></div>
        </div>

        <h3>Existing Members</h3>
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Image</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?php echo (int)$m['id']; ?></td>
                    <td><?php echo esc($m['name']); ?></td>
                    <td><?php echo esc($m['role']); ?></td>
                    <td><?php
                        if ($m['image']) {
                            $rel = (strpos($m['image'], '/') !== false) ? ltrim($m['image'], '/\\') : 'assets/img/team/' . $m['image'];
                            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                            $url = $base . '/' . $rel;
                            echo '<img src="' . esc($url) . '" style="height:40px">';
                        }
                    ?></td>
                    <td>
                        <a href="manage_team.php?action=edit&id=<?php echo (int)$m['id']; ?>">Edit</a>
                        <form action="manage_team.php" method="post" style="display:inline" onsubmit="return confirm('Delete member?');">
                            <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                            <input type="hidden" name="form" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int)$m['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($action === 'edit' && !empty($member)): ?>
            <h3>Edit Member #<?php echo (int)$member['id']; ?></h3>
            <?php
            $forms = include __DIR__ . '/forms.php';
            $teamForm = $forms['team'] ?? [];
            $teamOptions = ['' => '-- none --'];
            foreach ($teamImages as $img) {
                $teamOptions[$img['filename']] = $img['type'] . '/' . $img['filename'];
            }
            foreach ($teamForm as &$f) {
                if (($f['name'] ?? '') === 'existing_image') {
                    $f['options'] = $teamOptions;
                }
                // prefill values for edit if matching key exists on member row
                if (!empty($member) && isset($f['name']) && isset($member[$f['name']])) {
                    $f['value'] = $member[$f['name']];
                }
            }
            unset($f);
            // inject current image preview as HTML field before the file input
            $previewHtml = '<div>Current Image: ' . ($member['image'] ? '<img src="/assets/img/team/' . esc($member['image']) . '" style="height:40px">' : 'None') . '</div>';
            // find index of file field and insert preview before it
            $insertAt = null;
            foreach ($teamForm as $i => $f) {
                if (($f['type'] ?? '') === 'file') { $insertAt = $i; break; }
            }
            if ($insertAt !== null) {
                array_splice($teamForm, $insertAt, 0, [['type'=>'html','html'=>$previewHtml]]);
            } else {
                $teamForm[] = ['type'=>'html','html'=>$previewHtml];
            }

            $form_action = 'manage_team.php';
            $hidden = ['csrf' => $csrf, 'form' => 'edit', 'id' => (int)$member['id']];
            $fields = $teamForm;
            $submit_label = 'Save Changes';
            include __DIR__ . '/partials/admin_form.php';
            ?>
            <div><a href="manage_team.php">Cancel</a></div>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
