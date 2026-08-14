<?php
require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['error' => 'invalid_csrf']);
    exit;
}

$user = current_user();
$projectId = (int)($_POST['project_id'] ?? 0);

$check = db()->prepare('SELECT favorite_id FROM favorites WHERE user_id = ? AND project_id = ?');
$check->execute([$user['user_id'], $projectId]);
$existing = $check->fetchColumn();

if ($existing) {
    $del = db()->prepare('DELETE FROM favorites WHERE favorite_id = ?');
    $del->execute([$existing]);
    echo json_encode(['favorited' => false]);
} else {
    $ins = db()->prepare('INSERT INTO favorites (user_id, project_id) VALUES (?, ?)');
    $ins->execute([$user['user_id'], $projectId]);
    echo json_encode(['favorited' => true]);
}
