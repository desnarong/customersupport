<?php
session_start();
require_once '../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$settings = getSiteSettings($pdo);
$message = '';
$error = '';

// อัพเดทการตั้งค่า
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Handle logo upload
        $logo_path = $settings['logo_path'];
        if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed)) {
                $new_filename = 'logo_' . time() . '.' . $ext;
                $upload_path = '../assets/uploads/logo/' . $new_filename;
                
                // สร้างโฟลเดอร์ถ้ายังไม่มี
                if(!is_dir('../assets/uploads/logo/')) {
                    mkdir('../assets/uploads/logo/', 0777, true);
                }
                
                if(move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    // ลบไฟล์เก่า
                    if($settings['logo_path'] && file_exists('..' . $settings['logo_path'])) {
                        unlink('..' . $settings['logo_path']);
                    }
                    $logo_path = '/assets/uploads/logo/' . $new_filename;
                }
            }
        }
        
        $sql = "UPDATE settings SET 
                site_name = ?,
                site_tagline = ?,
                logo_path = ?,
                email = ?,
                phone = ?,
                address = ?,
                facebook_url = ?,
                instagram_url = ?,
                line_id = ?,
                meta_description = ?,
                meta_keywords = ?
                WHERE id = 1";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['site_name'],
            $_POST['site_tagline'],
            $logo_path,
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['facebook_url'],
            $_POST['instagram_url'],
            $_POST['line_id'],
            $_POST['meta_description'],
            $_POST['meta_keywords']
        ]);
        
        $message = 'บันทึกการตั้งค่าเรียบร้อยแล้ว';
        $settings = getSiteSettings($pdo); // โหลดข้อมูลใหม่
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
    <title>ตั้งค่าเว็บไซต์ - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>ตั้งค่าเว็บไซต์</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="content-card">
                    <h2>ข้อมูลทั่วไป</h2>
                    
                    <div class="form-group">
                        <label for="site_name">ชื่อเว็บไซต์</label>
                        <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_tagline">Tagline</label>
                        <input type="text" id="site_tagline" name="site_tagline" value="<?= htmlspecialchars($settings['site_tagline']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Logo (ปล่อยว่างถ้าไม่ต้องการเปลี่ยน)</label>
                        <input type="file" id="logo" name="logo" accept="image/*">
                        <?php if($settings['logo_path']): ?>
                            <p style="margin-top: 0.5rem; color: var(--text-secondary);">
                                Logo ปัจจุบัน: <img src="..<?= htmlspecialchars($settings['logo_path']) ?>" style="height: 40px; vertical-align: middle;">
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>ข้อมูลติดต่อ</h2>
                    
                    <div class="form-group">
                        <label for="email">อีเมล</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($settings['email']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">เบอร์โทรศัพท์</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($settings['phone']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">ที่อยู่</label>
                        <textarea id="address" name="address" rows="3"><?= htmlspecialchars($settings['address']) ?></textarea>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>โซเชียลมีเดีย</h2>
                    
                    <div class="form-group">
                        <label for="facebook_url">Facebook URL</label>
                        <input type="text" id="facebook_url" name="facebook_url" value="<?= htmlspecialchars($settings['facebook_url']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="instagram_url">Instagram URL</label>
                        <input type="text" id="instagram_url" name="instagram_url" value="<?= htmlspecialchars($settings['instagram_url']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="line_id">Line ID</label>
                        <input type="text" id="line_id" name="line_id" value="<?= htmlspecialchars($settings['line_id']) ?>">
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>SEO Settings</h2>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Description (สำหรับ SEO)</label>
                        <textarea id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($settings['meta_description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_keywords">Meta Keywords (คั่นด้วยเครื่องหมายจุลภาค)</label>
                        <textarea id="meta_keywords" name="meta_keywords" rows="2"><?= htmlspecialchars($settings['meta_keywords']) ?></textarea>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">💾 บันทึกการตั้งค่า</button>
                    <a href="dashboard.php" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
