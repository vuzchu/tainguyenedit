<?php
require_once __DIR__ . '/includes/bootstrap.php';

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$where = ['status = :status'];
$params = ['status' => 'active'];
if ($categoryId > 0) {
    $where[] = 'category_id = :category_id';
    $params['category_id'] = $categoryId;
}
if ($q !== '') {
    $where[] = '(title LIKE :q1 OR author LIKE :q2)';
    $params['q1'] = '%' . $q . '%';
    $params['q2'] = '%' . $q . '%';
}
$whereSql = implode(' AND ', $where);

$countStmt = db()->prepare("SELECT COUNT(*) FROM project WHERE $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$stmt = db()->prepare("SELECT * FROM project WHERE $whereSql ORDER BY create_date DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll();

$countsByCategory = [];
foreach (db()->query("SELECT category_id, COUNT(*) c FROM project WHERE status = 'active' GROUP BY category_id") as $row) {
    $countsByCategory[(int)$row['category_id']] = (int)$row['c'];
}

$favoritedIds = [];
$user = current_user();
if ($user && $projects) {
    $ids = array_column($projects, 'project_id');
    $in = implode(',', array_fill(0, count($ids), '?'));
    $favStmt = db()->prepare("SELECT project_id FROM favorites WHERE user_id = ? AND project_id IN ($in)");
    $favStmt->execute(array_merge([$user['user_id']], $ids));
    $favoritedIds = array_map('intval', $favStmt->fetchAll(PDO::FETCH_COLUMN));
}

$pageTitle = $categoryId ? category_name($categoryId) : ($q !== '' ? t('home.search_results_for', ['q' => $q]) : null);
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <h1><?= $q !== '' ? t('home.search_results_for', ['q' => e($q)]) : ($categoryId ? e(category_name($categoryId)) : t('home.latest_title')) ?></h1>
  <span class="result-count"><?= t('home.resource_count', ['n' => $total]) ?></span>
</div>

<div class="layout">
  <div class="sidebar-col">
    <aside class="sidebar">
      <div class="sidebar-head">
        <h3><?= t('home.categories') ?></h3>
        <?php if ($categoryId || $q !== ''): ?>
          <a class="sidebar-clear" href="<?= e(SITE_URL) ?>/index.php"><?= t('home.clear_filter') ?></a>
        <?php endif; ?>
      </div>
      <ul class="sidebar-list">
        <li><a href="<?= e(SITE_URL) ?>/index.php" class="<?= $categoryId === 0 ? 'active' : '' ?>">
          <span class="check" aria-hidden="true"></span>
          <span class="label"><?= t('home.all_pill') ?></span>
          <span class="count"><?= array_sum($countsByCategory) ?></span>
        </a></li>
        <?php foreach (get_categories() as $cat): ?>
          <li><a href="<?= e(SITE_URL) ?>/index.php?category=<?= (int)$cat['category_id'] ?>" class="<?= $categoryId === (int)$cat['category_id'] ? 'active' : '' ?>">
            <span class="check" aria-hidden="true"></span>
            <span class="label"><?= e($cat['category_name']) ?></span>
            <span class="count"><?= $countsByCategory[(int)$cat['category_id']] ?? 0 ?></span>
          </a></li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <button type="button" class="btn btn-outline btn-block donate-trigger" id="donateTrigger">Donate me</button>
  </div>

  <div class="content">
    <?php if (empty($projects)): ?>
      <div class="empty-state">
        <h1>—</h1>
        <p><?= t('home.empty_title') ?></p>
      </div>
    <?php else: ?>
      <div class="resource-grid">
        <?php foreach ($projects as $p): ?>
          <a class="resource-card" href="<?= e(SITE_URL) ?>/project.php?id=<?= (int)$p['project_id'] ?>">
            <div class="resource-thumb">
              <img src="<?= e($p['image'] ?: (SITE_URL . '/assets/img/placeholder.svg')) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
              <span class="resource-cta"><?= t('home.view_detail') ?></span>
              <button type="button"
                class="fav-btn <?= in_array((int)$p['project_id'], $favoritedIds, true) ? 'active' : '' ?>"
                data-project-id="<?= (int)$p['project_id'] ?>"
                data-base-url="<?= e(SITE_URL) ?>"
                data-csrf="<?= e(csrf_token()) ?>"
                data-login-url="<?= e(SITE_URL) ?>/auth/login.php">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7.5-4.6-10-9.2C.4 8.4 2 4.5 6 4c2.2-.3 4 1 6 3.2C14 5 15.8 3.7 18 4c4 .5 5.6 4.4 4 7.8-2.5 4.6-10 9.2-10 9.2z"/></svg>
              </button>
            </div>
            <div class="resource-body">
              <span class="resource-cat"><?= e(category_name($p['category_id'])) ?></span>
              <h3 class="resource-title"><?= e($p['title']) ?></h3>
              <div class="resource-meta">
                <span><?= e($p['author'] ?: t('anonymous')) ?></span>
                <span><?= time_ago($p['create_date']) ?></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php
        $qs = ['category' => $categoryId ?: null, 'q' => $q ?: null];
        $qs = array_filter($qs, fn($v) => $v !== null && $v !== '');
        echo paginate_links($page, $totalPages, $qs);
      ?>
    <?php endif; ?>
  </div>
</div>

<div class="donate-modal" id="donateModal">
  <div class="donate-modal-backdrop" id="donateModalBackdrop"></div>
  <div class="donate-modal-box">
    <button type="button" class="donate-modal-close" id="donateModalClose" aria-label="<?= e(t('home.donate_close')) ?>">&times;</button>
    <h3>Donate me</h3>
    <div class="donate-info">
      <div class="donate-info-row">
        <span class="donate-label"><?= t('home.donate_bank') ?></span>
        <span class="donate-value">BIDV</span>
      </div>
      <div class="donate-info-row">
        <span class="donate-label"><?= t('home.donate_holder') ?></span>
        <span class="donate-value">CHU QUANG VU</span>
      </div>
      <div class="donate-info-row">
        <span class="donate-label"><?= t('home.donate_account') ?></span>
        <span class="donate-value" id="donateAccountNumber">8854188433</span>
        <button type="button" class="donate-copy-btn" id="donateCopyBtn" data-copy="8854188433" data-label="<?= e(t('home.donate_copy')) ?>" data-copied="<?= e(t('home.donate_copied')) ?>"><?= t('home.donate_copy') ?></button>
      </div>
    </div>
    <img src="https://i.ibb.co/FSHRqX8/536ccdea904611184857.jpg" alt="<?= e(t('home.donate_qr_alt')) ?>" class="donate-qr-img">
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
