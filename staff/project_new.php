<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_STAFF, ROLE_ADMIN]);

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

    if ($old['title'] === '') $errors[] = 'Vui lòng nhập tiêu đề.';
    if ($old['category_id'] <= 0) $errors[] = 'Vui lòng chọn danh mục.';
    if ($old['source'] === '' || !filter_var($old['source'], FILTER_VALIDATE_URL)) $errors[] = 'Vui lòng nhập đường dẫn tải xuống hợp lệ.';

    $imageUrl = null;
    if (!empty($_FILES['cover']['tmp_name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($_FILES['cover']['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            $errors[] = 'Ảnh bìa phải là JPG, PNG, WEBP hoặc GIF.';
        } elseif ($_FILES['cover']['size'] > 8 * 1024 * 1024) {
            $errors[] = 'Ảnh bìa tối đa 8MB.';
        } else {
            $imageUrl = upload_image_imgbb($_FILES['cover']['tmp_name']);
            if ($imageUrl === false) {
                $errors[] = 'Tải ảnh bìa lên thất bại, vui lòng thử lại.';
            }
        }
    } else {
        $errors[] = 'Vui lòng chọn ảnh bìa.';
    }

    if (!$errors) {
        $description = nl2br(e($old['description']), false);
        $stmt = db()->prepare('INSERT INTO project (title, description, author, status, source, category_id, image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $old['title'],
            $description,
            $old['author'],
            'active',
            $old['source'],
            $old['category_id'],
            $imageUrl,
            $user['user_id'],
        ]);
        $newId = (int)db()->lastInsertId();
        flash('success', 'Đăng tài nguyên thành công!');
        redirect(SITE_URL . '/project.php?id=' . $newId);
    }
}

$pageTitle = 'Đăng tài nguyên mới';
require __DIR__ . '/../includes/header.php';
?>

<div class="form-card wide">
  <h1>Đăng tài nguyên mới</h1>
  <p class="form-subtitle">Chia sẻ pack chỉnh sửa, project file hoặc tài nguyên của bạn với cộng đồng</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="form-group">
      <label for="title">Tiêu đề</label>
      <input class="form-control" type="text" id="title" name="title" value="<?= e($old['title']) ?>" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="category_id">Danh mục</label>
        <select class="form-control" id="category_id" name="category_id" required>
          <option value="">— Chọn danh mục —</option>
          <?php foreach (get_categories() as $cat): ?>
            <option value="<?= (int)$cat['category_id'] ?>" <?= $old['category_id'] === (int)$cat['category_id'] ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="author">Tên tác giả hiển thị</label>
        <input class="form-control" type="text" id="author" name="author" value="<?= e($old['author']) ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="source">Đường dẫn tải xuống (Google Drive, Mega...)</label>
      <input class="form-control" type="url" id="source" name="source" value="<?= e($old['source']) ?>" placeholder="https://..." required>
    </div>
    <div class="form-group">
      <label for="description">Mô tả</label>
      <textarea class="form-control" id="description" name="description" placeholder="Nội dung pack bao gồm những gì..."><?= e($old['description']) ?></textarea>
    </div>
    <div class="form-group">
      <label for="coverInput">Ảnh bìa</label>
      <div class="upload-drop">
        <input type="file" id="coverInput" name="cover" accept="image/*" required>
        <span class="hint">Ảnh sẽ được lưu trữ qua ImgBB. Tối đa 8MB.</span>
        <img id="coverPreview" class="upload-preview" style="display:none;" alt="Xem trước ảnh bìa">
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Đăng tài nguyên</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
