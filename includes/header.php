<?php
$user = current_user();
$categories = get_categories();
$activeCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$searchQuery = trim($_GET['q'] ?? '');

$currentLang = current_lang();
$currentUrl = parse_url($_SERVER['REQUEST_URI'] ?? (SITE_URL . '/index.php'));
parse_str($currentUrl['query'] ?? '', $currentQuery);
$langUrl = function (string $lang) use ($currentUrl, $currentQuery): string {
    $qs = $currentQuery;
    $qs['lang'] = $lang;
    return ($currentUrl['path'] ?? SITE_URL . '/index.php') . '?' . http_build_query($qs);
};

$metaTitle = isset($pageTitle) ? $pageTitle . ' — ' . SITE_NAME : SITE_NAME . ' — ' . t('site.tagline');
$metaDescription = $pageDescription ?? t('site.tagline');
$metaImage = $pageImage ?? 'https://i.ibb.co/SDq5Vnk9/Screenshot-2026-08-16-231217.png';
$metaUrl = SITE_ORIGIN . ($_SERVER['REQUEST_URI'] ?? SITE_URL . '/index.php');
?>
<!doctype html>
<html lang="<?= e($currentLang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($metaTitle) ?></title>
<meta name="description" content="<?= e($metaDescription) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:title" content="<?= e($metaTitle) ?>">
<meta property="og:description" content="<?= e($metaDescription) ?>">
<meta property="og:image" content="<?= e($metaImage) ?>">
<meta property="og:url" content="<?= e($metaUrl) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($metaTitle) ?>">
<meta name="twitter:description" content="<?= e($metaDescription) ?>">
<meta name="twitter:image" content="<?= e($metaImage) ?>">
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
        <option value="0"><?= t('nav.all_categories') ?></option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['category_id'] ?>" <?= $activeCategory === (int)$cat['category_id'] ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="q" placeholder="<?= e(t('nav.search_placeholder')) ?>" value="<?= e($searchQuery) ?>">
      <button type="submit" aria-label="<?= e(t('nav.search')) ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </form>

    <nav class="header-actions">
      <div class="lang-menu">
        <button type="button" class="lang-trigger" id="langTrigger">
          <span><?= strtoupper($currentLang) ?></span>
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="lang-dropdown" id="langDropdown">
          <a href="<?= e($langUrl('vi')) ?>" class="<?= $currentLang === 'vi' ? 'active' : '' ?>">Tiếng Việt<?= $currentLang === 'vi' ? ' <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>' : '' ?></a>
          <a href="<?= e($langUrl('en')) ?>" class="<?= $currentLang === 'en' ? 'active' : '' ?>">English<?= $currentLang === 'en' ? ' <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>' : '' ?></a>
        </div>
      </div>
      <?php if ($user): ?>
        <?php if (is_staff()): ?>
          <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/staff/project_new.php">+ Đóng góp</a>
        <?php else: ?>
          <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/submit_project.php"><?= t('nav.submit_resource') ?></a>
        <?php endif; ?>
        <div class="account-menu">
          <button type="button" class="account-trigger" id="accountTrigger">
            <span class="avatar-circle"><?= e(initials($user['full_name'])) ?></span>
            <span class="account-name"><?= e($user['full_name']) ?></span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <div class="account-dropdown" id="accountDropdown">
            <a href="<?= e(SITE_URL) ?>/favorites.php"><?= t('nav.favorites') ?></a>
            <?php if (is_staff()): ?>
              <a href="<?= e(SITE_URL) ?>/staff/index.php">Quản lý tài nguyên</a>
            <?php endif; ?>
            <?php if (is_admin()): ?>
              <a href="<?= e(SITE_URL) ?>/admin/index.php">Quản trị hệ thống</a>
            <?php endif; ?>
            <a href="<?= e(SITE_URL) ?>/feedback.php"><?= t('nav.feedback') ?></a>
            <hr>
            <a href="<?= e(SITE_URL) ?>/auth/logout.php"><?= t('nav.logout') ?></a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/feedback.php"><?= t('nav.feedback_short') ?></a>
        <a class="btn btn-outline btn-sm" href="<?= e(SITE_URL) ?>/auth/login.php"><?= t('nav.login') ?></a>
        <a class="btn btn-primary btn-sm" href="<?= e(SITE_URL) ?>/auth/register.php"><?= t('nav.register') ?></a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>
<div class="container">
<?php foreach (get_flashes() as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endforeach; ?>
