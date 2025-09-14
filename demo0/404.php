<?php
require_once 'config/database.php';
$settings = getSiteSettings($pdo);
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - ไม่พบหน้าที่ต้องการ - <?= htmlspecialchars($settings['site_name']) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dynamic-styles.php">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1;
        }
        .error-message {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin: 1rem 0 2rem;
        }
        .error-description {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 500px;
        }
        .error-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div>
            <div class="error-animation">
                <h1 class="error-code">404</h1>
            </div>
            <h2 class="error-message">ไม่พบหน้าที่คุณต้องการ</h2>
            <p class="error-description">
                ขออภัย หน้าที่คุณกำลังค้นหาอาจถูกย้าย เปลี่ยนชื่อ หรือไม่มีอยู่ในระบบ
                กรุณาตรวจสอบ URL อีกครั้งหรือกลับไปยังหน้าหลัก
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="/" class="btn btn-primary">🏠 กลับหน้าหลัก</a>
                <a href="/contact" class="btn btn-secondary">📞 ติดต่อเรา</a>
            </div>
        </div>
    </div>
</body>
</html>
