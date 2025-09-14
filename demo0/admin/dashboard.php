<?php
session_start();
require_once '../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$settings = getSiteSettings($pdo);

// นับจำนวนหน้า
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pages");
$total_pages = $stmt->fetch()['total'];

// นับหน้าที่ active
$stmt = $pdo->query("SELECT COUNT(*) as active FROM pages WHERE is_active = 1");
$active_pages = $stmt->fetch()['active'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>Dashboard</h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['admin_username']) ?>! 👋
            </p>
            
            <!-- Stats Cards -->
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <h3>จำนวนหน้าทั้งหมด</h3>
                    <div class="value"><?= $total_pages ?></div>
                </div>
                <div class="dashboard-card">
                    <h3>หน้าที่เปิดใช้งาน</h3>
                    <div class="value"><?= $active_pages ?></div>
                </div>
                <div class="dashboard-card">
                    <h3>ชื่อเว็บไซต์</h3>
                    <div class="value" style="font-size: 1.5rem;"><?= htmlspecialchars($settings['site_name']) ?></div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="content-card">
                <h2>Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="settings.php" class="btn btn-primary">⚙️ แก้ไขการตั้งค่า</a>
                    <a href="pages.php" class="btn btn-secondary">📄 จัดการหน้าเว็บ</a>
                    <a href="../index.php" target="_blank" class="btn btn-secondary">🌐 ดูเว็บไซต์</a>
                </div>
            </div>
            
            <!-- Current Settings -->
            <div class="content-card">
                <h2>การตั้งค่าปัจจุบัน</h2>
                <div class="table-container">
                    <table>
                        <tr>
                            <th style="width: 200px;">รายการ</th>
                            <th>ค่า</th>
                        </tr>
                        <tr>
                            <td>ชื่อเว็บไซต์</td>
                            <td><?= htmlspecialchars($settings['site_name']) ?></td>
                        </tr>
                        <tr>
                            <td>Tagline</td>
                            <td><?= htmlspecialchars($settings['site_tagline']) ?></td>
                        </tr>
                        <tr>
                            <td>อีเมล</td>
                            <td><?= htmlspecialchars($settings['email']) ?></td>
                        </tr>
                        <tr>
                            <td>เบอร์โทร</td>
                            <td><?= htmlspecialchars($settings['phone']) ?></td>
                        </tr>
                        <tr>
                            <td>ที่อยู่</td>
                            <td><?= nl2br(htmlspecialchars($settings['address'])) ?></td>
                        </tr>
                        <tr>
                            <td>อัพเดทล่าสุด</td>
                            <td><?= date('d/m/Y H:i', strtotime($settings['updated_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
