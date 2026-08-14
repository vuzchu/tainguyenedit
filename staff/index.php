<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_STAFF, ROLE_ADMIN]);

$user = current_user();
$stmt = db()->prepare('SELECT * FROM project WHERE user_id = ? ORDER BY create_date DESC');
$stmt->execute([$user['user_id']]);
$projects = $stmt->fetchAll();

$pageTitle = 'Tài nguyên của tôi';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1>Tài nguyên của tôi</h1>
  <a class="btn btn-primary btn-sm" href="<?= e(SITE_URL) ?>/staff/project_new.php">+ Đăng tài nguyên mới</a>
</div>

<?php if (empty($projects)): ?>
  <div class="empty-state">
    <h1>—</h1>
    <p>Bạn chưa đăng tài nguyên nào.</p>
    <a class="btn btn-primary" href="<?= e(SITE_URL) ?>/staff/project_new.php">Đăng tài nguyên đầu tiên</a>
  </div>
<?php else: ?>
  <div class="table-card">
    <table class="data-table">
      <thead>
        <tr><th>Tài nguyên</th><th>Danh mục</th><th>Trạng thái</th><th>Ngày đăng</th><th>Thao tác</th></tr>
      </thead>
      <tbody>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><a href="<?= e(SITE_URL) ?>/project.php?id=<?= (int)$p['project_id'] ?>"><strong><?= e($p['title']) ?></strong></a></td>
            <td><?= e(category_name($p['category_id'])) ?></td>
            <td><span class="badge badge-status-<?= e($p['status']) ?>"><?= $p['status'] === 'active' ? 'Hoạt động' : 'Đã tắt' ?></span></td>
            <td><?= date('d/m/Y', strtotime($p['create_date'])) ?></td>
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
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
