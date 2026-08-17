<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_STAFF, ROLE_ADMIN]);

$user = current_user();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM project WHERE project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    die('Không tìm thấy tài nguyên.');
}

$errors = [];
$old = [
    'title' => $project['title'],
    'category_id' => (int)$project['category_id'],
    'author' => $project['author'],
    'description' => $project['description'],
    'source' => $project['source'],
    'status' => $project['status'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $old['title'] = trim($_POST['title'] ?? '');
    $old['category_id'] = (int)($_POST['category_id'] ?? 0);
    $old['author'] = trim($_POST['author'] ?? '') ?: $user['full_name'];
    $old['description'] = trim($_POST['description'] ?? '');
    $old['source'] = trim($_POST['source'] ?? '');
    $old['status'] = ($_POST['status'] ?? 'active') === 'disable' ? 'disable' : 'active';

    if ($old['title'] === '') $errors[] = 'Vui lòng nhập tiêu đề.';
    if ($old['category_id'] <= 0) $errors[] = 'Vui lòng chọn danh mục.';
    if ($old['source'] === '' || !filter_var($old['source'], FILTER_VALIDATE_URL)) $errors[] = 'Vui lòng nhập đường dẫn tải xuống hợp lệ.';

    $imageUrl = $project['image'];
    $newCoverUrl = trim($_POST['cover_url'] ?? '');
    if ($newCoverUrl !== '') {
        if (!is_valid_cover_url($newCoverUrl)) {
            $errors[] = 'Tải ảnh bìa lên thất bại, vui lòng thử lại.';
        } else {
            $imageUrl = $newCoverUrl;
        }
    }

    if (!$errors) {
        $description = clean_html($old['description']);
        $update = db()->prepare('UPDATE project SET title = ?, description = ?, author = ?, source = ?, category_id = ?, image = ?, status = ? WHERE project_id = ?');
        $update->execute([
            $old['title'],
            $description,
            $old['author'],
            $old['source'],
            $old['category_id'],
            $imageUrl,
            $old['status'],
            $id,
        ]);
        flash('success', 'Cập nhật tài nguyên thành công!');
        redirect(SITE_URL . '/staff/index.php');
    }
}

$pageTitle = 'Sửa: ' . $project['title'];
require __DIR__ . '/../includes/header.php';
?>

<div class="form-card wide">
  <h1>Sửa tài nguyên</h1>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$id ?>">
    <input type="hidden" name="cover_url" id="coverUrlInput" value="">
    <div class="form-group">
      <label for="title">Tiêu đề</label>
      <input class="form-control" type="text" id="title" name="title" value="<?= e($old['title']) ?>" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="category_id">Danh mục</label>
        <select class="form-control" id="category_id" name="category_id" required>
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
      <label for="source">Đường dẫn tải xuống</label>
      <input class="form-control" type="url" id="source" name="source" value="<?= e($old['source']) ?>" required>
    </div>
    <div class="form-group">
      <label for="description">Mô tả</label>
      <textarea class="form-control" id="description" name="description"><?= e($old['description']) ?></textarea>
    </div>
    <div class="form-group">
      <label for="status">Trạng thái</label>
      <select class="form-control" id="status" name="status">
        <option value="active" <?= $old['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
        <option value="disable" <?= $old['status'] === 'disable' ? 'selected' : '' ?>>Tắt hiển thị</option>
      </select>
    </div>
    <div class="form-group">
      <label for="coverInput">Ảnh bìa</label>
      <div class="upload-drop">
        <input type="file" id="coverInput" accept="image/*" data-imgbb-key="<?= e(IMGBB_API_KEY) ?>">
        <span class="hint">Để trống nếu muốn giữ ảnh hiện tại.</span>
        <span id="coverUploadStatus" class="hint"></span>
        <img id="coverPreview" class="upload-preview" src="<?= e($project['image'] ?: (SITE_URL . '/assets/img/placeholder.svg')) ?>" alt="Ảnh bìa">
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Lưu thay đổi</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
