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
            $url = asset('assets/img/' . $img['type'] . '/' . $img['filename']);
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

<script>
  (function(){
    let currentTarget = null;
    // when a thumbnail clicked, set the target input/select
    document.addEventListener('click', function(e){
      const item = e.target.closest('.image-picker-item');
      if (!item) return;
      const file = item.getAttribute('data-file');
      const url = item.getAttribute('data-url');
      // find target select/input stored on modal element
      const modal = document.getElementById('imagePickerModal');
      const targetId = modal.getAttribute('data-target-id');
      if (!targetId) return;
      const target = document.getElementById(targetId);
      if (!target) return;
      // if target is a select, set value matching option else set input value
      if (target.tagName.toLowerCase() === 'select') {
        // try to find option matching value
        for (let i=0;i<target.options.length;i++){
          if (target.options[i].value === file || target.options[i].value === url || target.options[i].value === ('assets/img/' + file)) {
            target.value = target.options[i].value;
            break;
          }
        }
      } else {
        target.value = file;
      }
      // update preview if available
      const previewSelector = modal.getAttribute('data-preview-id');
      if (previewSelector) {
        const prev = document.getElementById(previewSelector);
        if (prev) {
          prev.innerHTML = '<img src="'+url+'"> <div style="font-size:12px">'+file+'</div>';
        }
      }
      // close modal
      var bsModal = bootstrap.Modal.getInstance(modal);
      if (bsModal) bsModal.hide();
    });

    // when opening, set which target select/input to populate
    window.openImagePicker = function(targetId, previewId) {
      const modal = document.getElementById('imagePickerModal');
      modal.setAttribute('data-target-id', targetId);
      if (previewId) modal.setAttribute('data-preview-id', previewId); else modal.removeAttribute('data-preview-id');
      var bs = new bootstrap.Modal(modal);
      bs.show();
    }
  })();
</script>
