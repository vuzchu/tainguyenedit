<?php

function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 2592000) return floor($diff / 86400) . ' ngày trước';
    return date('d/m/Y', strtotime($datetime));
}

function excerpt_html(?string $html, int $length = 140): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string)$html)));
    if (mb_strlen($text) <= $length) {
        return e($text);
    }
    return e(mb_substr($text, 0, $length)) . '…';
}

/**
 * Strip a description down to a safe allowlist of formatting tags and
 * remove any inline event-handler attributes or javascript: URIs, since
 * this HTML comes from user-submitted project descriptions.
 */
function clean_html(?string $html): string
{
    $allowed = '<p><br><strong><em><b><i><u><ul><ol><li><a><span><h3><h4><blockquote>';
    $clean = strip_tags((string)$html, $allowed);
    $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
    $clean = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i', '$1=$2#$3', $clean);
    return $clean;
}

function get_categories(): array
{
    static $categories = null;
    if ($categories === null) {
        $categories = db()->query('SELECT * FROM category ORDER BY category_name ASC')->fetchAll();
    }
    return $categories;
}

function category_name(?int $id): string
{
    foreach (get_categories() as $cat) {
        if ((int)$cat['category_id'] === (int)$id) {
            return $cat['category_name'];
        }
    }
    return 'Chưa phân loại';
}

function paginate_links(int $current, int $totalPages, array $params): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $html = '<nav class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $params['page'] = $i;
        $qs = http_build_query($params);
        $active = $i === $current ? ' active' : '';
        $html .= '<a class="page-link' . $active . '" href="?' . e($qs) . '">' . $i . '</a>';
    }
    $html .= '</nav>';
    return $html;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $parts = array_filter($parts);
    if (empty($parts)) return '?';
    $first = mb_substr(reset($parts), 0, 1);
    $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return mb_strtoupper($first . $last);
}
