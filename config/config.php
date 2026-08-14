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
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'if0_37413994');
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

if (!defined('SITE_URL')) {
    define('SITE_URL', rtrim(getenv('SITE_URL') ?: (isset($_SERVER['HTTP_HOST']) ? (detect_scheme() . '://' . $_SERVER['HTTP_HOST']) : 'http://localhost'), '/'));
}

// ---- Roles ----
if (!defined('ROLE_CUSTOMER')) define('ROLE_CUSTOMER', 1);
if (!defined('ROLE_STAFF')) define('ROLE_STAFF', 2);
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 3);

// ---- Google OAuth ----
// Never echo GOOGLE_CLIENT_SECRET to the browser.
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
if (!defined('GOOGLE_REDIRECT_URI')) define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: (SITE_URL . '/auth/google_callback.php'));

// ---- ImgBB (cover image uploads) ----
// Never echo IMGBB_API_KEY to the browser.
if (!defined('IMGBB_API_KEY')) define('IMGBB_API_KEY', getenv('IMGBB_API_KEY') ?: '');
