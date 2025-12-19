// Centralized admin JavaScript: moved from inline scripts to this file.
(function(){
  document.addEventListener('DOMContentLoaded', function(){

    // Image picker: click thumbnails to populate the target input/select
    document.addEventListener('click', function(e){
      var item = e.target.closest && e.target.closest('.image-picker-item');
      if (!item) return;
      var file = item.getAttribute('data-file');
      var url = item.getAttribute('data-url');
      var modal = document.getElementById('imagePickerModal');
      if (!modal) return;
      var targetId = modal.getAttribute('data-target-id');
      if (!targetId) return;
      var target = document.getElementById(targetId);
      if (!target) return;
      if (target.tagName.toLowerCase() === 'select') {
        for (var i=0;i<target.options.length;i++){
          if (target.options[i].value === file || target.options[i].value === url || target.options[i].value === ('assets/img/' + file)) {
            target.value = target.options[i].value; break;
          }
        }
      } else {
        target.value = file;
      }
      var previewSelector = modal.getAttribute('data-preview-id');
      if (previewSelector) {
        var prev = document.getElementById(previewSelector);
        if (prev) prev.innerHTML = '<img src="'+url+'"> <div style="font-size:12px">'+file+'</div>';
      }
      try { var bsModal = bootstrap.Modal.getInstance(modal); if (bsModal) bsModal.hide(); } catch(e){}
    });

    // Expose openImagePicker to pages
    window.openImagePicker = function(targetId, previewId) {
      var modal = document.getElementById('imagePickerModal'); if (!modal) return;
      modal.setAttribute('data-target-id', targetId);
      if (previewId) modal.setAttribute('data-preview-id', previewId); else modal.removeAttribute('data-preview-id');
      try { var bs = new bootstrap.Modal(modal); bs.show(); } catch(e){}
    };

    // Signup modal show/hide handlers
    (function(){
      var modal = document.getElementById('signupModal'); if (!modal) return;
      var close = document.getElementById('signupModalClose');
      var cancel = document.getElementById('signupModalCancel');
      function show(){ modal.style.display = 'flex'; document.body.style.overflow='hidden'; }
      function hide(){ modal.style.display = 'none'; document.body.style.overflow='auto'; }
      document.addEventListener('click', function(e){ if (e.target && e.target.matches && e.target.matches('.show-signup-btn')) { e.preventDefault(); show(); } });
      if (close) close.addEventListener('click', hide);
      if (cancel) cancel.addEventListener('click', hide);
      document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hide(); });
    })();

    // manage_images: attach list population (pages should set window.admin_attach_lists = {...})
    try {
      var lists = window.admin_attach_lists || null;
      var typeSel = document.getElementById('attached_type');
      var idSel = document.getElementById('attached_id');
      if (typeSel && idSel && lists) {
        function populate(t){ idSel.innerHTML = '<option value="">-- select item --</option>'; if (!t || !lists[t]) return; lists[t].forEach(function(r){ var label = r.title || r.name || r.label || (r.type ? (r.type + ' #' + r.id) : (r.name || r.title || ('#'+r.id))); var opt = document.createElement('option'); opt.value = r.id; opt.text = label; idSel.appendChild(opt); }); }
        typeSel.addEventListener('change', function(){ populate(this.value); });
      }
    } catch(e){}

    // manage_feedback: showEdit and image preview handling
    window.showEdit = function(id){ var el = document.getElementById('edit-row-'+id); if(!el) return; if(el.style.display==='none' || el.style.display==='') el.style.display='table-row'; else el.style.display='none'; };
    document.querySelectorAll('[id^="feedback-image-select"]').forEach(function(sel){
      try {
        sel.addEventListener('change', function(){ var opt = this.selectedOptions[0]; var previewId = this.id === 'feedback-image-select' ? 'feedback-image-preview' : 'feedback-image-preview-' + this.id.split('-').pop(); var img = document.getElementById(previewId); if (img && opt && opt.dataset && opt.dataset.path) { img.src = opt.dataset.path; img.style.display = 'block'; } else if (img) { img.style.display = 'none'; } });
        setTimeout(function(){ var opt = sel.selectedOptions[0]; var previewId = sel.id === 'feedback-image-select' ? 'feedback-image-preview' : 'feedback-image-preview-' + sel.id.split('-').pop(); var p = document.getElementById(previewId); if (p && opt && opt.dataset && opt.dataset.path) { p.src = opt.dataset.path; p.style.display='block'; } }, 10);
      } catch(e){}
    });

    // manage_content: editItem and preview hookup
    window.editItem = function(id, type, title, body, image){
      var idEl = document.getElementById('content-id'); var typeEl = document.getElementById('content-type'); var titleEl = document.getElementById('content-title'); var bodyEl = document.getElementById('content-body'); var imageEl = document.getElementById('content-image'); var preview = document.getElementById('content-image-preview');
      if (idEl) idEl.value = id || ''; if (typeEl) typeEl.value = type || ''; if (titleEl) titleEl.value = title || ''; if (bodyEl) bodyEl.value = body || '';
      if (imageEl) { imageEl.value = image || ''; var opt = imageEl.selectedOptions[0]; if (opt && opt.dataset && opt.dataset.path && preview) { preview.src = opt.dataset.path; preview.style.display = 'block'; } else if (preview) { preview.style.display = 'none'; } }
      window.scrollTo(0,0);
    };
    var contentImage = document.getElementById('content-image'); if (contentImage) { contentImage.addEventListener('change', function(){ var opt = this.selectedOptions[0]; var img = document.getElementById('content-image-preview'); if (opt && opt.dataset && opt.dataset.path) { img.src = opt.dataset.path; img.style.display = 'block'; } else if (img) { img.style.display = 'none'; } }); }

  }); // DOMContentLoaded
})();
