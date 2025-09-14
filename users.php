<?php
session_start();
include 'config.php';
if (!isset($_SESSION['loggedin']) || !is_admin($conn, $_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$error = '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// จัดการเพิ่ม/แก้ไข/ลบ users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $username = trim($_POST['username']);
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        // เช็คชื่อซ้ำ
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_stmt->bind_param("si", $username, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'ชื่อผู้ใช้ซ้ำกัน'];
        } else {
            $role = $_POST['role'];
            if ($action == 'add') {
                if (empty($_POST['password'])) {
                    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'กรุณากรอกรหัสผ่าน'];
                } else {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, password, role, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt->bind_param("sss", $username, $password, $role);
                    if ($stmt->execute()) {
                        $_SESSION['alert'] = ['type' => 'success', 'message' => 'เพิ่มผู้ใช้สำเร็จ'];
                    } else {
                        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้: ' . $conn->error];
                    }
                    $stmt->close();
                }
            } elseif ($action == 'edit') {
                if ($_POST['password'] !== '') {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=?, updated_at=NOW() WHERE id=?");
                    $stmt->bind_param("sssi", $username, $password, $role, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username=?, role=?, updated_at=NOW() WHERE id=?");
                    $stmt->bind_param("ssi", $username, $role, $id);
                }
                if ($stmt->execute()) {
                    $_SESSION['alert'] = ['type' => 'success', 'message' => 'อัปเดตผู้ใช้สำเร็จ'];
                } else {
                    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตผู้ใช้: ' . $conn->error];
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    } elseif (isset($_POST['delete_id'])) {
        $id = intval($_POST['delete_id']);
        if ($id == $user_id) {
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'ไม่สามารถลบผู้ใช้ตัวเองได้'];
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'ลบผู้ใช้สำเร็จ'];
            } else {
                $_SESSION['alert'] = ['type' => 'danger', 'message' => 'เกิดข้อผิดพลาดในการลบผู้ใช้: ' . $conn->error];
            }
            $stmt->close();
        }
    }
    // Redirect to prevent form resubmission
    header('Location: users.php' . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// ดึงข้อมูล users
$sql = "SELECT * FROM users";
if ($search_query) {
    $search_query = '%' . $conn->real_escape_string($search_query) . '%';
    $sql .= " WHERE username LIKE '$search_query'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้ - ฐานข้อมูลสนับสนุนรีโมท</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-user-cog me-2" style="color: #3b82f6;"></i>
                <span style="color: #374151; font-weight: 600;">จัดการผู้ใช้</span>
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

        <!-- Search & Add -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="position-relative">
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="ค้นหาชื่อผู้ใช้"
                                       value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3" style="color: #94a3b8; font-size: 0.8rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-7 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>ค้นหา
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus me-1"></i>เพิ่มผู้ใช้ใหม่
                                </button>
                                <?php if ($search_query): ?>
                                    <a href="users.php" class="btn btn-outline-secondary btn-sm">
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
                            <th style="width: 30%;"><i class="fas fa-user me-1"></i>ชื่อผู้ใช้</th>
                            <th class="text-center" style="width: 20%;"><i class="fas fa-user-shield me-1"></i>บทบาท</th>
                            <th class="text-center" style="width: 20%;"><i class="fas fa-clock me-1"></i>สร้างเมื่อ</th>
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
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['username']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $row['role'] == 'admin' ? 'bg-pastel-yellow' : 'bg-pastel-blue'; ?>">
                                            <?php echo $row['role'] == 'admin' ? 'Admin' : 'User'; ?>
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
                                                    data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                                    data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                                    title="แก้ไขผู้ใช้">
                                                <i class="fas fa-edit me-1"></i>แก้ไข
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('ลบผู้ใช้ <?php echo htmlspecialchars($row['username']); ?>?')">
                                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        title="ลบผู้ใช้"
                                                        <?php echo $row['id'] == $user_id ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-trash me-1"></i>ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                        <h5>ไม่พบข้อมูล</h5>
                                        <p>ไม่มีผู้ใช้ที่ตรงกับการค้นหาของคุณ</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                            <i class="fas fa-plus me-1"></i>เพิ่มผู้ใช้แรก
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
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    แสดงผล <?php echo $result->num_rows; ?> รายการ
                                    <?php if ($search_query): ?>
                                        สำหรับคำค้นหา "<?php echo htmlspecialchars($search_query); ?>"
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    อัปเดตล่าสุด: <?php echo date('d/m/Y H:i:s'); ?>
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
                        <i class="fas fa-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">บทบาท <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
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
                        <i class="fas fa-edit me-2"></i>แก้ไขผู้ใช้
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">บทบาท <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
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
                modal.querySelector('input[name="username"]').value = this.getAttribute('data-username');
                modal.querySelector('select[name="role"]').value = this.getAttribute('data-role');
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
