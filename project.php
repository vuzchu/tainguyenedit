<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM project WHERE project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy';
    require __DIR__ . '/includes/header.php';
    echo '<div class="empty-state"><h1>404</h1><p>Tài nguyên không tồn tại hoặc đã bị gỡ.</p>
          <a class="btn btn-primary" href="' . e(SITE_URL) . '/index.php">Về trang chủ</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$user = current_user();
$isFavorited = false;
if ($user) {
    $favStmt = db()->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND project_id = ?');
    $favStmt->execute([$user['user_id'], $id]);
    $isFavorited = (bool)$favStmt->fetchColumn();
}

$relStmt = db()->prepare("SELECT * FROM project WHERE category_id = ? AND project_id != ? AND status = 'active' ORDER BY create_date DESC LIMIT 4");
$relStmt->execute([$project['category_id'], $id]);
$related = $relStmt->fetchAll();

$pageTitle = $project['title'];
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <a href="<?= e(SITE_URL) ?>/index.php">Trang chủ</a> &rsaquo;
  <a href="<?= e(SITE_URL) ?>/index.php?category=<?= (int)$project['category_id'] ?>"><?= e(category_name($project['category_id'])) ?></a> &rsaquo;
  <span><?= e($project['title']) ?></span>
</div>

<div class="detail-layout">
  <div class="detail-image">
    <img src="<?= e($project['image'] ?: (SITE_URL . '/assets/img/placeholder.svg')) ?>" alt="<?= e($project['title']) ?>">
  </div>

  <div class="detail-info">
    <span class="badge badge-outline"><?= e(category_name($project['category_id'])) ?></span>
    <h1><?= e($project['title']) ?></h1>
    <div class="detail-author">
      <span class="avatar-circle"><?= e(initials($project['author'] ?: 'A')) ?></span>
      <span>Đăng bởi <strong><?= e($project['author'] ?: 'Ẩn danh') ?></strong> · <?= time_ago($project['create_date']) ?></span>
    </div>

    <div class="detail-actions">
      <a class="btn btn-primary btn-block" href="<?= e($project['source']) ?>" target="_blank" rel="noopener noreferrer nofollow">
        Tải xuống / Xem nguồn
      </a>
      <button type="button"
        class="btn btn-outline fav-btn <?= $isFavorited ? 'active' : '' ?>"
        data-project-id="<?= (int)$project['project_id'] ?>"
        data-base-url="<?= e(SITE_URL) ?>"
        data-csrf="<?= e(csrf_token()) ?>"
        data-login-url="<?= e(SITE_URL) ?>/auth/login.php"
        aria-label="Yêu thích">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7.5-4.6-10-9.2C.4 8.4 2 4.5 6 4c2.2-.3 4 1 6 3.2C14 5 15.8 3.7 18 4c4 .5 5.6 4.4 4 7.8-2.5 4.6-10 9.2-10 9.2z"/></svg>
      </button>
    </div>

    <div class="detail-block">
      <h3>Mô tả</h3>
      <div class="detail-description"><?= clean_html($project['description']) ?></div>
    </div>

    <div class="detail-block">
      <h3>Thông tin</h3>
      <dl class="detail-facts">
        <div><dt>Danh mục</dt><dd><?= e(category_name($project['category_id'])) ?></dd></div>
        <div><dt>Trạng thái</dt><dd><?= $project['status'] === 'active' ? 'Đang hoạt động' : 'Đã tắt' ?></dd></div>
        <div><dt>Ngày đăng</dt><dd><?= date('d/m/Y', strtotime($project['create_date'])) ?></dd></div>
        <div><dt>Cập nhật</dt><dd><?= date('d/m/Y', strtotime($project['update_date'])) ?></dd></div>
      </dl>
    </div>
  </div>
</div>

<?php if ($related): ?>
  <div class="section-heading"><h2>Có thể bạn cũng thích</h2></div>
  <div class="resource-grid" style="padding-bottom:60px;">
    <?php foreach ($related as $p): ?>
      <a class="resource-card" href="<?= e(SITE_URL) ?>/project.php?id=<?= (int)$p['project_id'] ?>">
        <div class="resource-thumb">
          <img src="<?= e($p['image'] ?: (SITE_URL . '/assets/img/placeholder.svg')) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
        </div>
        <div class="resource-body">
          <span class="resource-cat"><?= e(category_name($p['category_id'])) ?></span>
          <h3 class="resource-title"><?= e($p['title']) ?></h3>
          <div class="resource-meta">
            <span><?= e($p['author'] ?: 'Ẩn danh') ?></span>
            <span><?= time_ago($p['create_date']) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
