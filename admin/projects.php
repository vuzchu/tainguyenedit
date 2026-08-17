<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_role([ROLE_ADMIN]);

// "Tất cả tài nguyên" was merged into staff/index.php (which has search,
// category/date filters, sort and pagination) to avoid two near-identical
// resource lists in the admin area.
redirect(SITE_URL . '/staff/index.php');
