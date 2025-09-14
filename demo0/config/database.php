<?php
// การตั้งค่าฐานข้อมูล
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'S@m0966414159');
define('DB_NAME', 'fitness_db');

// สร้างการเชื่อมต่อ
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ฟังก์ชันสำหรับดึงการตั้งค่าเว็บไซต์
function getSiteSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    return $stmt->fetch();
}

// ฟังก์ชันสำหรับดึงการตั้งค่า Theme
function getThemeSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM theme_settings WHERE id = 1");
    return $stmt->fetch();
}

// ฟังก์ชันสำหรับดึงเมนู
function getMenuItems($pdo) {
    $stmt = $pdo->query("SELECT page_slug, menu_title FROM pages WHERE is_active = 1 ORDER BY sort_order");
    return $stmt->fetchAll();
}

// ฟังก์ชันสำหรับดึงข้อมูลหน้า
function getPageContent($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// ตั้งค่า timezone
date_default_timezone_set('Asia/Bangkok');

// เริ่ม session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
