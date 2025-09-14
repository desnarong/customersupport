<?php
session_start();
require_once '../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$theme = getThemeSettings($pdo);
$message = '';
$error = '';

// Preset themes
$presets = [
    'dark' => [
        'name' => 'Dark Modern',
        'primary' => '#6366f1',
        'secondary' => '#8b5cf6',
        'accent' => '#ec4899',
        'bg_start' => '#0f172a',
        'bg_end' => '#1a1f3a'
    ],
    'blue' => [
        'name' => 'Ocean Blue',
        'primary' => '#3b82f6',
        'secondary' => '#0ea5e9',
        'accent' => '#06b6d4',
        'bg_start' => '#0c4a6e',
        'bg_end' => '#075985'
    ],
    'green' => [
        'name' => 'Nature Green',
        'primary' => '#10b981',
        'secondary' => '#059669',
        'accent' => '#84cc16',
        'bg_start' => '#064e3b',
        'bg_end' => '#047857'
    ],
    'purple' => [
        'name' => 'Royal Purple',
        'primary' => '#9333ea',
        'secondary' => '#a855f7',
        'accent' => '#e879f9',
        'bg_start' => '#4c1d95',
        'bg_end' => '#5b21b6'
    ],
    'red' => [
        'name' => 'Energy Red',
        'primary' => '#ef4444',
        'secondary' => '#f97316',
        'accent' => '#fbbf24',
        'bg_start' => '#7f1d1d',
        'bg_end' => '#991b1b'
    ],
    'light' => [
        'name' => 'Light Clean',
        'primary' => '#3b82f6',
        'secondary' => '#8b5cf6',
        'accent' => '#ec4899',
        'bg_start' => '#f8fafc',
        'bg_end' => '#e2e8f0'
    ]
];

// Google Fonts options
$fonts = [
    'Inter' => 'Inter',
    'Roboto' => 'Roboto',
    'Open Sans' => 'Open+Sans',
    'Lato' => 'Lato',
    'Montserrat' => 'Montserrat',
    'Poppins' => 'Poppins',
    'Raleway' => 'Raleway',
    'Playfair Display' => 'Playfair+Display',
    'Bebas Neue' => 'Bebas+Neue',
    'Oswald' => 'Oswald',
    'Quicksand' => 'Quicksand',
    'Kanit' => 'Kanit',
    'Prompt' => 'Prompt',
    'Sarabun' => 'Sarabun',
    'Noto Sans Thai' => 'Noto+Sans+Thai'
];

// อัพเดทการตั้งค่า
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Apply preset if selected
        if(isset($_POST['apply_preset']) && isset($presets[$_POST['preset']])) {
            $preset = $presets[$_POST['preset']];
            $sql = "UPDATE theme_settings SET 
                    theme_mode = ?,
                    primary_color = ?,
                    secondary_color = ?,
                    accent_color = ?,
                    bg_gradient_start = ?,
                    bg_gradient_end = ?
                    WHERE id = 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['preset'],
                $preset['primary'],
                $preset['secondary'],
                $preset['accent'],
                $preset['bg_start'],
                $preset['bg_end']
            ]);
            $message = 'เปลี่ยนธีมเป็น ' . $preset['name'] . ' เรียบร้อยแล้ว';
        } else {
            // Save custom settings
            $sql = "UPDATE theme_settings SET 
                    theme_mode = ?,
                    primary_color = ?,
                    secondary_color = ?,
                    accent_color = ?,
                    bg_gradient_start = ?,
                    bg_gradient_end = ?,
                    font_family = ?,
                    font_size_base = ?,
                    heading_font = ?,
                    custom_css = ?
                    WHERE id = 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['theme_mode'],
                $_POST['primary_color'],
                $_POST['secondary_color'],
                $_POST['accent_color'],
                $_POST['bg_gradient_start'],
                $_POST['bg_gradient_end'],
                $_POST['font_family'],
                $_POST['font_size_base'],
                $_POST['heading_font'],
                $_POST['custom_css']
            ]);
            $message = 'บันทึกการตั้งค่าธีมเรียบร้อยแล้ว';
        }
        
        $theme = getThemeSettings($pdo); // โหลดข้อมูลใหม่
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการธีมและสี - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <style>
        .theme-preview {
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .theme-preview:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        }
        .theme-preview.active {
            border-color: var(--primary-color);
        }
        .color-input-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .color-preview {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
        .font-preview {
            padding: 1rem;
            background: var(--dark-surface);
            border-radius: 8px;
            margin-top: 0.5rem;
        }
        .preset-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .preset-card {
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid var(--border-color);
        }
        .preset-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        }
        .preset-colors {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        .preset-color {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>🏋️ Admin Panel</h2>
            <nav class="admin-nav">
                <a href="dashboard.php">📊 Dashboard</a>
                <a href="settings.php">⚙️ ตั้งค่าเว็บไซต์</a>
                <a href="pages.php">📄 จัดการหน้า</a>
                <a href="theme.php" class="active">🎨 ธีมและสี</a>
                <a href="logout.php">🚪 ออกจากระบบ</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>🎨 จัดการธีมและสี</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <!-- Preset Themes -->
            <div class="content-card">
                <h2>ธีมสำเร็จรูป</h2>
                <form method="POST">
                    <div class="preset-grid">
                        <?php foreach($presets as $key => $preset): ?>
                        <div class="preset-card" onclick="applyPreset('<?= $key ?>')">
                            <h3><?= $preset['name'] ?></h3>
                            <div class="preset-colors">
                                <div class="preset-color" style="background: <?= $preset['primary'] ?>"></div>
                                <div class="preset-color" style="background: <?= $preset['secondary'] ?>"></div>
                                <div class="preset-color" style="background: <?= $preset['accent'] ?>"></div>
                            </div>
                            <div style="height: 40px; border-radius: 8px; background: linear-gradient(135deg, <?= $preset['bg_start'] ?>, <?= $preset['bg_end'] ?>);"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="preset" id="preset_value">
                    <button type="submit" name="apply_preset" id="apply_preset_btn" style="display: none;"></button>
                </form>
            </div>
            
            <!-- Custom Settings -->
            <form method="POST">
                <div class="content-card">
                    <h2>ปรับแต่งสี</h2>
                    
                    <div class="form-group">
                        <label>โหมดธีม</label>
                        <select name="theme_mode" id="theme_mode">
                            <option value="dark" <?= $theme['theme_mode'] == 'dark' ? 'selected' : '' ?>>Dark Mode</option>
                            <option value="light" <?= $theme['theme_mode'] == 'light' ? 'selected' : '' ?>>Light Mode</option>
                            <option value="custom" <?= $theme['theme_mode'] == 'custom' ? 'selected' : '' ?>>Custom</option>
                        </select>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div class="form-group">
                            <label for="primary_color">สีหลัก (Primary)</label>
                            <div class="color-input-group">
                                <input type="color" id="primary_color" name="primary_color" value="<?= htmlspecialchars($theme['primary_color']) ?>">
                                <input type="text" value="<?= htmlspecialchars($theme['primary_color']) ?>" onchange="document.getElementById('primary_color').value = this.value">
                                <div class="color-preview" style="background: <?= htmlspecialchars($theme['primary_color']) ?>"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="secondary_color">สีรอง (Secondary)</label>
                            <div class="color-input-group">
                                <input type="color" id="secondary_color" name="secondary_color" value="<?= htmlspecialchars($theme['secondary_color']) ?>">
                                <input type="text" value="<?= htmlspecialchars($theme['secondary_color']) ?>" onchange="document.getElementById('secondary_color').value = this.value">
                                <div class="color-preview" style="background: <?= htmlspecialchars($theme['secondary_color']) ?>"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="accent_color">สีเน้น (Accent)</label>
                            <div class="color-input-group">
                                <input type="color" id="accent_color" name="accent_color" value="<?= htmlspecialchars($theme['accent_color']) ?>">
                                <input type="text" value="<?= htmlspecialchars($theme['accent_color']) ?>" onchange="document.getElementById('accent_color').value = this.value">
                                <div class="color-preview" style="background: <?= htmlspecialchars($theme['accent_color']) ?>"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="bg_gradient_start">พื้นหลัง Gradient เริ่มต้น</label>
                            <div class="color-input-group">
                                <input type="color" id="bg_gradient_start" name="bg_gradient_start" value="<?= htmlspecialchars($theme['bg_gradient_start']) ?>">
                                <input type="text" value="<?= htmlspecialchars($theme['bg_gradient_start']) ?>" onchange="document.getElementById('bg_gradient_start').value = this.value">
                                <div class="color-preview" style="background: <?= htmlspecialchars($theme['bg_gradient_start']) ?>"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="bg_gradient_end">พื้นหลัง Gradient สิ้นสุด</label>
                            <div class="color-input-group">
                                <input type="color" id="bg_gradient_end" name="bg_gradient_end" value="<?= htmlspecialchars($theme['bg_gradient_end']) ?>">
                                <input type="text" value="<?= htmlspecialchars($theme['bg_gradient_end']) ?>" onchange="document.getElementById('bg_gradient_end').value = this.value">
                                <div class="color-preview" style="background: <?= htmlspecialchars($theme['bg_gradient_end']) ?>"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="form-group">
                        <label>ตัวอย่างการแสดงผล</label>
                        <div id="color_preview" style="padding: 2rem; border-radius: 12px; background: linear-gradient(135deg, <?= $theme['bg_gradient_start'] ?>, <?= $theme['bg_gradient_end'] ?>);">
                            <h2 style="color: <?= $theme['primary_color'] ?>; margin-bottom: 1rem;">ตัวอย่างหัวข้อ</h2>
                            <p style="color: #94a3b8;">นี่คือตัวอย่างข้อความปกติที่จะแสดงบนเว็บไซต์ของคุณ</p>
                            <button class="btn" style="background: linear-gradient(135deg, <?= $theme['primary_color'] ?>, <?= $theme['secondary_color'] ?>); color: white; margin-right: 1rem;">ปุ่มหลัก</button>
                            <button class="btn" style="background: <?= $theme['accent_color'] ?>; color: white;">ปุ่มเน้น</button>
                        </div>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>ตั้งค่าฟอนต์</h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                        <div class="form-group">
                            <label for="font_family">ฟอนต์หลัก (Body Font)</label>
                            <select name="font_family" id="font_family" onchange="updateFontPreview()">
                                <?php foreach($fonts as $name => $value): ?>
                                <option value="<?= $name ?>" <?= $theme['font_family'] == $name ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="font-preview" id="body_font_preview" style="font-family: '<?= $theme['font_family'] ?>';">
                                <p>ตัวอย่างฟอนต์: The quick brown fox jumps over the lazy dog</p>
                                <p>ภาษาไทย: สวัสดีครับ ยินดีต้อนรับสู่เว็บไซต์ฟิตเนส</p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="heading_font">ฟอนต์หัวข้อ (Heading Font)</label>
                            <select name="heading_font" id="heading_font" onchange="updateHeadingPreview()">
                                <?php foreach($fonts as $name => $value): ?>
                                <option value="<?= $name ?>" <?= $theme['heading_font'] == $name ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="font-preview" id="heading_font_preview" style="font-family: '<?= $theme['heading_font'] ?>';">
                                <h2>ตัวอย่างหัวข้อ Heading</h2>
                                <h3>หัวข้อรอง Sub Heading</h3>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="font_size_base">ขนาดฟอนต์พื้นฐาน</label>
                            <select name="font_size_base" id="font_size_base">
                                <option value="14px" <?= $theme['font_size_base'] == '14px' ? 'selected' : '' ?>>เล็ก (14px)</option>
                                <option value="16px" <?= $theme['font_size_base'] == '16px' ? 'selected' : '' ?>>ปานกลาง (16px)</option>
                                <option value="18px" <?= $theme['font_size_base'] == '18px' ? 'selected' : '' ?>>ใหญ่ (18px)</option>
                                <option value="20px" <?= $theme['font_size_base'] == '20px' ? 'selected' : '' ?>>ใหญ่มาก (20px)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>Custom CSS (ขั้นสูง)</h2>
                    <div class="form-group">
                        <label for="custom_css">CSS เพิ่มเติม (จะถูกเพิ่มต่อท้าย stylesheet หลัก)</label>
                        <textarea name="custom_css" id="custom_css" rows="10" style="font-family: 'Courier New', monospace;"><?= htmlspecialchars($theme['custom_css']) ?></textarea>
                        <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                            💡 ตัวอย่าง: <code>body { background-image: url('/path/to/image.jpg'); }</code>
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">💾 บันทึกการตั้งค่า</button>
                    <a href="dashboard.php" class="btn btn-secondary">ยกเลิก</a>
                    <a href="../index.php" target="_blank" class="btn btn-secondary">👁️ ดูตัวอย่างเว็บไซต์</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Live preview updates
        document.querySelectorAll('input[type="color"]').forEach(input => {
            input.addEventListener('input', function() {
                updatePreview();
                // Update text input
                this.nextElementSibling.value = this.value;
                // Update color preview
                this.parentElement.querySelector('.color-preview').style.background = this.value;
            });
        });
        
        function updatePreview() {
            const preview = document.getElementById('color_preview');
            const primary = document.getElementById('primary_color').value;
            const secondary = document.getElementById('secondary_color').value;
            const accent = document.getElementById('accent_color').value;
            const bgStart = document.getElementById('bg_gradient_start').value;
            const bgEnd = document.getElementById('bg_gradient_end').value;
            
            preview.style.background = `linear-gradient(135deg, ${bgStart}, ${bgEnd})`;
            preview.querySelector('h2').style.color = primary;
            preview.querySelector('button').style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
            preview.querySelectorAll('button')[1].style.background = accent;
        }
        
        function applyPreset(preset) {
            document.getElementById('preset_value').value = preset;
            document.getElementById('apply_preset_btn').click();
        }
        
        function updateFontPreview() {
            const font = document.getElementById('font_family').value;
            document.getElementById('body_font_preview').style.fontFamily = font;
        }
        
        function updateHeadingPreview() {
            const font = document.getElementById('heading_font').value;
            document.getElementById('heading_font_preview').style.fontFamily = font;
        }
    </script>
</body>
</html>
