<?php
// includes/export_csv.php - ไฟล์สำหรับส่งออกข้อมูล CSV

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=entries_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// เขียน BOM สำหรับ UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// เขียนหัวตาราง
fputcsv($output, [
    'ID', 
    'ชื่อลูกค้า', 
    'ประเภท', 
    'ชื่อผู้ใช้', 
    'รหัสผ่าน (เข้ารหัส)', 
    'IP', 
    'URL', 
    'พอร์ต', 
    'โน้ต', 
    'อัปเดตล่าสุด'
]);

// สร้าง SQL query
$sql = "SELECT e.id, c.name AS customer_name, t.type_name AS entry_type, e.username, e.password, e.ip, e.url, e.port, e.notes, e.last_updated 
        FROM entries e 
        JOIN customers c ON e.customer_id = c.id 
        JOIN entry_types t ON e.entry_type_id = t.id";

if (!$is_admin) {
    $sql .= " WHERE e.user_id = $user_id";
} else {
    $sql .= " WHERE 1=1";
}

if ($search_query) {
    $search_query = '%' . $conn->real_escape_string($search_query) . '%';
    $sql .= " AND (c.name LIKE '$search_query' OR t.type_name LIKE '$search_query' OR e.username LIKE '$search_query' OR e.ip LIKE '$search_query' OR e.url LIKE '$search_query' OR e.notes LIKE '$search_query')";
}

if (isset($_GET['entry_type']) && $_GET['entry_type'] != '') {
    $entry_type_filter = intval($_GET['entry_type']);
    $sql .= " AND e.entry_type_id = $entry_type_filter";
}

$sql .= " ORDER BY e.last_updated DESC";

$result = $conn->query($sql);

// เขียนข้อมูล
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['customer_name'],
        $row['entry_type'],
        $row['username'],
        $row['password'], // เก็บไว้ในรูปแบบเข้ารหัส
        $row['ip'],
        $row['url'],
        $row['port'],
        $row['notes'],
        $row['last_updated']
    ]);
}

fclose($output);
?>
