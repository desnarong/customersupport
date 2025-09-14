<?php
// ไฟล์สำหรับประมวลผลข้อมูลรายการ (เพิ่ม/แก้ไข/ลบ)

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $customer_id = $_POST['customer_id'];
    $entry_type_id = $_POST['entry_type_id'];
    $username = $_POST['username'];
    $password = encryptData($_POST['password']);
    $ip = $_POST['ip'];
    $url = $_POST['url'];
    $port = $_POST['port'];
    $notes = $_POST['notes'];

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO entries (customer_id, entry_type_id, username, password, ip, url, port, notes, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssisi", $customer_id, $entry_type_id, $username, $password, $ip, $url, $port, $notes, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "เพิ่มรายการสำเร็จ";
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเพิ่มรายการ";
        }
        
    } elseif ($action == 'edit') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE entries SET customer_id=?, entry_type_id=?, username=?, password=?, ip=?, url=?, port=?, notes=? WHERE id=? AND user_id=?");
        $stmt->bind_param("iissssisii", $customer_id, $entry_type_id, $username, $password, $ip, $url, $port, $notes, $id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "อัปเดตรายการสำเร็จ";
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดตรายการ";
        }
    }
    
    // Redirect เพื่อป้องกัน form resubmission
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit;
    
} elseif (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM entries WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "ลบรายการสำเร็จ";
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบรายการ";
    }
    
    // Redirect เพื่อป้องกัน form resubmission
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit;
}
?>
