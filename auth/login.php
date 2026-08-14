<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (is_logged_in()) {
    redirect(SITE_URL . '/index.php');
}

$redirectTo = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$identifier, $identifier]);
    $userRow = $stmt->fetch();

    if (!$userRow || !password_verify($password, $userRow['password'])) {
        $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    } else {
        login_user($userRow);
        $target = $redirectTo !== '' ? urldecode($redirectTo) : (SITE_URL . '/index.php');
        if (!str_starts_with($target, SITE_URL)) {
            $target = SITE_URL . '/index.php';
        }
        redirect($target);
    }
}

$pageTitle = 'Đăng nhập';
require __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
  <h1>Chào mừng trở lại</h1>
  <p class="form-subtitle">Đăng nhập để lưu tài nguyên yêu thích và đăng bài</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <a class="btn-google" href="<?= e(SITE_URL) ?>/auth/google_login.php<?= $redirectTo ? '?redirect=' . e($redirectTo) : '' ?>">
    <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.9-2.26c-.8.54-1.84.86-3.06.86-2.35 0-4.34-1.59-5.05-3.72H.98v2.33A9 9 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.95 10.7A5.4 5.4 0 0 1 3.66 9c0-.59.1-1.16.29-1.7V4.97H.98A9 9 0 0 0 0 9c0 1.45.35 2.83.98 4.03l2.97-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.51.46 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0A9 9 0 0 0 .98 4.97l2.97 2.33C4.66 5.17 6.65 3.58 9 3.58z"/></svg>
    Đăng nhập với Google
  </a>
  <div class="form-divider">hoặc</div>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="redirect" value="<?= e($redirectTo) ?>">
    <div class="form-group">
      <label for="identifier">Tên đăng nhập hoặc email</label>
      <input class="form-control" type="text" id="identifier" name="identifier" value="<?= e($identifier) ?>" required autofocus>
    </div>
    <div class="form-group">
      <label for="password">Mật khẩu</label>
      <input class="form-control" type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
  </form>

  <div class="form-footer-link">Chưa có tài khoản? <a href="<?= e(SITE_URL) ?>/auth/register.php"><strong>Đăng ký ngay</strong></a></div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
