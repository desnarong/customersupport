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

// ตรวจสอบ ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: sliders.php');
    exit;
}

$slider_id = $_GET['id'];

// ดึงข้อมูล slider
$stmt = $pdo->prepare("SELECT * FROM sliders WHERE id = ?");
$stmt->execute([$slider_id]);
$slider = $stmt->fetch();

if(!$slider) {
    header('Location: sliders.php');
    exit;
}

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

// Update slider
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $image_path = $slider['image_path']; // Keep old image by default
        
        // Check if new image uploaded
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Delete old image
            if($slider['image_path'] && file_exists('..' . $slider['image_path'])) {
                unlink('..' . $slider['image_path']);
            }
            // Upload new image
            $image_path = uploadImage($_FILES['image']);
        }
        
        $sql = "UPDATE sliders SET 
                title = ?,
                subtitle = ?,
                image_path = ?,
                button_text = ?,
                button_link = ?,
                sort_order = ?,
                is_active = ?
                WHERE id = ?";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['title'],
            $_POST['subtitle'],
            $image_path,
            $_POST['button_text'],
            $_POST['button_link'],
            $_POST['sort_order'],
            isset($_POST['is_active']) ? 1 : 0,
            $slider_id
        ]);
        
        $message = 'บันทึกข้อมูลเรียบร้อยแล้ว';
        
        // Reload slider data
        $stmt = $pdo->prepare("SELECT * FROM sliders WHERE id = ?");
        $stmt->execute([$slider_id]);
        $slider = $stmt->fetch();
        
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
    <title>แก้ไข Slider - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .image-preview {
            max-width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: var(--dark-bg);
            margin-bottom: 1rem;
        }
        
        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: var(--dark-bg);
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.05);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <a href="sliders.php" class="back-link">
                ← กลับไปหน้า Sliders
            </a>
            
            <h1>✏️ แก้ไข Slider</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Current Image -->
                <div class="content-card">
                    <h2>รูปภาพปัจจุบัน</h2>
                    <?php if($slider['image_path']): ?>
                        <div class="image-preview">
                            <img src="..<?= htmlspecialchars($slider['image_path']) ?>" alt="Current slider image">
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-secondary);">ไม่มีรูปภาพ</p>
                    <?php endif; ?>
                    
                    <div class="upload-area">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">📷</div>
                        <h3>อัพโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</h3>
                        <p style="color: var(--text-secondary); margin: 0.5rem 0;">
                            แนะนำขนาด 1920x600px | JPG, PNG, GIF, WebP | สูงสุด 5MB
                        </p>
                        <input type="file" name="image" accept="image/*" onchange="previewNewImage(this)">
                        <div id="new-preview" style="margin-top: 1rem; display: none;">
                            <h4>รูปภาพใหม่:</h4>
                            <img id="preview-img" src="" style="max-width: 100%; border-radius: 8px;">
                        </div>
                    </div>
                </div>
                
                <!-- Slider Details -->
                <div class="content-card">
                    <h2>ข้อมูล Slider</h2>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title">หัวข้อหลัก *</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($slider['title']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subtitle">หัวข้อรอง</label>
                            <input type="text" id="subtitle" name="subtitle" value="<?= htmlspecialchars($slider['subtitle']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="button_text">ข้อความปุ่ม</label>
                            <input type="text" id="button_text" name="button_text" value="<?= htmlspecialchars($slider['button_text']) ?>" placeholder="เช่น ดูรายละเอียด">
                        </div>
                        
                        <div class="form-group">
                            <label for="button_link">ลิงก์ปุ่ม</label>
                            <input type="text" id="button_link" name="button_link" value="<?= htmlspecialchars($slider['button_link']) ?>" placeholder="/services">
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_order">ลำดับการแสดง</label>
                            <input type="number" id="sort_order" name="sort_order" value="<?= $slider['sort_order'] ?>" min="0">
                            <small style="color: var(--text-secondary);">ตัวเลขน้อยแสดงก่อน</small>
                        </div>
                        
                        <div class="form-group">
                            <label>สถานะ</label>
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_active" name="is_active" <?= $slider['is_active'] ? 'checked' : '' ?>>
                                <label for="is_active">เปิดใช้งาน Slider นี้</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary">💾 บันทึกการเปลี่ยนแปลง</button>
                    <a href="sliders.php" class="btn btn-secondary">ยกเลิก</a>
                    <a href="../" target="_blank" class="btn btn-secondary">👁️ ดูหน้าเว็บ</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function previewNewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('new-preview').style.display = 'block';
                    document.getElementById('preview-img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Auto hide alerts
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
