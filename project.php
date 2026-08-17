<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM project WHERE project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    $pageTitle = t('detail.not_found_title');
    require __DIR__ . '/includes/header.php';
    echo '<div class="empty-state"><h1>404</h1><p>' . e(t('detail.not_found_text')) . '</p>
          <a class="btn btn-primary" href="' . e(SITE_URL) . '/index.php">' . e(t('detail.back_home')) . '</a></div>';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    require_login();
    csrf_verify();
    $commentText = trim($_POST['comment_text']);
    if ($commentText === '') {
        flash('error', t('comments.err_empty'));
    } elseif (mb_strlen($commentText) > 2000) {
        flash('error', t('comments.err_too_long'));
    } else {
        $ins = db()->prepare('INSERT INTO comments (project_id, user_id, comment_text) VALUES (?, ?, ?)');
        $ins->execute([$id, $user['user_id'], $commentText]);
        flash('success', t('comments.pending_notice'));
    }
    redirect(SITE_URL . '/project.php?id=' . $id . '#comments');
}

$commentsStmt = db()->prepare('SELECT c.*, u.full_name FROM comments c JOIN users u ON u.user_id = c.user_id WHERE c.project_id = ? AND c.is_active = 1 ORDER BY c.created_at DESC');
$commentsStmt->execute([$id]);
$comments = $commentsStmt->fetchAll();

$relStmt = db()->prepare("SELECT * FROM project WHERE category_id = ? AND project_id != ? AND status = 'active' ORDER BY create_date DESC LIMIT 4");
$relStmt->execute([$project['category_id'], $id]);
$related = $relStmt->fetchAll();

$pageTitle = $project['title'];
$pageDescription = excerpt_html($project['description'], 160);
if ($project['image']) {
    $pageImage = $project['image'];
}
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <a href="<?= e(SITE_URL) ?>/index.php"><?= t('detail.home') ?></a> &rsaquo;
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
      <span><?= t('detail.posted_by') ?> <strong><?= e($project['author'] ?: t('anonymous')) ?></strong> · <?= time_ago($project['create_date']) ?></span>
    </div>

    <div class="detail-actions">
      <a class="btn btn-primary btn-block" href="<?= e($project['source']) ?>" target="_blank" rel="noopener noreferrer nofollow">
        <?= t('detail.download') ?>
      </a>
      <button type="button"
        class="btn btn-outline fav-btn <?= $isFavorited ? 'active' : '' ?>"
        data-project-id="<?= (int)$project['project_id'] ?>"
        data-base-url="<?= e(SITE_URL) ?>"
        data-csrf="<?= e(csrf_token()) ?>"
        data-login-url="<?= e(SITE_URL) ?>/auth/login.php"
        aria-label="<?= e(t('detail.favorite')) ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7.5-4.6-10-9.2C.4 8.4 2 4.5 6 4c2.2-.3 4 1 6 3.2C14 5 15.8 3.7 18 4c4 .5 5.6 4.4 4 7.8-2.5 4.6-10 9.2-10 9.2z"/></svg>
      </button>
    </div>

    <div class="detail-block">
      <h3><?= t('detail.description') ?></h3>
      <div class="detail-description"><?= nl2br(clean_html($project['description'])) ?></div>
    </div>

    <div class="detail-block">
      <h3><?= t('detail.info') ?></h3>
      <dl class="detail-facts">
        <div><dt><?= t('detail.category') ?></dt><dd><?= e(category_name($project['category_id'])) ?></dd></div>
        <div><dt><?= t('detail.status') ?></dt><dd><?= $project['status'] === 'active' ? t('detail.status_active') : t('detail.status_disable') ?></dd></div>
        <div><dt><?= t('detail.posted_date') ?></dt><dd><?= date('d/m/Y', strtotime($project['create_date'])) ?></dd></div>
        <div><dt><?= t('detail.updated_date') ?></dt><dd><?= date('d/m/Y', strtotime($project['update_date'])) ?></dd></div>
      </dl>
    </div>
  </div>
</div>

<div class="comments-section" id="comments">
  <div class="section-heading"><h2><?= t('comments.title', ['n' => count($comments)]) ?></h2></div>

  <?php if ($user): ?>
    <form method="post" class="comment-form">
      <?= csrf_field() ?>
      <textarea class="form-control" name="comment_text" placeholder="<?= e(t('comments.placeholder')) ?>" required></textarea>
      <button type="submit" class="btn btn-primary btn-sm"><?= t('comments.submit') ?></button>
    </form>
  <?php else: ?>
    <p class="comment-login-prompt">
      <a href="<?= e(SITE_URL) ?>/auth/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><?= t('comments.login_prompt') ?></a> <?= t('comments.login_suffix') ?>
    </p>
  <?php endif; ?>

  <div class="comment-list">
    <?php if (empty($comments)): ?>
      <p class="form-subtitle"><?= t('comments.empty') ?></p>
    <?php endif; ?>
    <?php foreach ($comments as $c): ?>
      <div class="comment-item">
        <span class="avatar-circle"><?= e(initials($c['full_name'])) ?></span>
        <div class="comment-body">
          <div class="comment-meta"><strong><?= e($c['full_name']) ?></strong><span><?= time_ago($c['created_at']) ?></span></div>
          <p><?= nl2br(e($c['comment_text'])) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($related): ?>
  <div class="section-heading"><h2><?= t('detail.related') ?></h2></div>
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
            <span><?= e($p['author'] ?: t('anonymous')) ?></span>
            <span><?= time_ago($p['create_date']) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
