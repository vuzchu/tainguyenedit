</div>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <a class="logo logo-light" href="<?= e(SITE_URL) ?>/index.php">
        <span class="logo-mark">S</span><span class="logo-text"><?= e(SITE_NAME) ?></span>
      </a>
      <p><?= t('footer.blurb') ?></p>
    </div>
    <div class="footer-col">
      <h4><?= t('footer.categories') ?></h4>
      <ul>
        <?php foreach (get_categories() as $cat): ?>
          <li><a href="<?= e(SITE_URL) ?>/index.php?category=<?= (int)$cat['category_id'] ?>"><?= e($cat['category_name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4><?= t('footer.account') ?></h4>
      <ul>
        <li><a href="<?= e(SITE_URL) ?>/auth/login.php"><?= t('nav.login') ?></a></li>
        <li><a href="<?= e(SITE_URL) ?>/auth/register.php"><?= t('nav.register') ?></a></li>
        <li><a href="<?= e(SITE_URL) ?>/feedback.php"><?= t('nav.feedback') ?></a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4><?= t('footer.info') ?></h4>
      <ul>
        <li><a href="<?= e(SITE_URL) ?>/index.php"><?= t('footer.all_resources') ?></a></li>
        <li><a href="<?= e(SITE_URL) ?>/staff/project_new.php">Đóng góp</a></li>
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
