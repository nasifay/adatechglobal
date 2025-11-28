<?php
// admin/manage_posts.php - CRUD blog posts
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/upload.php';

$action = $_GET['action'] ?? 'list';
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || !hash_equals($_POST['csrf'], $csrf)) { die('Invalid CSRF token'); }
    try {
        if ($_POST['form'] === 'add') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $excerpt = $_POST['excerpt'] ?? '';
            $body = $_POST['body'] ?? '';
            $author = $_POST['author'] ?? ($_SESSION['admin_user'] ?? 'Admin');
            $imageName = null;
            if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/blog');
                $imageName = basename($uploaded);
            }
            $stmt = $pdo->prepare('INSERT INTO posts (title, slug, excerpt, body, image, author) VALUES (:title, :slug, :excerpt, :body, :image, :author)');
            $stmt->execute([':title'=>$title, ':slug'=>$slug, ':excerpt'=>$excerpt, ':body'=>$body, ':image'=>$imageName, ':author'=>$author]);
            header('Location: manage_posts.php?msg=added'); exit;
        }
        if ($_POST['form'] === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $excerpt = $_POST['excerpt'] ?? '';
            $body = $_POST['body'] ?? '';
            $author = $_POST['author'] ?? ($_SESSION['admin_user'] ?? 'Admin');
            $imageName = null;
            if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploaded = handle_image_upload('image', __DIR__ . '/../assets/img/blog');
                $imageName = basename($uploaded);
            }
            if ($imageName) {
                $stmt = $pdo->prepare('UPDATE posts SET title=:title, slug=:slug, excerpt=:excerpt, body=:body, image=:image, author=:author WHERE id=:id');
                $stmt->execute([':title'=>$title, ':slug'=>$slug, ':excerpt'=>$excerpt, ':body'=>$body, ':image'=>$imageName, ':author'=>$author, ':id'=>$id]);
            } else {
                $stmt = $pdo->prepare('UPDATE posts SET title=:title, slug=:slug, excerpt=:excerpt, body=:body, author=:author WHERE id=:id');
                $stmt->execute([':title'=>$title, ':slug'=>$slug, ':excerpt'=>$excerpt, ':body'=>$body, ':author'=>$author, ':id'=>$id]);
            }
            header('Location: manage_posts.php?msg=updated'); exit;
        }
        if ($_POST['form'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT image FROM posts WHERE id = :id'); $stmt->execute([':id'=>$id]); $row = $stmt->fetch();
            if ($row && !empty($row['image'])) { $file = __DIR__ . '/../assets/img/blog/' . $row['image']; if (is_file($file)) @unlink($file); }
            $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id'); $stmt->execute([':id'=>$id]);
            header('Location: manage_posts.php?msg=deleted'); exit;
        }
    } catch (Exception $e) { $error = $e->getMessage(); }
}

if ($action === 'edit') { $id = (int)($_GET['id'] ?? 0); $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id'); $stmt->execute([':id'=>$id]); $post = $stmt->fetch(); }
$stmt = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC'); $posts = $stmt->fetchAll();
?>
<?php $pageTitle = 'Manage Posts'; require_once __DIR__ . '/partials/header.php'; ?>
    <style>table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px}</style>
        <h2>Manage Blog Posts</h2>
        <?php if (!empty($error)): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>
        <?php if (!empty($_GET['msg'])): ?><div class="msg"><?php echo esc($_GET['msg']); ?></div><?php endif; ?>

        <h3>Add Post</h3>
        <h3>Add Post</h3>
        <?php
        $forms = include __DIR__ . '/forms.php';
        $base = $forms['posts'] ?? [];
        // allow selecting an existing blog image
        $imgRows = $pdo->query("SELECT * FROM images WHERE type IN ('blog','uploads') ORDER BY uploaded_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
        $opts = [];
        foreach ($imgRows as $ir) {
            $val = $ir['filename'];
            $label = ($ir['type'] ? $ir['type'] . '/' : '') . $ir['filename'];
            $path = !empty($ir['path']) ? ltrim($ir['path'], '/\\') : 'assets/img/' . $ir['type'] . '/' . $ir['filename'];
            $opts[$val] = ['value' => $val, 'label' => $label, 'path' => $path];
        }
        $base[] = ['type' => 'picker', 'name' => 'existing_image', 'label' => 'Or choose existing image', 'options' => $opts];
        $form_action = 'manage_posts.php';
        $hidden = ['csrf' => $csrf, 'form' => 'add'];
        // default author
        foreach ($base as &$bf) { if (($bf['name'] ?? '') === 'author') $bf['value'] = $_SESSION['admin_user'] ?? 'Admin'; }
        unset($bf);
        $fields = $base;
        $submit_label = 'Add Post';
        include __DIR__ . '/partials/admin_form.php';
        ?>

        <h3>Existing Posts</h3>
        <table><thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Date</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td><?php echo (int)$p['id']; ?></td>
                <td><?php echo esc($p['title']); ?></td>
                <td><?php echo esc($p['author']); ?></td>
                <td><?php echo esc($p['created_at']); ?></td>
                <td>
                    <a href="manage_posts.php?action=edit&id=<?php echo (int)$p['id']; ?>">Edit</a>
                    <form action="manage_posts.php" method="post" style="display:inline" onsubmit="return confirm('Delete post?');">
                        <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">
                        <input type="hidden" name="form" value="delete">
                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody></table>

        <?php if ($action === 'edit' && !empty($post)): ?>
            <h3>Edit Post #<?php echo (int)$post['id']; ?></h3>
            <?php
            $forms = include __DIR__ . '/forms.php';
            $base = $forms['posts'] ?? [];
            foreach ($base as &$bf) {
                if (!empty($post) && isset($bf['name']) && isset($post[$bf['name']])) {
                    $bf['value'] = $post[$bf['name']];
                }
            }
            unset($bf);
            // current image preview
            $preview = '<div>Current Image: ' . ($post['image'] ? '<img src="/assets/img/blog/' . esc($post['image']) . '" style="height:40px">' : 'None') . '</div>';
            array_splice($base, 4, 0, [['type' => 'html', 'html' => $preview]]);
            // attach existing images picker
            $opts = [];
            foreach ($imgRows as $ir) {
                $val = $ir['filename'];
                $label = ($ir['type'] ? $ir['type'] . '/' : '') . $ir['filename'];
                $path = !empty($ir['path']) ? ltrim($ir['path'], '/\\') : 'assets/img/' . $ir['type'] . '/' . $ir['filename'];
                $opts[$val] = ['value' => $val, 'label' => $label, 'path' => $path];
            }
            $base[] = ['type' => 'picker', 'name' => 'existing_image', 'label' => 'Or choose existing image', 'options' => $opts];
            $form_action = 'manage_posts.php';
            $hidden = ['csrf' => $csrf, 'form' => 'edit', 'id' => (int)$post['id']];
            $fields = $base;
            $submit_label = 'Save Changes';
            include __DIR__ . '/partials/admin_form.php';
            ?>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
