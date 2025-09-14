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

// Function to backup database
function backupDatabase($pdo) {
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $output = "-- Database Backup\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    
    foreach($tables as $table) {
        $output .= "-- Table structure for `$table`\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        
        $result = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $result->fetch(PDO::FETCH_NUM);
        $output .= $row[1] . ";\n\n";
        
        // Get table data
        $result = $pdo->query("SELECT * FROM `$table`");
        $numFields = $result->columnCount();
        
        $output .= "-- Dumping data for table `$table`\n";
        while($row = $result->fetch(PDO::FETCH_NUM)) {
            $output .= "INSERT INTO `$table` VALUES(";
            for($i = 0; $i < $numFields; $i++) {
                if($row[$i] === null) {
                    $output .= "NULL";
                } else {
                    $output .= "'" . addslashes($row[$i]) . "'";
                }
                if($i < $numFields - 1) {
                    $output .= ',';
                }
            }
            $output .= ");\n";
        }
        $output .= "\n";
    }
    
    return $output;
}

// Handle backup request
if(isset($_POST['backup'])) {
    try {
        $backup = backupDatabase($pdo);
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        
        // Create backups directory if not exists
        $backup_dir = '../backups/';
        if(!is_dir($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }
        
        // Save to file
        file_put_contents($backup_dir . $filename, $backup);
        
        // Force download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($backup));
        echo $backup;
        exit;
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาดในการ backup: ' . $e->getMessage();
    }
}

// Handle restore request
if(isset($_FILES['restore_file']) && $_FILES['restore_file']['error'] == 0) {
    try {
        $sql = file_get_contents($_FILES['restore_file']['tmp_name']);
        
        // Split SQL into individual queries
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        // Execute each query
        foreach($queries as $query) {
            if(!empty($query)) {
                $pdo->exec($query);
            }
        }
        
        $message = 'กู้คืนฐานข้อมูลเรียบร้อยแล้ว';
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาดในการกู้คืน: ' . $e->getMessage();
    }
}

// Get backup files
$backup_files = [];
$backup_dir = '../backups/';
if(is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach($files as $file) {
        if(pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            $backup_files[] = [
                'name' => $file,
                'size' => filesize($backup_dir . $file),
                'date' => filemtime($backup_dir . $file)
            ];
        }
    }
    // Sort by date descending
    usort($backup_files, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup & Restore - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>💾 Backup & Restore</h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <!-- Backup Section -->
                <div class="content-card">
                    <h2>📥 สำรองข้อมูล (Backup)</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                        สำรองฐานข้อมูลทั้งหมดเป็นไฟล์ SQL
                    </p>
                    <form method="POST">
                        <button type="submit" name="backup" class="btn btn-primary" style="width: 100%;">
                            💾 สำรองข้อมูลเดี๋ยวนี้
                        </button>
                    </form>
                </div>
                
                <!-- Restore Section -->
                <div class="content-card">
                    <h2>📤 กู้คืนข้อมูล (Restore)</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                        อัพโหลดไฟล์ SQL เพื่อกู้คืนฐานข้อมูล
                    </p>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="restore_file" accept=".sql" required>
                        </div>
                        <button type="submit" class="btn btn-secondary" style="width: 100%;" 
                                onclick="return confirm('คำเตือน: การกู้คืนจะลบข้อมูลปัจจุบันทั้งหมด ต้องการดำเนินการต่อ?')">
                            📤 กู้คืนจากไฟล์
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Backup History -->
            <?php if(count($backup_files) > 0): ?>
            <div class="content-card" style="margin-top: 2rem;">
                <h2>📁 ไฟล์สำรองข้อมูลที่มีอยู่</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ชื่อไฟล์</th>
                                <th>ขนาด</th>
                                <th>วันที่สร้าง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($backup_files as $file): ?>
                            <tr>
                                <td><?= htmlspecialchars($file['name']) ?></td>
                                <td><?= number_format($file['size'] / 1024, 2) ?> KB</td>
                                <td><?= date('d/m/Y H:i', $file['date']) ?></td>
                                <td>
                                    <a href="../backups/<?= htmlspecialchars($file['name']) ?>" 
                                       download class="btn btn-primary" 
                                       style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        ⬇️ ดาวน์โหลด
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Instructions -->
            <div class="content-card" style="margin-top: 2rem;">
                <h2>📖 คำแนะนำ</h2>
                <ul style="color: var(--text-secondary); line-height: 1.8;">
                    <li>ควรสำรองข้อมูลอย่างน้อยสัปดาห์ละครั้ง</li>
                    <li>เก็บไฟล์สำรองไว้ในที่ปลอดภัย</li>
                    <li>ทดสอบการกู้คืนเป็นประจำ</li>
                    <li>ไฟล์สำรองจะถูกเก็บในโฟลเดอร์ /backups</li>
                    <li>ก่อนกู้คืนควรสำรองข้อมูลปัจจุบันก่อนเสมอ</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
