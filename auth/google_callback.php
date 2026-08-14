<?php
require_once __DIR__ . '/../includes/bootstrap.php';

function http_post_json(string $url, array $fields)
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response === false ? null : json_decode($response, true);
}

function http_get_json(string $url, string $bearerToken)
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $bearerToken],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response === false ? null : json_decode($response, true);
}

$redirectTo = $_SESSION['google_oauth_redirect'] ?? '';
unset($_SESSION['google_oauth_redirect']);

if (isset($_GET['error'])) {
    flash('error', 'Đăng nhập Google đã bị hủy.');
    redirect(SITE_URL . '/auth/login.php');
}

$state = $_GET['state'] ?? '';
$expectedState = $_SESSION['google_oauth_state'] ?? '';
unset($_SESSION['google_oauth_state']);
if ($state === '' || !hash_equals($expectedState, $state)) {
    flash('error', 'Phiên đăng nhập Google không hợp lệ, vui lòng thử lại.');
    redirect(SITE_URL . '/auth/login.php');
}

$code = $_GET['code'] ?? '';
if ($code === '') {
    flash('error', 'Không nhận được mã xác thực từ Google.');
    redirect(SITE_URL . '/auth/login.php');
}

$tokenData = http_post_json('https://oauth2.googleapis.com/token', [
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => GOOGLE_REDIRECT_URI,
]);

if (empty($tokenData['access_token'])) {
    flash('error', 'Không thể xác thực với Google, vui lòng thử lại.');
    redirect(SITE_URL . '/auth/login.php');
}

$profile = http_get_json('https://www.googleapis.com/oauth2/v3/userinfo', $tokenData['access_token']);
if (empty($profile['email'])) {
    flash('error', 'Không lấy được thông tin tài khoản Google.');
    redirect(SITE_URL . '/auth/login.php');
}

$email = $profile['email'];
$googleId = $profile['sub'] ?? null;
$fullName = $profile['name'] ?? $email;
$avatar = $profile['picture'] ?? null;

$stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$userRow = $stmt->fetch();

if ($userRow) {
    $update = db()->prepare('UPDATE users SET google_id = ?, avatar = COALESCE(avatar, ?) WHERE user_id = ?');
    $update->execute([$googleId, $avatar, $userRow['user_id']]);
} else {
    $usernameBase = preg_replace('/[^a-zA-Z0-9_.]/', '', strtok($email, '@')) ?: 'user';
    $username = $usernameBase;
    $suffix = 1;
    $checkStmt = db()->prepare('SELECT 1 FROM users WHERE username = ?');
    while (true) {
        $checkStmt->execute([$username]);
        if (!$checkStmt->fetchColumn()) {
            break;
        }
        $username = $usernameBase . $suffix++;
    }

    $insert = db()->prepare('INSERT INTO users (username, email, password, full_name, role_id, google_id, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $insert->execute([
        $username,
        $email,
        password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT),
        $fullName,
        ROLE_CUSTOMER,
        $googleId,
        $avatar,
    ]);
    $userId = (int)db()->lastInsertId();
    $fetch = db()->prepare('SELECT * FROM users WHERE user_id = ?');
    $fetch->execute([$userId]);
    $userRow = $fetch->fetch();
}

login_user($userRow);
flash('success', 'Đăng nhập bằng Google thành công!');
$target = $redirectTo !== '' ? urldecode($redirectTo) : (SITE_URL . '/index.php');
if (!str_starts_with($target, SITE_URL)) {
    $target = SITE_URL . '/index.php';
}
redirect($target);
