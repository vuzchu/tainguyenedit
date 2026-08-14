<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['category_name'] ?? '');
        if ($name === '') {
            $errors[] = 'Vui lòng nhập tên danh mục.';
        } else {
            $stmt = db()->prepare('INSERT INTO category (category_name) VALUES (?)');
            $stmt->execute([$name]);
            flash('success', 'Đã thêm danh mục.');
            redirect(SITE_URL . '/admin/categories.php');
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['category_name'] ?? '');
        if ($name !== '' && $id > 0) {
            $stmt = db()->prepare('UPDATE category SET category_name = ? WHERE category_id = ?');
            $stmt->execute([$name, $id]);
            flash('success', 'Đã cập nhật danh mục.');
        }
        redirect(SITE_URL . '/admin/categories.php');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['category_id'] ?? 0);
        $stmt = db()->prepare('DELETE FROM category WHERE category_id = ?');
        $stmt->execute([$id]);
        flash('success', 'Đã xóa danh mục. Các tài nguyên thuộc danh mục này sẽ chuyển sang "Chưa phân loại".');
        redirect(SITE_URL . '/admin/categories.php');
    }
}

$categoryRows = db()->query('SELECT c.*, (SELECT COUNT(*) FROM project p WHERE p.category_id = c.category_id) AS project_count FROM category c ORDER BY c.category_name ASC')->fetchAll();

$pageTitle = 'Quản lý danh mục';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header"><h1>Danh mục</h1></div>
<div class="admin-tabs">
  <a href="<?= e(SITE_URL) ?>/admin/index.php">Tổng quan</a>
  <a href="<?= e(SITE_URL) ?>/admin/users.php">Người dùng</a>
  <a class="active" href="<?= e(SITE_URL) ?>/admin/categories.php">Danh mục</a>
  <a href="<?= e(SITE_URL) ?>/admin/projects.php">Tất cả tài nguyên</a>
</div>

<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?= e($err) ?></div>
<?php endforeach; ?>

<form method="post" style="display:flex;gap:10px;max-width:480px;margin:20px 0;">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="add">
  <input class="form-control" type="text" name="category_name" placeholder="Tên danh mục mới" required>
  <button type="submit" class="btn btn-primary btn-sm">Thêm</button>
</form>

<div class="table-card">
  <table class="data-table">
    <thead><tr><th>Danh mục</th><th>Số tài nguyên</th><th>Thao tác</th></tr></thead>
    <tbody>
      <?php foreach ($categoryRows as $cat): ?>
        <tr>
          <td>
            <form method="post" style="display:flex;gap:8px;align-items:center;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="category_id" value="<?= (int)$cat['category_id'] ?>">
              <input class="form-control" style="max-width:260px;" type="text" name="category_name" value="<?= e($cat['category_name']) ?>">
              <button type="submit" class="btn btn-outline btn-sm">Lưu</button>
            </form>
          </td>
          <td><?= (int)$cat['project_count'] ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Xóa danh mục này?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="category_id" value="<?= (int)$cat['category_id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
