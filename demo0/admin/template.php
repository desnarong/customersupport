<?php
// Template สำหรับหน้า Admin ทุกหน้า
// ใช้เป็นต้นแบบในการสร้างหน้า admin ใหม่หรือแก้ไขหน้าเดิม

session_start();
require_once '../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// ดึงข้อมูลที่จำเป็น
$settings = getSiteSettings($pdo);
$theme = getThemeSettings($pdo);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้า Admin - <?= htmlspecialchars($settings['site_name']) ?></title>
    
    <!-- CSS Files (ลำดับสำคัญ) -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    
    <!-- ไม่ต้องใส่ Google Fonts ตรงนี้ เพราะ dynamic-styles.php จัดการให้แล้ว -->
    
    <!-- Additional Admin Styles -->
    <style>
        /* Override สำหรับ Admin Panel ถ้าจำเป็น */
        .admin-container {
            background: transparent; /* ใช้ background จาก body */
        }
        
        /* Ensure readability in both light and dark mode */
        .admin-content {
            color: var(--text-primary);
        }
        
        /* Form elements inherit theme colors */
        input, textarea, select {
            background: var(--dark-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }
        
        /* Tables use theme colors */
        table {
            color: var(--text-primary);
        }
        
        /* Cards with theme support */
        .content-card {
            background: var(--dark-surface);
            border-color: var(--border-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                position: relative;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (ใช้ include หรือ copy โค้ดจาก sidebar.php) -->
        <?php 
        // ถ้ามีไฟล์ sidebar.php
        if(file_exists('includes/sidebar.php')) {
            include 'includes/sidebar.php';
        } else {
            // ถ้ายังไม่มีไฟล์ sidebar.php ใช้โค้ดนี้
            ?>
            <aside class="admin-sidebar">
                <h2>🏋️ Admin Panel</h2>
                <nav class="admin-nav">
                    <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">📊 Dashboard</a>
                    <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">⚙️ ตั้งค่าเว็บไซต์</a>
                    <a href="pages.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pages.php' ? 'active' : '' ?>">📄 จัดการหน้า</a>
                    <a href="theme.php" class="<?= basename($_SERVER['PHP_SELF']) == 'theme.php' ? 'active' : '' ?>">🎨 ธีมและสี</a>
                    <a href="theme-background.php" class="<?= basename($_SERVER['PHP_SELF']) == 'theme-background.php' ? 'active' : '' ?>">🖼️ Background</a>
                    <a href="sliders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'sliders.php' ? 'active' : '' ?>">🎞️ Sliders</a>
                    <a href="gallery.php" class="<?= basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : '' ?>">📸 Gallery</a>
                    <a href="backup.php" class="<?= basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : '' ?>">💾 Backup</a>
                    <a href="logout.php">🚪 ออกจากระบบ</a>
                </nav>
            </aside>
            <?php
        }
        ?>
        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>หัวข้อหน้า</h1>
            
            <!-- Alert Messages -->
            <?php if(isset($message) && $message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if(isset($error) && $error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <!-- Content Card Example -->
            <div class="content-card">
                <h2>เนื้อหา</h2>
                <p>ใส่เนื้อหาของหน้าตรงนี้</p>
            </div>
            
            <!-- Form Example -->
            <div class="content-card">
                <h2>ฟอร์มตัวอย่าง</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Input Field</label>
                        <input type="text" name="example" placeholder="ตัวอย่าง input">
                    </div>
                    
                    <div class="form-group">
                        <label>Textarea</label>
                        <textarea name="description" rows="4" placeholder="ตัวอย่าง textarea"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Select</label>
                        <select name="option">
                            <option>Option 1</option>
                            <option>Option 2</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">💾 บันทึก</button>
                    <button type="button" class="btn btn-secondary">ยกเลิก</button>
                </form>
            </div>
            
            <!-- Table Example -->
            <div class="content-card">
                <h2>ตารางตัวอย่าง</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Column 1</th>
                                <th>Column 2</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Data 1</td>
                                <td>Data 2</td>
                                <td>
                                    <button class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript ที่จำเป็น -->
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
