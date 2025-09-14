<?php
session_start();
include 'config.php';
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$is_admin = is_admin($conn, $user_id);
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// จัดการเพิ่ม/แก้ไข/ปรับ status entry types
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $type_name = trim($_POST['type_name']);
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (empty($type_name)) {
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'กรุณากรอกชื่อประเภทรายการ'];
        } else {
            // เช็คชื่อซ้ำ
            $check_stmt = $conn->prepare("SELECT id FROM entry_types WHERE type_name = ? AND id != ?");
            $check_stmt->bind_param("si", $type_name, $id);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $_SESSION['alert'] = ['type' => 'danger', 'message' => 'ชื่อประเภทซ้ำกับข้อมูลที่มีอยู่แล้ว'];
            } else {
                if ($action == 'add') {
                    $stmt = $conn->prepare("INSERT INTO entry_types (type_name, status, created_at, updated_at) VALUES (?, 'active', NOW(), NOW())");
                    $stmt->bind_param("s", $type_name);
                    if ($stmt->execute()) {
                        $_SESSION['alert'] = ['type' => 'success', 'message' => 'เพิ่มประเภทใหม่สำเร็จ'];
                    } else {
                        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'เกิดข้อผิดพลาดในการเพิ่มประเภท: ' . $conn->error];
                    }
                    $stmt->close();
                } elseif ($action == 'edit') {
                    $stmt = $conn->prepare("UPDATE entry_types SET type_name=?, updated_at=NOW() WHERE id=?");
                    $stmt->bind_param("si", $type_name, $id);
                    if ($stmt->execute()) {
                        $_SESSION['alert'] = ['type' => 'success', 'message' => 'อัปเดตข้อมูลประเภทสำเร็จ'];
                    } else {
                        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $conn->error];
                    }
                    $stmt->close();
                }
            }
            $check_stmt->close();
        }
    } elseif (isset($_POST['toggle_status_id'])) {
        $id = intval($_POST['toggle_status_id']);
        $new_status = $_POST['new_status'];

        if (in_array($new_status, ['active', 'inactive'])) {
            $stmt = $conn->prepare("UPDATE entry_types SET status=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("si", $new_status, $id);
            if ($stmt->execute()) {
                $status_text = $new_status == 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                $_SESSION['alert'] = ['type' => 'success', 'message' => "เปลี่ยนสถานะเป็น {$status_text} เรียบร้อย"];
            } else {
                $_SESSION['alert'] = ['type' => 'danger', 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $conn->error];
            }
            $stmt->close();
        }
    }

    // Redirect to prevent form resubmission
    header('Location: entry_types.php' . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// ดึงข้อมูล entry types พร้อมจำนวนรายการที่เกี่ยวข้อง
$sql = "SELECT et.*, COUNT(e.id) as entry_count, et.created_at, et.updated_at
        FROM entry_types et
        LEFT JOIN entries e ON et.id = e.entry_type_id
        WHERE 1=1";
if ($search_query) {
    $search_param = '%' . $conn->real_escape_string($search_query) . '%';
    $sql .= " AND et.type_name LIKE '$search_param'";
}
$sql .= " GROUP BY et.id ORDER BY et.created_at DESC";
$result = $conn->query($sql);

// นับจำนวน entry types แต่ละสถานะ
$stats_query = "SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_count
    FROM entry_types";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการประเภทรายการ - ฐานข้อมูลสนับสนุนรีโมท</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-tags me-2" style="color: #3b82f6;"></i>
                <span style="color: #374151; font-weight: 600;">จัดการประเภทรายการ</span>
            </span>
            <div class="navbar-nav ms-auto">
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>กลับแดชบอร์ด
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Alerts -->
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['alert']['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- Search & Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="position-relative">
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="ค้นหาชื่อประเภท..."
                                       value="<?php echo htmlspecialchars($search_query); ?>">
                                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3" style="color: #94a3b8; font-size: 0.8rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-7 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>ค้นหา
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus me-1"></i>เพิ่มประเภทใหม่
                                </button>
                                <?php if ($search_query): ?>
                                    <a href="entry_types.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>ล้าง
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%;"><i class="fas fa-hashtag me-1"></i>ID</th>
                            <th style="width: 30%;"><i class="fas fa-tag me-1"></i>ชื่อประเภท</th>
                            <th class="text-center" style="width: 15%;"><i class="fas fa-list me-1"></i>จำนวนรายการ</th>
                            <th class="text-center" style="width: 15%;"><i class="fas fa-toggle-on me-1"></i>สถานะ</th>
                            <th class="text-center" style="width: 20%;"><i class="fas fa-calendar me-1"></i>สร้างเมื่อ</th>
                            <th class="text-center" style="width: 20%;"><i class="fas fa-cogs me-1"></i>การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?php echo $row['id']; ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['type_name']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo $row['entry_count']; ?> รายการ</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $row['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                            <i class="fas fa-<?php echo $row['status'] == 'active' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                            <?php echo $row['status'] == 'active' ? 'ใช้งาน' : 'ปิดใช้งาน'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-warning btn-sm edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-type-name="<?php echo htmlspecialchars($row['type_name']); ?>"
                                                    title="แก้ไขประเภท">
                                                <i class="fas fa-edit me-1"></i>แก้ไข
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('<?php echo $row['status'] == 'active' ? 'ปิดใช้งาน' : 'เปิดใช้งาน'; ?>ประเภท <?php echo htmlspecialchars($row['type_name']); ?>?')">
                                                <input type="hidden" name="toggle_status_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="new_status" value="<?php echo $row['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                <button type="submit" class="btn btn-sm <?php echo $row['status'] == 'active' ? 'btn-danger' : 'btn-success'; ?>"
                                                        title="<?php echo $row['status'] == 'active' ? 'ปิดใช้งาน' : 'เปิดใช้งาน'; ?>ประเภท">
                                                    <i class="fas fa-toggle-<?php echo $row['status'] == 'active' ? 'off' : 'on'; ?> me-1"></i>
                                                    <?php echo $row['status'] == 'active' ? 'ปิด' : 'เปิด'; ?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                        <h5>ไม่พบข้อมูล</h5>
                                        <p>ไม่มีประเภทรายการที่ตรงกับการค้นหาของคุณ</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                            <i class="fas fa-plus me-1"></i>เพิ่มประเภทแรก
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="row align-items-center text-center text-md-start">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="fas fa-tags me-1"></i>
                                    ทั้งหมด <?php echo $stats['total']; ?> ประเภท
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    ใช้งาน <?php echo $stats['active_count']; ?> ประเภท
                                </small>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <small class="text-danger">
                                    <i class="fas fa-times-circle me-1"></i>
                                    ปิดใช้งาน <?php echo $stats['inactive_count']; ?> ประเภท
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">
                        <i class="fas fa-plus me-2"></i>เพิ่มประเภทใหม่
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addTypeForm">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">ชื่อประเภท <span class="text-danger">*</span></label>
                                <input type="text" name="type_name" class="form-control" required placeholder="กรอกชื่อประเภท เช่น Remote Desktop, VPN">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ประเภทใหม่จะถูกตั้งเป็น "ใช้งาน" โดยอัตโนมัติ
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>บันทึก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit me-2"></i>แก้ไขประเภทรายการ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">ชื่อประเภท <span class="text-danger">*</span></label>
                                <input type="text" name="type_name" class="form-control" required placeholder="กรอกชื่อประเภท เช่น Remote Desktop, VPN">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>อัปเดต
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/scripts.js"></script>
    <script>
        // Populate edit modal dynamically
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.querySelector('#editModal');
                modal.querySelector('input[name="id"]').value = this.getAttribute('data-id');
                modal.querySelector('input[name="type_name"]').value = this.getAttribute('data-type-name');
            });
        });

        // Auto-submit search form on Enter
        document.getElementById('searchForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.submit();
            }
        });

        // Add loading state to buttons
        document.querySelectorAll('form[method="POST"]').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });
        });

        // Input validation for type_name
        document.querySelectorAll('input[name="type_name"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.trim();
                if (this.value.length > 50) {
                    this.value = this.value.substring(0, 50);
                }
            });
        });

        // Auto focus for add modal
        document.getElementById('addModal').addEventListener('shown.bs.modal', function() {
            document.querySelector('#addModal input[name="type_name"]').focus();
        });

        // Fix modal z-index issues
        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach((modal, index) => {
                modal.style.setProperty('z-index', '99999', 'important');
                modal.addEventListener('show.bs.modal', function(e) {
                    this.style.setProperty('z-index', '99999', 'important');
                    setTimeout(() => {
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.style.setProperty('z-index', '99998', 'important');
                        }
                    }, 10);
                });
                modal.addEventListener('shown.bs.modal', function(e) {
                    this.style.setProperty('z-index', '99999', 'important');
                    const dialog = this.querySelector('.modal-dialog');
                    if (dialog) {
                        dialog.style.setProperty('z-index', '100000', 'important');
                    }
                    const content = this.querySelector('.modal-content');
                    if (content) {
                        content.style.setProperty('z-index', '100001', 'important');
                    }
                });
            });
            document.addEventListener('show.bs.modal', function(e) {
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => {
                        backdrop.style.setProperty('z-index', '99998', 'important');
                    });
                }, 50);
            });
            setInterval(() => {
                const activeModal = document.querySelector('.modal.show');
                if (activeModal) {
                    activeModal.style.setProperty('z-index', '99999', 'important');
                }
            }, 100);
        });
    </script>
</body>
</html>
