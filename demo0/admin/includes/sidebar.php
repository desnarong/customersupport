<?php
// Admin Sidebar Component
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="admin-sidebar">
    <h2>🏋️ Admin Panel</h2>
    <nav class="admin-nav">
        <a href="dashboard.php" class="<?= $current_page == 'dashboard' ? 'active' : '' ?>">
            📊 Dashboard
        </a>
        <a href="settings.php" class="<?= $current_page == 'settings' ? 'active' : '' ?>">
            ⚙️ ตั้งค่าเว็บไซต์
        </a>
        <a href="pages.php" class="<?= $current_page == 'pages' || $current_page == 'edit-page' ? 'active' : '' ?>">
            📄 จัดการหน้า
        </a>
        <a href="theme.php" class="<?= $current_page == 'theme' ? 'active' : '' ?>">
            🎨 ธีมและสี
        </a>
        <a href="theme-background.php" class="<?= $current_page == 'theme-background' ? 'active' : '' ?>">
            🖼️ Background
        </a>
        <a href="sliders.php" class="<?= $current_page == 'sliders' || $current_page == 'edit-slider' ? 'active' : '' ?>">
            🎞️ Sliders
        </a>
        <a href="gallery.php" class="<?= $current_page == 'gallery' ? 'active' : '' ?>">
            📸 Gallery
        </a>
        <a href="backup.php" class="<?= $current_page == 'backup' ? 'active' : '' ?>">
            💾 Backup
        </a>
        <a href="logout.php">
            🚪 ออกจากระบบ
        </a>
    </nav>
</aside>
