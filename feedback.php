<?php
require_once __DIR__ . '/includes/bootstrap.php';

$errors = [];
$sent = false;
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $text = trim($_POST['feedback_text'] ?? '');
    if ($text === '') {
        $errors[] = 'Vui lòng nhập nội dung góp ý.';
    } elseif (mb_strlen($text) > 2000) {
        $errors[] = 'Nội dung góp ý quá dài.';
    } else {
        $stmt = db()->prepare('INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)');
        $stmt->execute([$user['user_id'] ?? null, $text]);
        $sent = true;
    }
}

$pageTitle = 'Gửi góp ý';
require __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <h1>Gửi góp ý</h1>
  <p class="form-subtitle">Chia sẻ ý kiến của bạn để <?= e(SITE_NAME) ?> tốt hơn</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>
  <?php if ($sent): ?>
    <div class="alert alert-success">Cảm ơn bạn đã gửi góp ý!</div>
  <?php endif; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-group">
      <label for="feedback_text">Nội dung góp ý</label>
      <textarea class="form-control" id="feedback_text" name="feedback_text" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Gửi góp ý</button>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
