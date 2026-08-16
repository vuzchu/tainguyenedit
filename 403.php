<?php if (!defined('DB_HOST')) { require_once __DIR__ . '/includes/bootstrap.php'; } ?>
<?php $pageTitle = 'Không có quyền truy cập'; require __DIR__ . '/includes/header.php'; ?>
<div class="empty-state">
  <h1>403</h1>
  <p>Bạn không có quyền truy cập trang này.</p>
  <a class="btn btn-primary" href="<?= e(SITE_URL) ?>/index.php">Về trang chủ</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
