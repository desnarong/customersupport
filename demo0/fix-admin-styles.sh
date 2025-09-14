#!/bin/bash

# Script สำหรับแก้ไขหน้า Admin ให้ใช้ dynamic-styles.php
# ใช้งาน: bash fix-admin-styles.sh

ADMIN_DIR="/var/www/remote_support/demo0/admin"

echo "🔧 กำลังแก้ไขไฟล์ Admin ให้ใช้ Dynamic Styles..."

# Backup ก่อนแก้ไข
echo "📦 Backup ไฟล์เดิม..."
cp -r $ADMIN_DIR ${ADMIN_DIR}_backup_$(date +%Y%m%d_%H%M%S)

# รายชื่อไฟล์ที่ต้องแก้ไข
files=(
    "dashboard.php"
    "settings.php"
    "pages.php"
    "edit-page.php"
    "theme.php"
    "theme-background.php"
    "sliders.php"
    "edit-slider.php"
    "gallery.php"
    "backup.php"
)

# แก้ไขแต่ละไฟล์
for file in "${files[@]}"; do
    if [ -f "$ADMIN_DIR/$file" ]; then
        echo "✏️ แก้ไข $file..."
        
        # ลบ Google Fonts link เก่า
        sed -i '/<link href="https:\/\/fonts.googleapis.com/d' "$ADMIN_DIR/$file"
        
        # เพิ่ม dynamic-styles.php ถ้ายังไม่มี
        if ! grep -q "dynamic-styles.php" "$ADMIN_DIR/$file"; then
            # หาบรรทัดที่มี style.css แล้วเพิ่ม dynamic-styles.php ต่อท้าย
            sed -i '/<link rel="stylesheet" href="..\/assets\/css\/style.css">/a\    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">' "$ADMIN_DIR/$file"
        fi
        
        echo "✅ $file - เสร็จสิ้น"
    else
        echo "⚠️ ไม่พบไฟล์ $file"
    fi
done

# สร้างไฟล์ sidebar.php
echo "📄 สร้างไฟล์ sidebar.php..."
mkdir -p "$ADMIN_DIR/includes"

cat > "$ADMIN_DIR/includes/sidebar.php" << 'EOF'
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
EOF

echo "✅ สร้าง sidebar.php เสร็จสิ้น"

# ตั้ง permissions
echo "🔒 ตั้งค่า permissions..."
chown -R www-data:www-data $ADMIN_DIR
chmod -R 755 $ADMIN_DIR

echo "🎉 เสร็จสิ้นการแก้ไข!"
echo "📌 หมายเหตุ: ไฟล์ backup อยู่ที่ ${ADMIN_DIR}_backup_*"
echo "🌐 ทดสอบได้ที่: https://www.quickmt4.biz/demo0/admin/"
