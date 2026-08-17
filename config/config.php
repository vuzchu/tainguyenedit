<?php
/**
 * Site configuration.
 *
 * Real secrets (Google OAuth client secret, ImgBB API key, DB password) are
 * never hardcoded here — GitHub push protection will reject any commit that
 * contains them. Provide them either as real environment variables on your
 * host, or by copying config/config.local.php.example to
 * config/config.local.php (gitignored) and filling in the real values.
 */

if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// ---- Database ----
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'if0_37413994_sharedit');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');

// ---- Site ----
if (!defined('SITE_NAME')) define('SITE_NAME', 'ShareIt');

function detect_scheme(): string
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        return strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https' ? 'https' : 'http';
    }
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }
    return 'http';
}

/**
 * Root-relative path of the project, e.g. '/edit' when running under
 * htdocs/edit, or '' when deployed at the domain root. Computed from where
 * this file sits relative to the web server's document root, so moving the
 * project between a subfolder (local XAMPP) and a domain root (InfinityFree)
 * never requires editing this file or config.local.php.
 */
function detect_base_path(): string
{
    $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
    $projectRoot = str_replace('\\', '/', rtrim(dirname(__DIR__), '/'));
    if ($docRoot !== '' && str_starts_with($projectRoot, $docRoot)) {
        return rtrim(substr($projectRoot, strlen($docRoot)), '/');
    }
    return '';
}

if (!defined('SITE_URL')) {
    define('SITE_URL', getenv('SITE_URL') ?: detect_base_path());
}

// Full scheme+host origin — only needed for places that must send an
// absolute URL to a third party (e.g. Google OAuth's redirect_uri).
if (!defined('SITE_ORIGIN')) {
    define('SITE_ORIGIN', detect_scheme() . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
}

// ---- Roles ----
if (!defined('ROLE_CUSTOMER')) define('ROLE_CUSTOMER', 1);
if (!defined('ROLE_STAFF')) define('ROLE_STAFF', 2);
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 3);

// ---- Google OAuth ----
// Never echo GOOGLE_CLIENT_SECRET to the browser.
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
if (!defined('GOOGLE_REDIRECT_URI')) define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: (SITE_ORIGIN . SITE_URL . '/auth/google_callback.php'));

// ---- ImgBB (cover image uploads) ----
// Cover uploads run client-side (browser -> ImgBB directly), because
// InfinityFree's free tier blocks outbound curl/socket connections from
// PHP. IMGBB_API_KEY is intentionally rendered into the upload forms for
// that JS to use — it is not a server-only secret.
if (!defined('IMGBB_API_KEY')) define('IMGBB_API_KEY', getenv('IMGBB_API_KEY') ?: '');
