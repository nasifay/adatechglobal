<?php
// Image picker modal for admin pages.
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/helpers.php';

$images = $pdo->query('SELECT * FROM images ORDER BY uploaded_at DESC')->fetchAll();

?>

<!-- Image Picker Modal -->
<div class="modal fade" id="imagePickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Choose Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="image-picker-grid">
          <?php foreach ($images as $img): 
            $url = '/assets/img/' . $img['type'] . '/' . $img['filename'];
          ?>
            <div class="image-picker-item" data-file="<?php echo esc($img['type'] . '/' . $img['filename']); ?>" data-url="<?php echo esc($url); ?>">
              <img src="<?php echo esc($url); ?>" alt="<?php echo esc($img['filename']); ?>">
              <div style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo esc($img['type'] . '/' . $img['filename']); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Image picker behavior handled in admin/admin.js -->
