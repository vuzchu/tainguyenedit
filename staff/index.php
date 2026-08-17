<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_STAFF, ROLE_ADMIN]);

$q = trim($_GET['q'] ?? '');
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');
$sort = $_GET['sort'] ?? 'date_desc';
$allowedSorts = ['date_desc', 'date_asc', 'title_asc', 'title_desc'];
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'date_desc';
}
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(title LIKE :q OR author LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
if ($categoryId > 0) {
    $where[] = 'category_id = :category_id';
    $params['category_id'] = $categoryId;
}
if ($dateFrom !== '') {
    $where[] = 'DATE(create_date) >= :date_from';
    $params['date_from'] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'DATE(create_date) <= :date_to';
    $params['date_to'] = $dateTo;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$orderMap = [
    'date_desc' => 'create_date DESC',
    'date_asc' => 'create_date ASC',
    'title_asc' => 'title ASC',
    'title_desc' => 'title DESC',
];
$orderSql = $orderMap[$sort];

$countStmt = db()->prepare("SELECT COUNT(*) FROM project $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$stmt = db()->prepare("SELECT * FROM project $whereSql ORDER BY $orderSql LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll();

function sortable_header(string $label, string $field, string $sort, array $baseQs): string
{
    $ascKey = $field . '_asc';
    $descKey = $field . '_desc';
    $next = $sort === $descKey ? $ascKey : $descKey;
    $arrow = $sort === $ascKey ? ' &uarr;' : ($sort === $descKey ? ' &darr;' : '');
    $qs = $baseQs;
    $qs['sort'] = $next !== 'date_desc' ? $next : null;
    $qs = array_filter($qs, fn($v) => $v !== null && $v !== '');
    return '<a href="?' . e(http_build_query($qs)) . '">' . e($label) . $arrow . '</a>';
}

$baseQs = array_filter([
    'q' => $q ?: null,
    'category' => $categoryId ?: null,
    'date_from' => $dateFrom ?: null,
    'date_to' => $dateTo ?: null,
], fn($v) => $v !== null && $v !== '');

$pageTitle = 'Quản lý tài nguyên';
$activeAdminNav = 'resources';
require __DIR__ . '/../includes/admin_header.php';
require __DIR__ . '/../includes/admin_sidebar_end.php';
?>

      <div class="page-header">
        <h1>Quản lý tài nguyên</h1>
        <a class="btn btn-primary btn-sm" href="<?= e(SITE_URL) ?>/staff/project_new.php">+ Đăng tài nguyên mới</a>
      </div>
      <p class="result-count"><?= $total ?> tài nguyên</p>

      <form class="resource-filters" method="get">
        <input type="text" name="q" placeholder="Tiêu đề, tác giả..." value="<?= e($q) ?>">
        <select name="category">
          <option value="0">Tất cả danh mục</option>
          <?php foreach (get_categories() as $cat): ?>
            <option value="<?= (int)$cat['category_id'] ?>" <?= $categoryId === (int)$cat['category_id'] ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="date" name="date_from" value="<?= e($dateFrom) ?>" title="Từ ngày">
        <input type="date" name="date_to" value="<?= e($dateTo) ?>" title="Đến ngày">
        <select name="sort">
          <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Ngày đăng: mới nhất</option>
          <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Ngày đăng: cũ nhất</option>
          <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Tên: A &rarr; Z</option>
          <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Tên: Z &rarr; A</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
        <?php if ($q !== '' || $categoryId > 0 || $dateFrom !== '' || $dateTo !== '' || $sort !== 'date_desc'): ?>
          <a class="sidebar-clear" href="<?= e(SITE_URL) ?>/staff/index.php">Xóa lọc</a>
        <?php endif; ?>
      </form>

      <?php if (empty($projects)): ?>
        <div class="empty-state">
          <h1>—</h1>
          <p>Không tìm thấy tài nguyên phù hợp.</p>
        </div>
      <?php else: ?>
        <div class="table-card">
          <table class="data-table">
            <thead>
              <tr>
                <th><?= sortable_header('Tài nguyên', 'title', $sort, $baseQs) ?></th>
                <th>Danh mục</th>
                <th>Trạng thái</th>
                <th><?= sortable_header('Ngày đăng', 'date', $sort, $baseQs) ?></th>
                <th>Thao tác</th>
              </tr>
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
        <?php
          $qs = ['q' => $q ?: null, 'category' => $categoryId ?: null, 'date_from' => $dateFrom ?: null, 'date_to' => $dateTo ?: null, 'sort' => $sort !== 'date_desc' ? $sort : null];
          $qs = array_filter($qs, fn($v) => $v !== null && $v !== '');
          echo paginate_links($page, $totalPages, $qs);
        ?>
      <?php endif; ?>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
