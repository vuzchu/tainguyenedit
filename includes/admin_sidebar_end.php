    <div class="admin-user">
      <span class="avatar-circle"><?= e(initials($user['full_name'])) ?></span>
      <div class="admin-user-info">
        <strong><?= e($user['full_name']) ?></strong>
        <a href="<?= e(SITE_URL) ?>/auth/logout.php">Đăng xuất</a>
      </div>
    </div>
  </aside>

  <main class="admin-main">
    <div class="container">
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
