<?php
// ข้อมูลฐานข้อมูล
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'remote_user');  // เปลี่ยนเป็นผู้ใช้ DB ของคุณ
define('DB_PASSWORD', 'Remote#pwd0');      // เปลี่ยนเป็นรหัสผ่าน DB ของคุณ
define('DB_NAME', 'remote_support_db');

// การตั้งค่าการเข้ารหัส (เก็บเป็นความลับ! ใส่ใน .env หรือไฟล์ปลอดภัย)
define('ENCRYPTION_KEY', 'qvK7xWpMHpL3FtGdYz4wJ9nLsXBtC0ei');  // ต้อง 32 ตัวอักษรสำหรับ AES-256
define('ENCRYPTION_IV', 'T4nWu2ZkX3y9LbVg');           // ต้อง 16 ตัวอักษร

// เชื่อมต่อ DB
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// ฟังก์ชันเข้ารหัสข้อมูล
function encryptData($data) {
    return base64_encode(openssl_encrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

// ฟังก์ชันถอดรหัสข้อมูล
function decryptData($data) {
    return openssl_decrypt(base64_decode($data), 'aes-256-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

// ฟังก์ชันเช็คว่าเป็น admin หรือไม่
function is_admin($conn, $user_id) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
    return $role === 'admin';
}
?>
