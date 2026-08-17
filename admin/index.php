<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

$totalProjects = (int)db()->query('SELECT COUNT(*) FROM project')->fetchColumn();
$activeProjects = (int)db()->query("SELECT COUNT(*) FROM project WHERE status = 'active'")->fetchColumn();
$totalUsers = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalCategories = (int)db()->query('SELECT COUNT(*) FROM category')->fetchColumn();
$totalFavorites = (int)db()->query('SELECT COUNT(*) FROM favorites')->fetchColumn();

$newProjects30d = (int)db()->query('SELECT COUNT(*) FROM project WHERE create_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->fetchColumn();
$newUsers30d = (int)db()->query('SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->fetchColumn();

// Resources posted per month, last 6 months.
$monthly = [];
for ($i = 5; $i >= 0; $i--) {
    $monthly[date('Y-m', strtotime("-$i months"))] = 0;
}
$monthlyRows = db()->query("SELECT DATE_FORMAT(create_date, '%Y-%m') ym, COUNT(*) c FROM project WHERE create_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY ym")->fetchAll();
foreach ($monthlyRows as $row) {
    if (array_key_exists($row['ym'], $monthly)) {
        $monthly[$row['ym']] = (int)$row['c'];
    }
}
$maxMonthly = max(1, max($monthly));

// Resource count per category, ranked.
$catCounts = [];
foreach (db()->query('SELECT category_id, COUNT(*) c FROM project GROUP BY category_id') as $row) {
    $catCounts[(int)$row['category_id']] = (int)$row['c'];
}
$catList = [];
foreach (get_categories() as $cat) {
    $catList[] = ['name' => $cat['category_name'], 'count' => $catCounts[(int)$cat['category_id']] ?? 0];
}
usort($catList, fn($a, $b) => $b['count'] <=> $a['count']);
$maxCat = max(1, $catList[0]['count'] ?? 1);

$recentFeedback = db()->query('SELECT f.*, u.username FROM feedback f LEFT JOIN users u ON u.user_id = f.user_id ORDER BY f.created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Bảng điều khiển quản trị';
$activeAdminNav = 'overview';
require __DIR__ . '/../includes/admin_header.php';
require __DIR__ . '/../includes/admin_sidebar_end.php';
?>

<div class="page-header"><h1>Tổng quan</h1></div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="value"><?= $totalProjects ?></div>
    <div class="label">Tổng tài nguyên</div>
    <div class="delta">+<?= $newProjects30d ?> trong 30 ngày qua</div>
  </div>
  <div class="stat-card">
    <div class="value"><?= $activeProjects ?></div>
    <div class="label">Đang hoạt động</div>
  </div>
  <div class="stat-card">
    <div class="value"><?= $totalUsers ?></div>
    <div class="label">Người dùng</div>
    <div class="delta">+<?= $newUsers30d ?> trong 30 ngày qua</div>
  </div>
  <div class="stat-card">
    <div class="value"><?= $totalCategories ?></div>
    <div class="label">Danh mục</div>
  </div>
  <div class="stat-card">
    <div class="value"><?= $totalFavorites ?></div>
    <div class="label">Lượt yêu thích</div>
  </div>
</div>

<div class="charts-grid">
  <div class="chart-card">
    <h3>Tài nguyên đăng theo tháng</h3>
    <div class="bar-chart">
      <?php foreach ($monthly as $ym => $count): ?>
        <div class="bar-col">
          <div class="bar-track">
            <div class="bar-fill" style="height: <?= round($count / $maxMonthly * 100) ?>%" title="<?= e(date('m/Y', strtotime($ym . '-01'))) ?>: <?= $count ?> tài nguyên"></div>
          </div>
          <span class="bar-label"><?= e(date('m/y', strtotime($ym . '-01'))) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="chart-card">
    <h3>Tài nguyên theo danh mục</h3>
    <div class="hbar-list">
      <?php foreach ($catList as $cat): ?>
        <div class="hbar-row">
          <span class="hbar-label"><?= e($cat['name']) ?></span>
          <div class="hbar-track">
            <div class="hbar-fill" style="width: <?= round($cat['count'] / $maxCat * 100) ?>%"></div>
          </div>
          <span class="hbar-value"><?= $cat['count'] ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
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

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
