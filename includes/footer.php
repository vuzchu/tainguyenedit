</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <a class="logo logo-light" href="<?= e(SITE_URL) ?>/index.php">
        <span class="logo-mark">S</span><span class="logo-text"><?= e(SITE_NAME) ?></span>
      </a>
      <p>Nơi chia sẻ pack chỉnh sửa, project file, mask, plugin After Effects và raw cut miễn phí cho cộng đồng editor.</p>
    </div>
    <div class="footer-col">
      <h4>Danh mục</h4>
      <ul>
        <?php foreach (get_categories() as $cat): ?>
          <li><a href="<?= e(SITE_URL) ?>/index.php?category=<?= (int)$cat['category_id'] ?>"><?= e($cat['category_name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Tài khoản</h4>
      <ul>
        <li><a href="<?= e(SITE_URL) ?>/auth/login.php">Đăng nhập</a></li>
        <li><a href="<?= e(SITE_URL) ?>/auth/register.php">Đăng ký</a></li>
        <li><a href="<?= e(SITE_URL) ?>/feedback.php">Gửi góp ý</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Thông tin</h4>
      <ul>
        <li><a href="<?= e(SITE_URL) ?>/index.php">Tất cả tài nguyên</a></li>
        <li><a href="<?= e(SITE_URL) ?>/staff/project_new.php">Đăng tài nguyên</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</div>
  </div>
</footer>

<script src="<?= e(SITE_URL) ?>/assets/js/main.js"></script>
</body>
</html>
