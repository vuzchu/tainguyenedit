<?php
require_once __DIR__ . '/includes/bootstrap.php';

$errors = [];
$sent = false;
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $text = trim($_POST['feedback_text'] ?? '');
    if ($text === '') {
        $errors[] = t('feedback.err_empty');
    } elseif (mb_strlen($text) > 2000) {
        $errors[] = t('feedback.err_too_long');
    } else {
        $stmt = db()->prepare('INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)');
        $stmt->execute([$user['user_id'] ?? null, $text]);
        $sent = true;
    }
}

$pageTitle = t('feedback.title');
require __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <h1><?= t('feedback.title') ?></h1>
  <p class="form-subtitle"><?= t('feedback.subtitle', ['site' => e(SITE_NAME)]) ?></p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>
  <?php if ($sent): ?>
    <div class="alert alert-success"><?= t('feedback.thanks') ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-group">
      <label for="feedback_text"><?= t('feedback.label') ?></label>
      <textarea class="form-control" id="feedback_text" name="feedback_text" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary btn-block"><?= t('feedback.submit') ?></button>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
