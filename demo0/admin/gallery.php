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

// Upload multiple images
function uploadGalleryImage($file) {
    $target_dir = '../assets/uploads/gallery/';
    if(!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if(!in_array($ext, $allowed)) {
        return false;
    }
    
    if($file['size'] > 5000000) { // 5MB
        return false;
    }
    
    $new_filename = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $upload_path = $target_dir . $new_filename;
    
    if(move_uploaded_file($file['tmp_name'], $upload_path)) {
        return '/assets/uploads/gallery/' . $new_filename;
    }
    
    return false;
}

// Add images
if(isset($_POST['add_images'])) {
    $uploaded = 0;
    $failed = 0;
    
    if(isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $category = $_POST['category'] ?? 'general';
        
        for($i = 0; $i < count($files['name']); $i++) {
            if($files['error'][$i] == 0) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $image_path = uploadGalleryImage($file);
                if($image_path) {
                    $stmt = $pdo->prepare("INSERT INTO gallery (title, image_path, category) VALUES (?, ?, ?)");
                    $stmt->execute([
                        pathinfo($files['name'][$i], PATHINFO_FILENAME),
                        $image_path,
                        $category
                    ]);
                    $uploaded++;
                } else {
                    $failed++;
                }
            }
        }
    }
    
    if($uploaded > 0) {
        $message = "อัพโหลดสำเร็จ $uploaded รูป";
    }
    if($failed > 0) {
        $error = "อัพโหลดไม่สำเร็จ $failed รูป";
    }
}

// Delete image
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $image = $stmt->fetch();
    
    if($image && $image['image_path'] && file_exists('..' . $image['image_path'])) {
        unlink('..' . $image['image_path']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $message = 'ลบรูปภาพเรียบร้อยแล้ว';
}

// Get categories
$stmt = $pdo->query("SELECT DISTINCT category FROM gallery");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get gallery images
$category_filter = $_GET['category'] ?? '';
if($category_filter) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY sort_order, id DESC");
    $stmt->execute([$category_filter]);
} else {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY sort_order, id DESC");
}
$images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ Gallery - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: var(--dark-surface);
            aspect-ratio: 1;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .category-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .category-tab {
            padding: 0.5rem 1rem;
            background: var(--dark-surface);
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }
        .category-tab.active,
        .category-tab:hover {
            background: var(--primary-color);
            color: white;
        }
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.05);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>📸 จัดการ Gallery</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <!-- Upload Form -->
            <div class="content-card">
                <h2>อัพโหลดรูปภาพ</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-area">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📷</div>
                        <h3>เลือกรูปภาพเพื่ออัพโหลด</h3>
                        <p style="color: var(--text-secondary); margin: 1rem 0;">
                            รองรับ JPG, PNG, GIF, WebP (สูงสุด 5MB ต่อไฟล์)
                        </p>
                        
                        <div class="form-group" style="max-width: 300px; margin: 1rem auto;">
                            <label for="category">หมวดหมู่</label>
                            <select name="category" id="category">
                                <option value="general">ทั่วไป</option>
                                <option value="equipment">อุปกรณ์</option>
                                <option value="classes">คลาส</option>
                                <option value="trainers">เทรนเนอร์</option>
                                <option value="members">สมาชิก</option>
                                <option value="events">กิจกรรม</option>
                            </select>
                        </div>
                        
                        <input type="file" name="images[]" multiple accept="image/*" required class="btn btn-primary">
                        <button type="submit" name="add_images" class="btn btn-secondary" style="margin-left: 1rem;">
                            ⬆️ อัพโหลด
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Gallery Display -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    <h2>รูปภาพทั้งหมด (<?= count($images) ?> รูป)</h2>
                    
                    <!-- Category Filter -->
                    <div class="category-tabs">
                        <a href="?category=" class="category-tab <?= !$category_filter ? 'active' : '' ?>">
                            ทั้งหมด
                        </a>
                        <?php foreach(['general' => 'ทั่วไป', 'equipment' => 'อุปกรณ์', 'classes' => 'คลาส', 'trainers' => 'เทรนเนอร์', 'members' => 'สมาชิก', 'events' => 'กิจกรรม'] as $key => $label): ?>
                            <a href="?category=<?= $key ?>" class="category-tab <?= $category_filter == $key ? 'active' : '' ?>">
                                <?= $label ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if(count($images) > 0): ?>
                    <div class="gallery-grid">
                        <?php foreach($images as $image): ?>
                        <div class="gallery-item">
                            <img src="..<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
                            <div class="gallery-overlay">
                                <a href="..<?= htmlspecialchars($image['image_path']) ?>" target="_blank" class="btn btn-primary" style="padding: 0.5rem;">
                                    👁️
                                </a>
                                <a href="?delete=<?= $image['id'] ?>" class="btn btn-secondary" style="padding: 0.5rem; background: var(--error);" onclick="return confirm('ต้องการลบรูปนี้?')">
                                    🗑️
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                        ยังไม่มีรูปภาพ<?= $category_filter ? 'ในหมวดหมู่นี้' : '' ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
