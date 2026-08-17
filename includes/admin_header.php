<?php
$user = current_user();
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' . e(SITE_NAME) : e(SITE_NAME) ?> Quản trị</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(SITE_URL) ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a class="logo" href="<?= e(SITE_URL) ?>/index.php">
      <span class="logo-mark">S</span><span class="logo-text">ShareIt</span>
    </a>

    <nav class="admin-nav">
      <a class="<?= ($activeAdminNav ?? '') === 'resources' ? 'active' : '' ?>" href="<?= e(SITE_URL) ?>/staff/index.php">Quản lý tài nguyên</a>
      <?php if (is_admin()): ?>
        <a class="<?= ($activeAdminNav ?? '') === 'overview' ? 'active' : '' ?>" href="<?= e(SITE_URL) ?>/admin/index.php">Tổng quan</a>
        <a class="<?= ($activeAdminNav ?? '') === 'users' ? 'active' : '' ?>" href="<?= e(SITE_URL) ?>/admin/users.php">Người dùng</a>
        <a class="<?= ($activeAdminNav ?? '') === 'categories' ? 'active' : '' ?>" href="<?= e(SITE_URL) ?>/admin/categories.php">Danh mục</a>
        <a class="<?= ($activeAdminNav ?? '') === 'comments' ? 'active' : '' ?>" href="<?= e(SITE_URL) ?>/admin/comments.php">Bình luận</a>
      <?php endif; ?>
      <a href="<?= e(SITE_URL) ?>/index.php">&larr; Về trang chủ</a>
    </nav>