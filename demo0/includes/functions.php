<?php
/**
 * Helper Functions
 */

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Format date to Thai
function formatDateThai($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
        4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
        7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
        10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543; // Buddhist year
    
    return "$day $month $year";
}

// Generate SEO-friendly slug
function createSlug($string) {
    $string = trim($string);
    $string = preg_replace('/[^a-zA-Z0-9ก-๙\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = preg_replace('/^-+|-+$/', '', $string);
    return strtolower($string);
}

// Get client IP
function getClientIP() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Check if request is AJAX
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}

// File size formatter
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number (Thai format)
function isValidPhone($phone) {
    $pattern = '/^(0[0-9]{1,2})[0-9]{7,8}$/';
    return preg_match($pattern, $phone);
}

// Create excerpt from content
function createExcerpt($content, $length = 150) {
    $content = strip_tags($content);
    if (mb_strlen($content) <= $length) {
        return $content;
    }
    $excerpt = mb_substr($content, 0, $length);
    $lastSpace = mb_strrpos($excerpt, ' ');
    return mb_substr($excerpt, 0, $lastSpace) . '...';
}

// Log activity
function logActivity($pdo, $action, $details = '') {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $action,
            $details,
            getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch(Exception $e) {
        // Silently fail if logging fails
    }
}

// Get page load time
function getPageLoadTime() {
    if (defined('START_TIME')) {
        return round(microtime(true) - START_TIME, 4);
    }
    return 0;
}

// Simple cache function
function getCache($key) {
    $cacheFile = "../cache/" . md5($key) . ".cache";
    if (file_exists($cacheFile)) {
        $cache = unserialize(file_get_contents($cacheFile));
        if ($cache['expire'] > time()) {
            return $cache['data'];
        }
        unlink($cacheFile);
    }
    return false;
}

function setCache($key, $data, $expire = 3600) {
    $cacheDir = "../cache/";
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }
    $cacheFile = $cacheDir . md5($key) . ".cache";
    $cache = [
        'data' => $data,
        'expire' => time() + $expire
    ];
    file_put_contents($cacheFile, serialize($cache));
}

// Clear all cache
function clearCache() {
    $cacheDir = "../cache/";
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . "*.cache");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
