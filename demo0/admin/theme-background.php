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

// Handle background image upload
if(isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] == 0) {
    try {
        $target_dir = '../assets/uploads/backgrounds/';
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION));
        
        if(!in_array($ext, $allowed)) {
            throw new Exception('รูปแบบไฟล์ไม่ถูกต้อง');
        }
        
        if($_FILES['bg_image']['size'] > 10000000) { // 10MB
            throw new Exception('ไฟล์มีขนาดใหญ่เกินไป (สูงสุด 10MB)');
        }
        
        // Delete old background
        if($theme['bg_image'] && file_exists('..' . $theme['bg_image'])) {
            unlink('..' . $theme['bg_image']);
        }
        
        $new_filename = 'bg_' . time() . '.' . $ext;
        $upload_path = $target_dir . $new_filename;
        
        if(move_uploaded_file($_FILES['bg_image']['tmp_name'], $upload_path)) {
            $bg_image_path = '/assets/uploads/backgrounds/' . $new_filename;
            
            $stmt = $pdo->prepare("UPDATE theme_settings SET bg_image = ? WHERE id = 1");
            $stmt->execute([$bg_image_path]);
            
            $message = 'อัพโหลด Background เรียบร้อยแล้ว';
            $theme['bg_image'] = $bg_image_path;
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Update background settings
if(isset($_POST['update_background'])) {
    try {
        $sql = "UPDATE theme_settings SET 
                bg_type = ?,
                bg_gradient_start = ?,
                bg_gradient_end = ?,
                bg_overlay_opacity = ?
                WHERE id = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['bg_type'],
            $_POST['bg_gradient_start'],
            $_POST['bg_gradient_end'],
            $_POST['bg_overlay_opacity']
        ]);
        
        $message = 'บันทึกการตั้งค่า Background เรียบร้อยแล้ว';
        $theme = getThemeSettings($pdo);
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

// Remove background image
if(isset($_GET['remove_bg'])) {
    if($theme['bg_image'] && file_exists('..' . $theme['bg_image'])) {
        unlink('..' . $theme['bg_image']);
    }
    
    $stmt = $pdo->prepare("UPDATE theme_settings SET bg_image = NULL WHERE id = 1");
    $stmt->execute();
    
    header('Location: theme-background.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ Background - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <style>
        .bg-preview {
            height: 300px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .bg-preview-content {
            position: relative;
            z-index: 2;
            padding: 2rem;
            color: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .bg-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .bg-type-option {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .bg-type-option.active {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }
        .bg-type-option:hover {
            border-color: var(--primary-color);
        }
        .color-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .opacity-slider {
            width: 100%;
            margin: 1rem 0;
        }
        .image-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>🖼️ จัดการ Background</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <!-- Live Preview -->
            <div class="content-card">
                <h2>ตัวอย่างการแสดงผล</h2>
                <div class="bg-preview" id="bgPreview">
                    <div class="bg-preview-content">
                        <h1>FitLife Gym</h1>
                        <p>Your Journey to Fitness Starts Here</p>
                        <button class="btn btn-primary">เริ่มต้นเลย</button>
                    </div>
                </div>
            </div>
            
            <!-- Background Settings -->
            <form method="POST" enctype="multipart/form-data">
                <div class="content-card">
                    <h2>ประเภท Background</h2>
                    
                    <div class="bg-types">
                        <label class="bg-type-option <?= $theme['bg_type'] == 'gradient' ? 'active' : '' ?>">
                            <input type="radio" name="bg_type" value="gradient" <?= $theme['bg_type'] == 'gradient' ? 'checked' : '' ?> style="display: none;" onchange="updateBgType(this.value)">
                            <div style="font-size: 2rem;">🎨</div>
                            <div>Gradient</div>
                        </label>
                        
                        <label class="bg-type-option <?= $theme['bg_type'] == 'solid' ? 'active' : '' ?>">
                            <input type="radio" name="bg_type" value="solid" <?= $theme['bg_type'] == 'solid' ? 'checked' : '' ?> style="display: none;" onchange="updateBgType(this.value)">
                            <div style="font-size: 2rem;">🎯</div>
                            <div>Solid Color</div>
                        </label>
                        
                        <label class="bg-type-option <?= $theme['bg_type'] == 'image' ? 'active' : '' ?>">
                            <input type="radio" name="bg_type" value="image" <?= $theme['bg_type'] == 'image' ? 'checked' : '' ?> style="display: none;" onchange="updateBgType(this.value)">
                            <div style="font-size: 2rem;">🖼️</div>
                            <div>Image</div>
                        </label>
                        
                        <label class="bg-type-option <?= $theme['bg_type'] == 'pattern' ? 'active' : '' ?>">
                            <input type="radio" name="bg_type" value="pattern" <?= $theme['bg_type'] == 'pattern' ? 'checked' : '' ?> style="display: none;" onchange="updateBgType(this.value)">
                            <div style="font-size: 2rem;">🔲</div>
                            <div>Pattern</div>
                        </label>
                    </div>
                </div>
                
                <!-- Gradient Settings -->
                <div class="content-card" id="gradientSettings" style="<?= $theme['bg_type'] != 'gradient' ? 'display:none;' : '' ?>">
                    <h2>การตั้งค่า Gradient</h2>
                    <div class="color-grid">
                        <div class="form-group">
                            <label>สีเริ่มต้น</label>
                            <div class="color-input-group">
                                <input type="color" name="bg_gradient_start" value="<?= htmlspecialchars($theme['bg_gradient_start']) ?>" onchange="updatePreview()">
                                <input type="text" value="<?= htmlspecialchars($theme['bg_gradient_start']) ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>สีสิ้นสุด</label>
                            <div class="color-input-group">
                                <input type="color" name="bg_gradient_end" value="<?= htmlspecialchars($theme['bg_gradient_end']) ?>" onchange="updatePreview()">
                                <input type="text" value="<?= htmlspecialchars($theme['bg_gradient_end']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Image Upload -->
                <div class="content-card" id="imageSettings" style="<?= $theme['bg_type'] != 'image' ? 'display:none;' : '' ?>">
                    <h2>Background Image</h2>
                    
                    <?php if($theme['bg_image']): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="..<?= htmlspecialchars($theme['bg_image']) ?>" style="max-width: 300px; border-radius: 8px;">
                            <div style="margin-top: 1rem;">
                                <a href="?remove_bg=1" class="btn btn-secondary" onclick="return confirm('ต้องการลบ Background Image?')">
                                    🗑️ ลบรูปภาพ
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="image-upload-area">
                        <div style="font-size: 3rem;">📷</div>
                        <p>อัพโหลด Background Image</p>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            แนะนำขนาด 1920x1080px หรือใหญ่กว่า
                        </p>
                        <input type="file" name="bg_image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label>ความทึบของ Overlay (0-1)</label>
                        <input type="range" name="bg_overlay_opacity" min="0" max="1" step="0.1" value="<?= $theme['bg_overlay_opacity'] ?>" class="opacity-slider" onchange="updatePreview()">
                        <span id="opacityValue"><?= $theme['bg_overlay_opacity'] ?></span>
                    </div>
                </div>
                
                <button type="submit" name="update_background" class="btn btn-primary">💾 บันทึกการตั้งค่า</button>
            </form>
        </div>
    </div>
    
    <script>
        // Initialize preview
        updatePreview();
        
        function updateBgType(type) {
            document.querySelectorAll('.bg-type-option').forEach(el => el.classList.remove('active'));
            event.target.closest('.bg-type-option').classList.add('active');
            
            // Show/hide settings
            document.getElementById('gradientSettings').style.display = type === 'gradient' ? 'block' : 'none';
            document.getElementById('imageSettings').style.display = type === 'image' ? 'block' : 'none';
            
            updatePreview();
        }
        
        function updatePreview() {
            const preview = document.getElementById('bgPreview');
            const bgType = document.querySelector('input[name="bg_type"]:checked').value;
            
            if(bgType === 'gradient') {
                const start = document.querySelector('input[name="bg_gradient_start"]').value;
                const end = document.querySelector('input[name="bg_gradient_end"]').value;
                preview.style.background = `linear-gradient(135deg, ${start}, ${end})`;
            } else if(bgType === 'solid') {
                const color = document.querySelector('input[name="bg_gradient_start"]').value;
                preview.style.background = color;
            } else if(bgType === 'image') {
                <?php if($theme['bg_image']): ?>
                preview.style.background = `url('..<?= $theme['bg_image'] ?>') center/cover`;
                <?php endif; ?>
            }
        }
        
        // Update opacity value display
        document.querySelector('input[name="bg_overlay_opacity"]').addEventListener('input', function() {
            document.getElementById('opacityValue').textContent = this.value;
        });
    </script>
</body>
</html>
