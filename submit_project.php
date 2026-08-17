<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$errors = [];
$old = ['title' => '', 'category_id' => '', 'author' => $user['full_name'], 'description' => '', 'source' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $old['title'] = trim($_POST['title'] ?? '');
    $old['category_id'] = (int)($_POST['category_id'] ?? 0);
    $old['author'] = trim($_POST['author'] ?? '') ?: $user['full_name'];
    $old['description'] = trim($_POST['description'] ?? '');
    $old['source'] = trim($_POST['source'] ?? '');

    if ($old['title'] === '') $errors[] = t('submit.err_title');
    if ($old['category_id'] <= 0) $errors[] = t('submit.err_category');
    if ($old['source'] === '' || !filter_var($old['source'], FILTER_VALIDATE_URL)) $errors[] = t('submit.err_source');

    $imageUrl = trim($_POST['cover_url'] ?? '');
    if ($imageUrl === '') {
        $errors[] = t('submit.err_cover_required');
    } elseif (!is_valid_cover_url($imageUrl)) {
        $errors[] = t('submit.err_cover_upload');
    }

    if (!$errors) {
        $description = clean_html($old['description']);
        // Community submissions always start disabled — staff/admin review and
        // flip status to active from the existing edit page.
        $stmt = db()->prepare('INSERT INTO project (title, description, author, status, source, category_id, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $old['title'],
            $description,
            $old['author'],
            'disable',
            $old['source'],
            $old['category_id'],
            $imageUrl,
        ]);
        $newId = (int)db()->lastInsertId();
        flash('success', t('submit.success'));
        redirect(SITE_URL . '/project.php?id=' . $newId);
    }
}

$pageTitle = t('submit.title');
require __DIR__ . '/includes/header.php';
?>

<div class="form-card wide">
  <h1><?= t('submit.title') ?></h1>
  <p class="form-subtitle"><?= t('submit.subtitle') ?></p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="cover_url" id="coverUrlInput" value="">
    <div class="form-group">
      <label for="title"><?= t('submit.field_title') ?></label>
      <input class="form-control" type="text" id="title" name="title" value="<?= e($old['title']) ?>" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="category_id"><?= t('submit.field_category') ?></label>
        <select class="form-control" id="category_id" name="category_id" required>
          <option value=""><?= t('submit.field_category_placeholder') ?></option>
          <?php foreach (get_categories() as $cat): ?>
            <option value="<?= (int)$cat['category_id'] ?>" <?= $old['category_id'] === (int)$cat['category_id'] ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="author"><?= t('submit.field_author') ?></label>
        <input class="form-control" type="text" id="author" name="author" value="<?= e($old['author']) ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="source"><?= t('submit.field_source') ?></label>
      <input class="form-control" type="url" id="source" name="source" value="<?= e($old['source']) ?>" placeholder="https://..." required>
    </div>
    <div class="form-group">
      <label for="description"><?= t('submit.field_description') ?></label>
      <textarea class="form-control" id="description" name="description" placeholder="<?= e(t('submit.field_description_placeholder')) ?>"><?= e($old['description']) ?></textarea>
    </div>
    <div class="form-group">
      <label for="coverInput"><?= t('submit.field_cover') ?></label>
      <div class="upload-drop">
        <input type="file" id="coverInput" accept="image/*" data-imgbb-key="<?= e(IMGBB_API_KEY) ?>" required>
        <span class="hint"><?= t('submit.field_cover_hint') ?></span>
        <span id="coverUploadStatus" class="hint"></span>
        <img id="coverPreview" class="upload-preview" style="display:none;" alt="Preview">
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-block"><?= t('submit.submit_button') ?></button>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
