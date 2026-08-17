<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (is_logged_in()) {
    redirect(SITE_URL . '/index.php');
}

$errors = [];
$old = ['username' => '', 'email' => '', 'full_name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $old['username'] = trim($_POST['username'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($old['username'] === '' || !preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $old['username'])) {
        $errors[] = t('auth.err_username');
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = t('auth.err_email');
    }
    if ($old['full_name'] === '') {
        $errors[] = t('auth.err_full_name');
    }
    if (strlen($password) < 6) {
        $errors[] = t('auth.err_password_length');
    }
    if ($password !== $confirm) {
        $errors[] = t('auth.err_password_mismatch');
    }

    if (!$errors) {
        $check = db()->prepare('SELECT 1 FROM users WHERE username = ? OR email = ?');
        $check->execute([$old['username'], $old['email']]);
        if ($check->fetchColumn()) {
            $errors[] = t('auth.err_taken');
        }
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO users (username, email, password, full_name, role_id) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $old['username'],
            $old['email'],
            password_hash($password, PASSWORD_BCRYPT),
            $old['full_name'],
            ROLE_CUSTOMER,
        ]);
        $userId = (int)db()->lastInsertId();
        $userStmt = db()->prepare('SELECT * FROM users WHERE user_id = ?');
        $userStmt->execute([$userId]);
        login_user($userStmt->fetch());
        flash('success', t('auth.register_success', ['site' => SITE_NAME]));
        redirect(SITE_URL . '/index.php');
    }
}

$pageTitle = t('nav.register');
require __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
  <h1><?= t('auth.register_title') ?></h1>
  <p class="form-subtitle"><?= t('auth.register_subtitle') ?></p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <a class="btn-google" href="<?= e(SITE_URL) ?>/auth/google_login.php">
    <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.9-2.26c-.8.54-1.84.86-3.06.86-2.35 0-4.34-1.59-5.05-3.72H.98v2.33A9 9 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.95 10.7A5.4 5.4 0 0 1 3.66 9c0-.59.1-1.16.29-1.7V4.97H.98A9 9 0 0 0 0 9c0 1.45.35 2.83.98 4.03l2.97-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.51.46 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0A9 9 0 0 0 .98 4.97l2.97 2.33C4.66 5.17 6.65 3.58 9 3.58z"/></svg>
    <?= t('auth.google_register') ?>
  </a>
  <div class="form-divider"><?= t('auth.or') ?></div>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-group">
      <label for="username"><?= t('auth.username') ?></label>
      <input class="form-control" type="text" id="username" name="username" value="<?= e($old['username']) ?>" required>
    </div>
    <div class="form-group">
      <label for="full_name"><?= t('auth.full_name') ?></label>
      <input class="form-control" type="text" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" required>
    </div>
    <div class="form-group">
      <label for="email"><?= t('auth.email') ?></label>
      <input class="form-control" type="email" id="email" name="email" value="<?= e($old['email']) ?>" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="password"><?= t('auth.password') ?></label>
        <input class="form-control" type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="confirm_password"><?= t('auth.confirm_password') ?></label>
        <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-block"><?= t('auth.register_button') ?></button>
  </form>

  <div class="form-footer-link"><?= t('auth.have_account') ?> <a href="<?= e(SITE_URL) ?>/auth/login.php"><strong><?= t('nav.login') ?></strong></a></div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
