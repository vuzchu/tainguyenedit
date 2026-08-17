<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_STAFF, ROLE_ADMIN]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/staff/index.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM project WHERE project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if ($project) {
    $del = db()->prepare('DELETE FROM project WHERE project_id = ?');
    $del->execute([$id]);
    flash('success', 'Đã xóa tài nguyên.');
} else {
    flash('error', 'Không thể xóa tài nguyên này.');
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$back = (str_starts_with($referer, SITE_URL)) ? $referer : (SITE_URL . '/staff/index.php');
redirect($back);
