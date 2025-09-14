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

// Toggle active status
if(isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    try {
        $stmt = $pdo->prepare("UPDATE pages SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_GET['toggle']]);
        $message = 'อัพเดทสถานะเรียบร้อยแล้ว';
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

// Delete page (ถ้าต้องการเพิ่มฟังก์ชันลบ)
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        // ห้ามลบหน้าหลัก
        $stmt = $pdo->prepare("SELECT page_slug FROM pages WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $page = $stmt->fetch();
        
        if($page && !in_array($page['page_slug'], ['home', 'about', 'services', 'contact'])) {
            $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            $message = 'ลบหน้าเรียบร้อยแล้ว';
        } else {
            $error = 'ไม่สามารถลบหน้าหลักได้';
        }
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

// ดึงข้อมูลหน้าทั้งหมด
try {
    $stmt = $pdo->query("SELECT * FROM pages ORDER BY sort_order, id");
    $pages = $stmt->fetchAll();
} catch(Exception $e) {
    $error = 'ไม่สามารถดึงข้อมูลได้: ' . $e->getMessage();
    $pages = [];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหน้า - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
            border: 1px solid var(--error);
        }
        .page-protected {
            opacity: 0.8;
            font-style: italic;
        }
        .add-new-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            table {
                min-width: 700px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <h1>📄 จัดการหน้าเว็บ</h1>
                <a href="add-page.php" class="btn btn-primary add-new-btn">
                    ➕ เพิ่มหน้าใหม่
                </a>
            </div>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;">ลำดับ</th>
                                <th>ชื่อหน้า</th>
                                <th>ชื่อเมนู</th>
                                <th>URL Slug</th>
                                <th style="width: 100px;">สถานะ</th>
                                <th style="width: 150px;">อัพเดทล่าสุด</th>
                                <th style="width: 200px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($pages) > 0): ?>
                                <?php foreach($pages as $page): ?>
                                <?php 
                                    $isProtected = in_array($page['page_slug'], ['home', 'about', 'services', 'contact']);
                                ?>
                                <tr class="<?= $isProtected ? 'page-protected' : '' ?>">
                                    <td style="text-align: center;">
                                        <strong><?= $page['sort_order'] ?></strong>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($page['page_title']) ?></strong>
                                        <?php if($isProtected): ?>
                                            <br><small style="color: var(--text-secondary);">(หน้าหลัก)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($page['menu_title']) ?></td>
                                    <td>
                                        <code style="background: var(--dark-bg); padding: 0.25rem 0.5rem; border-radius: 4px;">
                                            /<?= htmlspecialchars($page['page_slug']) ?>
                                        </code>
                                    </td>
                                    <td>
                                        <?php if($page['is_active']): ?>
                                            <span class="status-badge status-active">เปิด</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">ปิด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($page['updated_at'])) ?><br>
                                        <?= date('H:i', strtotime($page['updated_at'])) ?> น.</small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit-page.php?id=<?= $page['id'] ?>" 
                                               class="btn btn-primary btn-small"
                                               title="แก้ไข">
                                                ✏️ แก้ไข
                                            </a>
                                            
                                            <a href="?toggle=<?= $page['id'] ?>" 
                                               class="btn btn-secondary btn-small"
                                               title="เปิด/ปิด"
                                               onclick="return confirm('ต้องการเปลี่ยนสถานะหน้านี้?')">
                                                <?= $page['is_active'] ? '🔒' : '🔓' ?>
                                            </a>
                                            
                                            <a href="../<?= $page['page_slug'] ?>" 
                                               target="_blank"
                                               class="btn btn-secondary btn-small"
                                               title="ดูหน้า">
                                                👁️
                                            </a>
                                            
                                            <?php if(!$isProtected): ?>
                                                <a href="?delete=<?= $page['id'] ?>" 
                                                   class="btn btn-secondary btn-small"
                                                   style="background: var(--error);"
                                                   title="ลบ"
                                                   onclick="return confirm('คำเตือน: การลบหน้านี้ไม่สามารถกู้คืนได้\n\nต้องการลบหน้า <?= htmlspecialchars($page['page_title']) ?> หรือไม่?')">
                                                    🗑️
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">
                                        <p style="color: var(--text-secondary);">ไม่พบข้อมูลหน้าเว็บ</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- คำอธิบาย -->
            <div class="content-card" style="margin-top: 2rem;">
                <h3>📖 คำอธิบาย</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <strong>สัญลักษณ์:</strong>
                        <ul style="margin-top: 0.5rem; color: var(--text-secondary);">
                            <li>✏️ = แก้ไขเนื้อหาและ SEO</li>
                            <li>🔒/🔓 = เปิด/ปิดการแสดงผล</li>
                            <li>👁️ = ดูหน้าเว็บ</li>
                            <li>🗑️ = ลบหน้า (เฉพาะหน้าที่สร้างเพิ่ม)</li>
                        </ul>
                    </div>
                    <div>
                        <strong>หน้าหลัก:</strong>
                        <ul style="margin-top: 0.5rem; color: var(--text-secondary);">
                            <li>หน้าหลัก 4 หน้าไม่สามารถลบได้</li>
                            <li>สามารถแก้ไขเนื้อหาได้</li>
                            <li>สามารถปิดการแสดงผลได้</li>
                        </ul>
                    </div>
                    <div>
                        <strong>การเรียงลำดับ:</strong>
                        <ul style="margin-top: 0.5rem; color: var(--text-secondary);">
                            <li>เรียงตามหมายเลขลำดับ (sort_order)</li>
                            <li>แก้ไขลำดับได้ในหน้าแก้ไข</li>
                            <li>ค่า 0 = แสดงก่อน, ค่ามาก = แสดงหลัง</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto hide alerts after 5 seconds
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
