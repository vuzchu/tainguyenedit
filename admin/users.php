<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

$currentUser = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $targetId = (int)($_POST['user_id'] ?? 0);

    if ($targetId === (int)$currentUser['user_id']) {
        flash('error', 'Bạn không thể tự thay đổi hoặc xóa tài khoản của chính mình.');
    } elseif ($action === 'update_role') {
        $roleId = (int)($_POST['role_id'] ?? 0);
        if (in_array($roleId, [ROLE_CUSTOMER, ROLE_STAFF, ROLE_ADMIN], true)) {
            $stmt = db()->prepare('UPDATE users SET role_id = ? WHERE user_id = ?');
            $stmt->execute([$roleId, $targetId]);
            flash('success', 'Đã cập nhật quyền người dùng.');
        }
    } elseif ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$targetId]);
        flash('success', 'Đã xóa người dùng.');
    }
    redirect(SITE_URL . '/admin/users.php');
}

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = db()->prepare('SELECT u.*, r.role_name FROM users u LEFT JOIN roles r ON r.role_id = u.role_id WHERE u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ? ORDER BY u.created_at DESC');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = db()->query('SELECT u.*, r.role_name FROM users u LEFT JOIN roles r ON r.role_id = u.role_id ORDER BY u.created_at DESC');
}
$users = $stmt->fetchAll();

$pageTitle = 'Quản lý người dùng';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header"><h1>Người dùng</h1></div>
<div class="admin-tabs">
  <a href="<?= e(SITE_URL) ?>/admin/index.php">Tổng quan</a>
  <a class="active" href="<?= e(SITE_URL) ?>/admin/users.php">Người dùng</a>
  <a href="<?= e(SITE_URL) ?>/admin/categories.php">Danh mục</a>
  <a href="<?= e(SITE_URL) ?>/admin/projects.php">Tất cả tài nguyên</a>
</div>

<form method="get" class="search-form" style="max-width:360px;margin:20px 0;">
  <input type="text" name="q" placeholder="Tìm theo tên, email..." value="<?= e($q) ?>">
  <button type="submit">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  </button>
</form>

<div class="table-card">
  <table class="data-table">
    <thead>
      <tr><th>Người dùng</th><th>Email</th><th>Quyền</th><th>Tham gia</th><th>Thao tác</th></tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><strong><?= e($u['full_name']) ?></strong><br><span style="color:var(--muted);font-size:12.5px;">@<?= e($u['username']) ?></span></td>
          <td><?= e($u['email']) ?></td>
          <td>
            <?php if ((int)$u['user_id'] === (int)$currentUser['user_id']): ?>
              <span class="badge badge-status-active"><?= e($u['role_name'] ?: '—') ?></span>
            <?php else: ?>
              <form method="post" style="display:inline-flex;gap:6px;align-items:center;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_role">
                <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                <select name="role_id" class="role-select" onchange="this.form.submit()">
                  <option value="<?= ROLE_CUSTOMER ?>" <?= (int)$u['role_id'] === ROLE_CUSTOMER ? 'selected' : '' ?>>Customer</option>
                  <option value="<?= ROLE_STAFF ?>" <?= (int)$u['role_id'] === ROLE_STAFF ? 'selected' : '' ?>>Staff</option>
                  <option value="<?= ROLE_ADMIN ?>" <?= (int)$u['role_id'] === ROLE_ADMIN ? 'selected' : '' ?>>Admin</option>
                </select>
              </form>
            <?php endif; ?>
          </td>
          <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if ((int)$u['user_id'] !== (int)$currentUser['user_id']): ?>
              <form method="post" onsubmit="return confirm('Xóa người dùng này?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
