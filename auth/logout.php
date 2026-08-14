<?php
require_once __DIR__ . '/../includes/bootstrap.php';
logout_user();
session_start();
flash('info', 'Bạn đã đăng xuất.');
redirect(SITE_URL . '/index.php');
