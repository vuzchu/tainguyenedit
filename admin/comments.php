<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['comment_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'approve') {
        $stmt = db()->prepare('UPDATE comments SET is_active = 1 WHERE comment_id = ?');
        $stmt->execute([$id]);
        flash('success', 'Đã duyệt bình luận.');
    } elseif ($action === 'unapprove') {
        $stmt = db()->prepare('UPDATE comments SET is_active = 0 WHERE comment_id = ?');
        $stmt->execute([$id]);
        flash('success', 'Đã ẩn bình luận.');
    } elseif ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM comments WHERE comment_id = ?');
        $stmt->execute([$id]);
        flash('success', 'Đã xóa bình luận.');
    }
    $backQs = array_filter([
        'status' => $_POST['back_status'] ?? null,
        'date_from' => $_POST['back_date_from'] ?? null,
        'date_to' => $_POST['back_date_to'] ?? null,
    ], fn($v) => $v !== null && $v !== '');
    redirect(SITE_URL . '/admin/comments.php' . ($backQs ? '?' . http_build_query($backQs) : ''));
}

$status = $_GET['status'] ?? 'pending';
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');

$where = [];
$params = [];
if ($status === 'pending') {
    $where[] = 'c.is_active = 0';
} elseif ($status === 'approved') {
    $where[] = 'c.is_active = 1';
}
if ($dateFrom !== '') {
    $where[] = 'DATE(c.created_at) >= :date_from';
    $params['date_from'] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'DATE(c.created_at) <= :date_to';
    $params['date_to'] = $dateTo;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$stmt = db()->prepare("SELECT c.*, u.full_name, p.project_id, p.title AS project_title FROM comments c JOIN users u ON u.user_id = c.user_id JOIN project p ON p.project_id = c.project_id $whereSql ORDER BY c.created_at DESC");
$stmt->execute($params);
$comments = $stmt->fetchAll();

$pageTitle = 'Bình luận';
$activeAdminNav = 'comments';
require __DIR__ . '/../includes/admin_header.php';
require __DIR__ . '/../includes/admin_sidebar_end.php';
?>

<div class="page-header"><h1>Bình luận</h1></div>

<form class="resource-filters" method="get">
  <select name="status">
    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Tất cả</option>
  </select>
  <input type="date" name="date_from" value="<?= e($dateFrom) ?>" title="Từ ngày">
  <input type="date" name="date_to" value="<?= e($dateTo) ?>" title="Đến ngày">
  <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
  <?php if ($status !== 'pending' || $dateFrom !== '' || $dateTo !== ''): ?>
    <a class="sidebar-clear" href="<?= e(SITE_URL) ?>/admin/comments.php">Xóa lọc</a>
  <?php endif; ?>
</form>

<?php if (empty($comments)): ?>
  <div class="empty-state">
    <h1>—</h1>
    <p>Không có bình luận nào.</p>
  </div>
<?php else: ?>
  <div class="table-card">
    <table class="data-table">
      <thead><tr><th>Người bình luận</th><th>Tài nguyên</th><th>Nội dung</th><th>Trạng thái</th><th>Thời gian</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach ($comments as $c): ?>
          <tr>
            <td><strong><?= e($c['full_name']) ?></strong></td>
            <td><a href="<?= e(SITE_URL) ?>/project.php?id=<?= (int)$c['project_id'] ?>#comments"><?= e($c['project_title']) ?></a></td>
            <td><?= e($c['comment_text']) ?></td>
            <td><span class="badge badge-status-<?= $c['is_active'] ? 'active' : 'disable' ?>"><?= $c['is_active'] ? 'Đã duyệt' : 'Chờ duyệt' ?></span></td>
            <td><?= time_ago($c['created_at']) ?></td>
            <td class="row-actions">
              <?php if ($c['is_active']): ?>
                <form method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="unapprove">
                  <input type="hidden" name="comment_id" value="<?= (int)$c['comment_id'] ?>">
                  <input type="hidden" name="back_status" value="<?= e($status) ?>">
                  <input type="hidden" name="back_date_from" value="<?= e($dateFrom) ?>">
                  <input type="hidden" name="back_date_to" value="<?= e($dateTo) ?>">
                  <button type="submit" class="btn btn-outline btn-sm">Ẩn</button>
                </form>
              <?php else: ?>
                <form method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="approve">
                  <input type="hidden" name="comment_id" value="<?= (int)$c['comment_id'] ?>">
                  <input type="hidden" name="back_status" value="<?= e($status) ?>">
                  <input type="hidden" name="back_date_from" value="<?= e($dateFrom) ?>">
                  <input type="hidden" name="back_date_to" value="<?= e($dateTo) ?>">
                  <button type="submit" class="btn btn-primary btn-sm">Duyệt</button>
                </form>
              <?php endif; ?>
              <form method="post" onsubmit="return confirm('Xóa bình luận này?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="comment_id" value="<?= (int)$c['comment_id'] ?>">
                <input type="hidden" name="back_status" value="<?= e($status) ?>">
                <input type="hidden" name="back_date_from" value="<?= e($dateFrom) ?>">
                <input type="hidden" name="back_date_to" value="<?= e($dateTo) ?>">
                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
