<?php

/**
 * The cover image itself is uploaded straight from the browser to ImgBB
 * (see assets/js/main.js) — InfinityFree's free tier blocks outbound
 * curl/socket connections from PHP, so a server-side upload to ImgBB never
 * completes there. The server only receives the resulting ImgBB URL and
 * must confirm it actually points at ImgBB before storing it, since it's
 * otherwise unvalidated user input.
 */
function is_valid_cover_url(string $url): bool
{
    $parts = parse_url($url);
    if ($parts === false || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])) {
        return false;
    }

    $host = strtolower($parts['host']);
    return $host === 'ibb.co' || str_ends_with($host, '.ibb.co');
}
