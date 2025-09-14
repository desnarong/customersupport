<?php
session_start();
require_once '../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle image upload
function uploadImage($file, $target_dir = '../assets/uploads/sliders/') {
    if(!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if(!in_array($ext, $allowed)) {
        throw new Exception('รูปแบบไฟล์ไม่ถูกต้อง');
    }
    
    if($file['size'] > 5000000) { // 5MB
        throw new Exception('ไฟล์มีขนาดใหญ่เกินไป (สูงสุด 5MB)');
    }
    
    $new_filename = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $upload_path = $target_dir . $new_filename;
    
    if(move_uploaded_file($file['tmp_name'], $upload_path)) {
        return '/assets/uploads/sliders/' . $new_filename;
    }
    
    throw new Exception('ไม่สามารถอัพโหลดไฟล์ได้');
}

// Add new slider
if(isset($_POST['add_slider'])) {
    try {
        $image_path = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_path = uploadImage($_FILES['image']);
        }
        
        $stmt = $pdo->prepare("INSERT INTO sliders (title, subtitle, image_path, button_text, button_link, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['subtitle'],
            $image_path,
            $_POST['button_text'],
            $_POST['button_link'],
            $_POST['sort_order']
        ]);
        
        $message = 'เพิ่ม Slider เรียบร้อยแล้ว';
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

// Toggle active status
if(isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE sliders SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$_GET['toggle']]);
    $message = 'อัพเดทสถานะเรียบร้อยแล้ว';
}

// Delete slider
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // Get image path before delete
    $stmt = $pdo->prepare("SELECT image_path FROM sliders WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $slider = $stmt->fetch();
    
    if($slider && $slider['image_path'] && file_exists('..' . $slider['image_path'])) {
        unlink('..' . $slider['image_path']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $message = 'ลบ Slider เรียบร้อยแล้ว';
}

// Get all sliders
$stmt = $pdo->query("SELECT * FROM sliders ORDER BY sort_order, id");
$sliders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ Image Slider - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <style>
        .slider-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .slider-card {
            background: var(--dark-surface);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }
        .slider-card:hover {
            transform: translateY(-5px);
        }
        .slider-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: var(--dark-bg);
        }
        .slider-content {
            padding: 1.5rem;
        }
        .slider-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .upload-preview {
            max-width: 300px;
            margin-top: 1rem;
            border-radius: 8px;
            overflow: hidden;
        }
        .upload-preview img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>🖼️ จัดการ Image Slider</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <!-- Add New Slider -->
            <div class="content-card">
                <h2>เพิ่ม Slider ใหม่</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <div class="form-group">
                            <label for="title">หัวข้อหลัก *</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subtitle">หัวข้อรอง</label>
                            <input type="text" id="subtitle" name="subtitle">
                        </div>
                        
                        <div class="form-group">
                            <label for="button_text">ข้อความปุ่ม</label>
                            <input type="text" id="button_text" name="button_text" placeholder="เช่น ดูรายละเอียด">
                        </div>
                        
                        <div class="form-group">
                            <label for="button_link">ลิงก์ปุ่ม</label>
                            <input type="text" id="button_link" name="button_link" placeholder="/services">
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_order">ลำดับการแสดง</label>
                            <input type="number" id="sort_order" name="sort_order" value="0" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="image">รูปภาพ * (แนะนำ 1920x600px)</label>
                            <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(this)">
                        </div>
                    </div>
                    
                    <div class="upload-preview" id="preview" style="display: none;">
                        <img id="preview-img" src="" alt="Preview">
                    </div>
                    
                    <button type="submit" name="add_slider" class="btn btn-primary">➕ เพิ่ม Slider</button>
                </form>
            </div>
            
            <!-- Existing Sliders -->
            <div class="content-card">
                <h2>Sliders ที่มีอยู่</h2>
                
                <?php if(count($sliders) > 0): ?>
                    <div class="slider-grid">
                        <?php foreach($sliders as $slider): ?>
                        <div class="slider-card">
                            <?php if($slider['image_path']): ?>
                                <img src="..<?= htmlspecialchars($slider['image_path']) ?>" alt="<?= htmlspecialchars($slider['title']) ?>" class="slider-image">
                            <?php else: ?>
                                <div class="slider-image" style="display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                                    ไม่มีรูปภาพ
                                </div>
                            <?php endif; ?>
                            
                            <div class="slider-content">
                                <h3><?= htmlspecialchars($slider['title']) ?></h3>
                                <?php if($slider['subtitle']): ?>
                                    <p style="color: var(--text-secondary); margin: 0.5rem 0;">
                                        <?= htmlspecialchars($slider['subtitle']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div style="margin: 1rem 0;">
                                    <small>ลำดับ: <?= $slider['sort_order'] ?></small><br>
                                    <small>สถานะ: <?= $slider['is_active'] ? '<span style="color: var(--success);">เปิด</span>' : '<span style="color: var(--error);">ปิด</span>' ?></small>
                                </div>
                                
                                <div class="slider-actions">
                                    <a href="edit-slider.php?id=<?= $slider['id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        ✏️ แก้ไข
                                    </a>
                                    <a href="?toggle=<?= $slider['id'] ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        <?= $slider['is_active'] ? '🔒 ปิด' : '🔓 เปิด' ?>
                                    </a>
                                    <a href="?delete=<?= $slider['id'] ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: var(--error);" onclick="return confirm('ต้องการลบ Slider นี้?')">
                                        🗑️ ลบ
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        ยังไม่มี Slider
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').style.display = 'block';
                    document.getElementById('preview-img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
