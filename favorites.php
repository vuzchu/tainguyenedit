<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$stmt = db()->prepare('SELECT p.* FROM favorites f JOIN project p ON p.project_id = f.project_id WHERE f.user_id = ? ORDER BY f.created_at DESC');
$stmt->execute([$user['user_id']]);
$projects = $stmt->fetchAll();
$favoritedIds = array_map('intval', array_column($projects, 'project_id'));

$pageTitle = t('favorites.title');
require __DIR__ . '/includes/header.php';
?>

<div class="page-header"><h1><?= t('favorites.title') ?></h1></div>

<?php if (empty($projects)): ?>
  <div class="empty-state">
    <h1>—</h1>
    <p><?= t('favorites.empty') ?></p>
    <a class="btn btn-primary" href="<?= e(SITE_URL) ?>/index.php"><?= t('favorites.explore') ?></a>
  </div>
<?php else: ?>
  <div class="resource-grid" style="padding-bottom:60px;">
    <?php foreach ($projects as $p): ?>
      <a class="resource-card" href="<?= e(SITE_URL) ?>/project.php?id=<?= (int)$p['project_id'] ?>">
        <div class="resource-thumb">
          <img src="<?= e($p['image'] ?: (SITE_URL . '/assets/img/placeholder.svg')) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
          <button type="button"
            class="fav-btn active"
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
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
