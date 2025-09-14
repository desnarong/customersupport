<?php
/**
 * Base URL Configuration
 */

// กำหนด Base URL
define('BASE_URL', '/demo0');
define('SITE_URL', 'https://www.quickmt4.biz/demo0');

// Asset URLs
define('CSS_URL', BASE_URL . '/assets/css');
define('JS_URL', BASE_URL . '/assets/js');
define('IMG_URL', BASE_URL . '/assets/images');
define('UPLOAD_URL', BASE_URL . '/assets/uploads');

// Admin URL
define('ADMIN_URL', BASE_URL . '/admin');

// Function สำหรับสร้าง URL
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

// Function สำหรับ asset URL
function asset($path) {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// Function สำหรับ redirect
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}
