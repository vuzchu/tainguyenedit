<?php
$user = current_user();
$categories = get_categories();
$activeCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$searchQuery = trim($_GET['q'] ?? '');
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' . e(SITE_NAME) : e(SITE_NAME) . ' — Chia sẻ tài nguyên editing' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(SITE_URL) ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="<?= e(SITE_URL) ?>/index.php">
      <span class="logo-mark">S</span><span class="logo-text"><?= e(SITE_NAME) ?></span>
    </a>

    <form class="search-form" action="<?= e(SITE_URL) ?>/index.php" method="get">
      <select name="category" class="search-category">
        <option value="0">Tất cả danh mục</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['category_id'] ?>" <?= $activeCategory === (int)$cat['category_id'] ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="q" placeholder="Tìm kiếm tài nguyên..." value="<?= e($searchQuery) ?>">
      <button type="submit" aria-label="Tìm kiếm">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </form>

    <nav class="header-actions">
      <?php if ($user): ?>
        <?php if (is_staff()): ?>
          <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/staff/project_new.php">+ Đăng tài nguyên</a>
        <?php endif; ?>
        <div class="account-menu">
          <button type="button" class="account-trigger" id="accountTrigger">
            <span class="avatar-circle"><?= e(initials($user['full_name'])) ?></span>
            <span class="account-name"><?= e($user['full_name']) ?></span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <div class="account-dropdown" id="accountDropdown">
            <a href="<?= e(SITE_URL) ?>/favorites.php">Mục yêu thích</a>
            <?php if (is_staff()): ?>
              <a href="<?= e(SITE_URL) ?>/staff/index.php">Tài nguyên của tôi</a>
            <?php endif; ?>
            <?php if (is_admin()): ?>
              <a href="<?= e(SITE_URL) ?>/admin/index.php">Quản trị hệ thống</a>
            <?php endif; ?>
            <a href="<?= e(SITE_URL) ?>/feedback.php">Gửi góp ý</a>
            <hr>
            <a href="<?= e(SITE_URL) ?>/auth/logout.php">Đăng xuất</a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/auth/login.php">Đăng nhập</a>
        <a class="btn btn-primary btn-sm" href="<?= e(SITE_URL) ?>/auth/register.php">Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>
<div class="container">
<?php foreach (get_flashes() as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endforeach; ?>
</div>
