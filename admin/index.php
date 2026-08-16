<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

$totalProjects = (int)db()->query('SELECT COUNT(*) FROM project')->fetchColumn();
$activeProjects = (int)db()->query("SELECT COUNT(*) FROM project WHERE status = 'active'")->fetchColumn();
$totalUsers = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalCategories = (int)db()->query('SELECT COUNT(*) FROM category')->fetchColumn();
$totalFavorites = (int)db()->query('SELECT COUNT(*) FROM favorites')->fetchColumn();

$recentFeedback = db()->query('SELECT f.*, u.username FROM feedback f LEFT JOIN users u ON u.user_id = f.user_id ORDER BY f.created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Bảng điều khiển quản trị';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header"><h1>Bảng điều khiển</h1></div>
<div class="admin-tabs">
  <a class="active" href="<?= e(SITE_URL) ?>/admin/index.php">Tổng quan</a>
  <a href="<?= e(SITE_URL) ?>/admin/users.php">Người dùng</a>
  <a href="<?= e(SITE_URL) ?>/admin/categories.php">Danh mục</a>
  <a href="<?= e(SITE_URL) ?>/admin/projects.php">Tất cả tài nguyên</a>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="value"><?= $totalProjects ?></div><div class="label">Tổng tài nguyên</div></div>
  <div class="stat-card"><div class="value"><?= $activeProjects ?></div><div class="label">Đang hoạt động</div></div>
  <div class="stat-card"><div class="value"><?= $totalUsers ?></div><div class="label">Người dùng</div></div>
  <div class="stat-card"><div class="value"><?= $totalCategories ?></div><div class="label">Danh mục</div></div>
  <div class="stat-card"><div class="value"><?= $totalFavorites ?></div><div class="label">Lượt yêu thích</div></div>
</div>

<div class="section-heading"><h2>Góp ý gần đây</h2></div>
<?php if (empty($recentFeedback)): ?>
  <p class="form-subtitle">Chưa có góp ý nào.</p>
<?php else: ?>
  <div class="table-card">
    <table class="data-table">
      <thead><tr><th>Người gửi</th><th>Nội dung</th><th>Thời gian</th></tr></thead>
      <tbody>
        <?php foreach ($recentFeedback as $f): ?>
          <tr>
            <td><?= e($f['username'] ?: 'Khách') ?></td>
            <td><?= e($f['feedback_text']) ?></td>
            <td><?= time_ago($f['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
