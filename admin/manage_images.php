<?php
// Manage images (register, upload and remove DB entries)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/upload.php';

$message = '';
$error = '';

// Handle uploads (register into images table)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    try {
        $type = $_POST['type'] ?? 'other';
        if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('No file selected');
        }
        // target directory like assets/img/{type}
        $targetDir = __DIR__ . '/../assets/img/' . $type;
        $webPath = handle_image_upload('image', $targetDir);
        // store filename and type in images table, allow optional attachment to a content record
        $filename = basename($webPath);
        $attached_type = !empty($_POST['attached_type']) ? $_POST['attached_type'] : null;
        $attached_id = !empty($_POST['attached_id']) ? (int)$_POST['attached_id'] : null;
        $folder = $type;
        $path = $webPath;
        $stmt = $pdo->prepare('INSERT INTO images (type, filename, attached_type, attached_id, folder, path) VALUES (:type, :filename, :attached_type, :attached_id, :folder, :path)');
        $stmt->execute([':type' => $type, ':filename' => $filename, ':attached_type' => $attached_type, ':attached_id' => $attached_id, ':folder' => $folder, ':path' => $path]);
        $message = 'Image uploaded and registered.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle delete of DB entry (optionally delete file)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $delId = (int)$_GET['id'];
    $deleteFile = (!empty($_GET['delete_file']));
    $stmt = $pdo->prepare('SELECT * FROM images WHERE id = :id');
    $stmt->execute([':id' => $delId]);
    $row = $stmt->fetch();
    if ($row) {
        if ($deleteFile) {
            $possible = __DIR__ . '/../assets/img/' . $row['type'] . '/' . $row['filename'];
            if (is_file($possible)) {@unlink($possible);}            
        }
        $stmt = $pdo->prepare('DELETE FROM images WHERE id = :id');
        $stmt->execute([':id' => $delId]);
        $message = 'Image record removed.';
    } else {
        $error = 'Image not found';
    }
}

$images = $pdo->query('SELECT * FROM images ORDER BY uploaded_at DESC')->fetchAll();

?>
<?php $pageTitle = 'Manage Images'; require_once __DIR__ . '/partials/header.php'; ?>
    <style>table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px} img{max-height:60px}</style>
    
        <h2>Manage Images</h2>
        <?php if ($message): ?><div class="msg"><?php echo esc($message); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo esc($error); ?></div><?php endif; ?>

        <h3>Upload & Register Image</h3>
        <form action="manage_images.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload">
            <div><label>Type (folder):<br>
                <select name="type">
                    <option value="projects">projects</option>
                    <option value="team">team</option>
                    <option value="testimonials">testimonials</option>
                    <option value="blog">blog</option>
                    <option value="uploads">uploads</option>
                    <option value="other">other</option>
                </select>
            </label></div>
                <?php
                // Prepare lists for attachable records so admin can pick by title
                $attachLists = [];
                // content
                $stmt = $pdo->query("SELECT id, type, COALESCE(title, CONCAT(type, ' #', id)) AS label FROM content ORDER BY updated_at DESC LIMIT 200");
                $attachLists['content'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // services
                $stmt = $pdo->query("SELECT id, title FROM services ORDER BY id DESC LIMIT 200");
                $attachLists['services'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // team
                $stmt = $pdo->query("SELECT id, name FROM team ORDER BY id DESC LIMIT 200");
                $attachLists['team'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // testimonials
                $stmt = $pdo->query("SELECT id, COALESCE(author,name) AS name FROM testimonials ORDER BY id DESC LIMIT 200");
                $attachLists['testimonials'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // projects
                $stmt = $pdo->query("SELECT id, title FROM projects ORDER BY id DESC LIMIT 200");
                $attachLists['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // posts
                $stmt = $pdo->query("SELECT id, title FROM posts ORDER BY id DESC LIMIT 200");
                $attachLists['posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div>
                    <label>Attach to (optional):<br>
                        <select id="attached_type" name="attached_type">
                            <option value="">-- none --</option>
                            <option value="content">Content</option>
                            <option value="team">Team</option>
                            <option value="services">Service</option>
                            <option value="testimonials">Testimonial</option>
                            <option value="projects">Project</option>
                            <option value="posts">Post</option>
                        </select>
                    </label>
                    <label style="margin-left:10px;">Item:<br>
                        <select id="attached_id" name="attached_id" style="width:220px">
                            <option value="">-- select item --</option>
                        </select>
                    </label>
                </div>
                <script>
                    (function(){
                        var lists = <?php echo json_encode($attachLists, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
                        var typeSel = document.getElementById('attached_type');
                        var idSel = document.getElementById('attached_id');
                        function populate(t){
                            idSel.innerHTML = '<option value="">-- select item --</option>';
                            if (!t || !lists[t]) return;
                            lists[t].forEach(function(r){
                                var label = r.title || r.name || r.label || (r.type ? (r.type + ' #' + r.id) : (r.name || r.title || ('#'+r.id)));
                                var opt = document.createElement('option'); opt.value = r.id; opt.text = label; idSel.appendChild(opt);
                            });
                        }
                        typeSel.addEventListener('change', function(){ populate(this.value); });
                    })();
                </script>
            <div><label>Image<br><input type="file" name="image" accept="image/*"></label></div>
            <div><button type="submit">Upload & Register</button></div>
        </form>

        <h3>Registered Images</h3>
        <table>
                        <thead><tr><th>ID</th><th>Type</th><th>Filename</th><th>Attached</th><th>Preview</th><th>Uploaded</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($images as $it): ?>
                <tr>
                    <td><?php echo (int)$it['id']; ?></td>
                    <td><?php echo esc($it['type']); ?></td>
                    <td><?php echo esc($it['filename']); ?></td>
                                        <td>
                                            <?php if (!empty($it['attached_type']) && !empty($it['attached_id'])): 
                                                $atype = htmlspecialchars($it['attached_type']); $aid = (int)$it['attached_id'];
                                                $link = 'manage_' . $atype . '.php';
                                                if (is_file(__DIR__ . '/' . $link)):
                                            ?>
                                                <a href="<?php echo $link; ?>?id=<?php echo $aid; ?>"><?php echo $atype; ?> #<?php echo $aid; ?></a>
                                            <?php else: ?>
                                                <?php echo $atype; ?> #<?php echo $aid; ?>
                                            <?php endif; else: ?>
                                                (none)
                                            <?php endif; ?>
                                        </td>
                                        <td><?php
                                            // Build a project-aware URL for images so it works when the site lives in a subfolder
                                            $relPath = 'assets/img/' . $it['type'] . '/' . $it['filename'];
                                            $filePath = __DIR__ . '/../' . $relPath;
                                            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                                            $url = $base . '/' . $relPath;
                                            if (is_file($filePath)){
                                                ?><img src="<?php echo esc($url); ?>" alt=""><?php
                                            } else { echo 'file missing'; }
                                        ?></td>
                    <td><?php echo $it['uploaded_at']; ?></td>
                    <td>
                        <a href="?action=delete&id=<?php echo (int)$it['id']; ?>" onclick="return confirm('Delete DB record?');">Delete record</a>
                        |
                        <a href="?action=delete&id=<?php echo (int)$it['id']; ?>&delete_file=1" onclick="return confirm('Delete DB record and file?');">Delete + file</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>

