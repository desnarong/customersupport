<?php
/**
 * สคริปต์รีเซ็ตรหัสผ่าน Admin
 * วางไฟล์นี้ใน root directory แล้วรันผ่าน browser
 * ลบไฟล์นี้ทันทีหลังใช้งานเสร็จ!
 */

require_once 'config/database.php';

// รหัสผ่านใหม่
$new_password = 'admin123';
$username = 'admin';

// Encrypt รหัสผ่าน
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // ตรวจสอบว่ามี admin user หรือไม่
    $check = $pdo->prepare("SELECT id FROM admin WHERE username = ?");
    $check->execute([$username]);
    
    if($check->fetch()) {
        // อัพเดทรหัสผ่าน
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        $message = "✅ รีเซ็ตรหัสผ่านสำเร็จ!";
    } else {
        // สร้าง admin user ใหม่
        $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        $message = "✅ สร้าง admin user ใหม่สำเร็จ!";
    }
    
    $success = true;
} catch(Exception $e) {
    $message = "❌ เกิดข้อผิดพลาด: " . $e->getMessage();
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin-bottom: 1rem;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin-bottom: 1rem;
        }
        .info-box {
            background: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #bee5eb;
            margin: 1rem 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #ffeeba;
            margin-top: 1rem;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        .button:hover {
            background: #5a67d8;
        }
        .button-danger {
            background: #dc3545;
        }
        .button-danger:hover {
            background: #c82333;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Admin Password</h1>
        
        <?php if(isset($success) && $success): ?>
            <div class="success">
                <?= $message ?>
            </div>
            
            <div class="info-box">
                <strong>ข้อมูลสำหรับเข้าสู่ระบบ:</strong><br><br>
                👤 Username: <code><?= $username ?></code><br>
                🔑 Password: <code><?= $new_password ?></code><br><br>
                🔒 Hashed: <code style="word-break: break-all; font-size: 11px;"><?= $hashed_password ?></code>
            </div>
            
            <div class="center">
                <a href="admin/login.php" class="button">เข้าสู่ระบบ Admin</a>
            </div>
            
            <div class="warning">
                <strong>⚠️ คำเตือนด้านความปลอดภัย:</strong><br>
                กรุณาลบไฟล์นี้ทันทีหลังใช้งานเสร็จ!<br><br>
                <code>rm <?= __FILE__ ?></code>
            </div>
            
        <?php else: ?>
            <div class="error">
                <?= $message ?>
            </div>
            
            <div class="center">
                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="button">ลองอีกครั้ง</a>
            </div>
        <?php endif; ?>
        
        <div class="center" style="margin-top: 2rem;">
            <form method="post" action="" onsubmit="return confirm('ต้องการลบไฟล์นี้หรือไม่?')">
                <button type="submit" name="delete_file" class="button button-danger">
                    🗑️ ลบไฟล์นี้
                </button>
            </form>
        </div>
    </div>
    
    <?php
    // ฟังก์ชันลบไฟล์ตัวเอง
    if(isset($_POST['delete_file'])) {
        if(unlink(__FILE__)) {
            echo "<script>alert('ลบไฟล์สำเร็จ!'); window.location='admin/login.php';</script>";
        } else {
            echo "<script>alert('ไม่สามารถลบไฟล์ได้ กรุณาลบด้วยตนเอง');</script>";
        }
    }
    ?>
</body>
</html>
