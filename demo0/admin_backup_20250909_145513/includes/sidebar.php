<?php
// Admin Sidebar Component
// Include this file in all admin pages

// Get current page for active state
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
        <a href="pages.php" class="<?= $current_page == 'pages' || $current_page == 'edit-page' || $current_page == 'add-page' ? 'active' : '' ?>">
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
    
    <!-- Admin Info -->
    <div style="padding: 1rem 1.5rem; margin-top: 2rem; border-top: 1px solid var(--border-color); color: var(--text-secondary); font-size: 0.875rem;">
        <div style="margin-bottom: 0.5rem;">
            👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
        </div>
        <div style="margin-bottom: 0.5rem;">
            🕒 <?= date('H:i') ?>
        </div>
        <div>
            📅 <?= date('d/m/Y') ?>
        </div>
    </div>
</aside>

<style>
/* Additional Sidebar Styles */
.admin-sidebar {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow-y: auto;
}

.admin-nav {
    flex: 1;
}

.admin-nav a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    font-size: 0.9375rem;
}

.admin-nav a:hover {
    background: var(--dark-bg);
    color: var(--primary-color);
    border-left-color: var(--primary-color);
}

.admin-nav a.active {
    background: var(--dark-bg);
    color: var(--primary-color);
    border-left-color: var(--primary-color);
    font-weight: 500;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .admin-sidebar {
        position: fixed;
        left: -250px;
        top: 0;
        height: 100vh;
        z-index: 1000;
        transition: left 0.3s ease;
        background: var(--dark-surface);
        box-shadow: 2px 0 10px rgba(0,0,0,0.3);
    }
    
    .admin-sidebar.active {
        left: 0;
    }
    
    /* Add mobile menu toggle button */
    .mobile-menu-toggle {
        display: block;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
}

@media (min-width: 769px) {
    .mobile-menu-toggle {
        display: none;
    }
}
</style>

<script>
// Mobile menu toggle
function toggleAdminMenu() {
    const sidebar = document.querySelector('.admin-sidebar');
    sidebar.classList.toggle('active');
}

// Add mobile menu button if not exists
if (window.innerWidth <= 768 && !document.querySelector('.mobile-menu-toggle')) {
    const menuBtn = document.createElement('button');
    menuBtn.className = 'mobile-menu-toggle';
    menuBtn.innerHTML = '☰';
    menuBtn.onclick = toggleAdminMenu;
    document.body.appendChild(menuBtn);
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768) {
        const sidebar = document.querySelector('.admin-sidebar');
        const menuBtn = document.querySelector('.mobile-menu-toggle');
        
        if (sidebar && menuBtn && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});
</script>
