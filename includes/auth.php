<?php

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function has_role(array $roleIds): bool
{
    $user = current_user();
    return $user !== null && in_array((int)$user['role_id'], $roleIds, true);
}

function is_staff(): bool
{
    return has_role([ROLE_STAFF, ROLE_ADMIN]);
}

function is_admin(): bool
{
    return has_role([ROLE_ADMIN]);
}

function login_user(array $userRow): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'user_id' => (int)$userRow['user_id'],
        'username' => $userRow['username'],
        'full_name' => $userRow['full_name'],
        'email' => $userRow['email'],
        'role_id' => (int)$userRow['role_id'],
        'avatar' => $userRow['avatar'] ?? null,
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_login(): void
{
    if (!is_logged_in()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/');
        redirect(SITE_URL . '/auth/login.php?redirect=' . $redirect);
    }
}

function require_role(array $roleIds): void
{
    require_login();
    if (!has_role($roleIds)) {
        http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
}
