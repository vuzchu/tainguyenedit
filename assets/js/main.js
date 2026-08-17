document.addEventListener('DOMContentLoaded', function () {
  var trigger = document.getElementById('accountTrigger');
  var dropdown = document.getElementById('accountDropdown');
  if (trigger && dropdown) {
    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
      }
    });
  }

  document.querySelectorAll('.fav-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var projectId = btn.getAttribute('data-project-id');
      fetch(btn.getAttribute('data-base-url') + '/favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'project_id=' + encodeURIComponent(projectId) + '&csrf_token=' + encodeURIComponent(btn.getAttribute('data-csrf'))
      })
        .then(function (res) {
          if (res.status === 401) {
            window.location.href = btn.getAttribute('data-login-url');
            return null;
          }
          return res.json();
        })
        .then(function (data) {
          if (!data) return;
          btn.classList.toggle('active', data.favorited);
        });
    });
  });

  var langTrigger = document.getElementById('langTrigger');
  var langDropdown = document.getElementById('langDropdown');
  if (langTrigger && langDropdown) {
    langTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      langDropdown.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (!langDropdown.contains(e.target)) {
        langDropdown.classList.remove('open');
      }
    });
  }

  var donateTrigger = document.getElementById('donateTrigger');
  var donateModal = document.getElementById('donateModal');
  var donateModalClose = document.getElementById('donateModalClose');
  var donateModalBackdrop = document.getElementById('donateModalBackdrop');
  if (donateTrigger && donateModal) {
    donateTrigger.addEventListener('click', function () {
      donateModal.classList.add('open');
    });
    var closeDonateModal = function () {
      donateModal.classList.remove('open');
    };
    if (donateModalClose) donateModalClose.addEventListener('click', closeDonateModal);
    if (donateModalBackdrop) donateModalBackdrop.addEventListener('click', closeDonateModal);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeDonateModal();
    });
  }

  var donateCopyBtn = document.getElementById('donateCopyBtn');
  if (donateCopyBtn) {
    donateCopyBtn.addEventListener('click', function () {
      var value = donateCopyBtn.getAttribute('data-copy');
      navigator.clipboard.writeText(value).then(function () {
        var original = donateCopyBtn.getAttribute('data-label');
        donateCopyBtn.textContent = donateCopyBtn.getAttribute('data-copied');
        setTimeout(function () {
          donateCopyBtn.textContent = original;
        }, 1500);
      });
    });
  }

  var fileInput = document.getElementById('coverInput');
  var preview = document.getElementById('coverPreview');
  var urlInput = document.getElementById('coverUrlInput');
  var status = document.getElementById('coverUploadStatus');
  if (fileInput && preview && urlInput) {
    var form = fileInput.closest('form');
    var submitBtn = form ? form.querySelector('button[type="submit"]') : null;

    fileInput.addEventListener('change', function () {
      var file = fileInput.files[0];
      if (!file) return;

      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);

      urlInput.value = '';
      if (status) status.textContent = 'Đang tải ảnh lên...';
      if (submitBtn) submitBtn.disabled = true;

      var body = new FormData();
      body.append('key', fileInput.getAttribute('data-imgbb-key') || '');
      body.append('image', file);

      fetch('https://api.imgbb.com/1/upload', { method: 'POST', body: body })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.success && data.data && data.data.url) {
            urlInput.value = data.data.url;
            if (status) status.textContent = '';
          } else {
            if (status) status.textContent = 'Tải ảnh lên thất bại, vui lòng thử lại.';
          }
        })
        .catch(function () {
          if (status) status.textContent = 'Tải ảnh lên thất bại, vui lòng thử lại.';
        })
        .finally(function () {
          if (submitBtn) submitBtn.disabled = false;
        });
    });

    if (form) {
      form.addEventListener('submit', function (e) {
        if (fileInput.files[0] && !urlInput.value) {
          e.preventDefault();
          if (status) status.textContent = 'Ảnh vẫn đang tải lên, vui lòng đợi rồi bấm lại.';
        }
      });
    }
  }
});
