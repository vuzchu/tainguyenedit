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

  var fileInput = document.getElementById('coverInput');
  var preview = document.getElementById('coverPreview');
  if (fileInput && preview) {
    fileInput.addEventListener('change', function () {
      var file = fileInput.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });
  }
});
