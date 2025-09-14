<?php
// ไฟล์นี้เป็น template สำหรับ about.php, services.php, contact.php
// ให้ copy ไฟล์นี้และเปลี่ยนชื่อเป็น about.php, services.php, contact.php
// แล้วเปลี่ยนค่า $page_slug ตามหน้าที่ต้องการ

require_once 'config/database.php';

// เปลี่ยน slug ตามหน้าที่ต้องการ
// สำหรับ about.php ใช้ 'about'
// สำหรับ services.php ใช้ 'services'  
// สำหรับ contact.php ใช้ 'contact'
$page_slug = basename($_SERVER['PHP_SELF'], '.php');

// ดึงข้อมูลการตั้งค่า
$settings = getSiteSettings($pdo);

// ดึงเนื้อหาหน้า
$page = getPageContent($pdo, $page_slug);
if (!$page || !$page['is_active']) {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>404 - Page Not Found</h1>';
    exit;
}

// SEO Meta Tags
$meta_title = $page['meta_title'] ?: $page['page_title'] . ' - ' . $settings['site_name'];
$meta_description = $page['meta_description'] ?: $settings['meta_description'];
$meta_keywords = $page['meta_keywords'] ?: $settings['meta_keywords'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($meta_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords) ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($meta_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dynamic-styles.php">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="content-card">
                <?= $page['page_content'] ?>
                
                <?php if($page_slug == 'contact'): ?>
                    <!-- Contact Form สำหรับหน้า contact -->
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h2>ส่งข้อความถึงเรา</h2>
                        <form id="contactForm" style="max-width: 600px;">
                            <div class="form-group">
                                <label for="name">ชื่อ-นามสกุล</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">อีเมล</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">เบอร์โทร</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="message">ข้อความ</label>
                                <textarea id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
                        </form>
                    </div>
                    
                    <script>
                        document.getElementById('contactForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            alert('ขอบคุณสำหรับข้อความของคุณ! เราจะติดต่อกลับโดยเร็วที่สุด');
                            this.reset();
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
