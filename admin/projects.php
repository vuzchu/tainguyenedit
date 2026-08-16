<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = db()->prepare('SELECT p.*, u.username AS owner_username FROM project p LEFT JOIN users u ON u.user_id = p.user_id WHERE p.title LIKE ? OR p.author LIKE ? ORDER BY p.create_date DESC');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = db()->query('SELECT p.*, u.username AS owner_username FROM project p LEFT JOIN users u ON u.user_id = p.user_id ORDER BY p.create_date DESC');
}
$projects = $stmt->fetchAll();

$pageTitle = 'Tất cả tài nguyên';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header"><h1>Tất cả tài nguyên</h1></div>
<div class="admin-tabs">
  <a href="<?= e(SITE_URL) ?>/admin/index.php">Tổng quan</a>
  <a href="<?= e(SITE_URL) ?>/admin/users.php">Người dùng</a>
  <a href="<?= e(SITE_URL) ?>/admin/categories.php">Danh mục</a>
  <a class="active" href="<?= e(SITE_URL) ?>/admin/projects.php">Tất cả tài nguyên</a>
</div>

<form method="get" class="search-form" style="max-width:360px;margin:20px 0;">
  <input type="text" name="q" placeholder="Tìm theo tiêu đề, tác giả..." value="<?= e($q) ?>">
  <button type="submit">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  </button>
</form>

<div class="table-card">
  <table class="data-table">
    <thead><tr><th>Tài nguyên</th><th>Danh mục</th><th>Người đăng</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
    <tbody>
      <?php foreach ($projects as $p): ?>
        <tr>
          <td><a href="<?= e(SITE_URL) ?>/project.php?id=<?= (int)$p['project_id'] ?>"><strong><?= e($p['title']) ?></strong></a></td>
          <td><?= e(category_name($p['category_id'])) ?></td>
          <td><?= e($p['owner_username'] ?: $p['author'] ?: '—') ?></td>
          <td><span class="badge badge-status-<?= e($p['status']) ?>"><?= $p['status'] === 'active' ? 'Hoạt động' : 'Đã tắt' ?></span></td>
          <td class="row-actions">
            <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/staff/project_edit.php?id=<?= (int)$p['project_id'] ?>">Sửa</a>
            <form method="post" action="<?= e(SITE_URL) ?>/staff/project_delete.php" onsubmit="return confirm('Xóa tài nguyên này?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$p['project_id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
