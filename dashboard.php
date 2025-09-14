<?php
session_start();
include 'config.php';
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$is_admin = is_admin($conn, $user_id);
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// จัดการ export CSV
if (isset($_GET['export_csv'])) {
    include 'includes/export_csv.php';
    exit;
}

// จัดการเพิ่ม/แก้ไข/ลบ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'includes/process_entries.php';
}

// ดึงข้อมูลรายการ
$sql = "SELECT e.*, c.name AS customer_name, t.type_name AS entry_type FROM entries e
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
$result = $conn->query($sql);

// ดึงลูกค้าและประเภทสำหรับ dropdown
$customers_result = $conn->query("SELECT id, name FROM customers WHERE status = 'active'");
$types_result = $conn->query("SELECT id, type_name FROM entry_types WHERE status = 'active'");
$types_for_filter = $conn->query("SELECT id, type_name FROM entry_types WHERE status = 'active'");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด - ฐานข้อมูลสนับสนุนรีโมท</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-database me-2" style="color: #3b82f6;"></i>
                <span style="color: #374151; font-weight: 600;">ฐานข้อมูลสนับสนุนรีโมท</span>
            </span>
            <div class="navbar-nav ms-auto">
                <span class="nav-text me-3 d-flex align-items-center">
                    <i class="fas fa-user-circle me-2" style="color: #64748b;"></i>
                    <span style="color: #374151;">ยินดีต้อนรับ <?php echo htmlspecialchars($_SESSION['username'] ?? 'ผู้ใช้'); ?></span>
                    <?php if ($is_admin): ?>
                        <span class="badge bg-pastel-yellow ms-2">
                            <i class="fas fa-crown me-1"></i>Admin
                        </span>
                    <?php endif; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>ออกจากระบบ
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Alerts -->
        <?php include 'alerts/alerts.php'; ?>

        <!-- Action Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="btn-group" role="group">
                            <a href="customers.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-users me-1"></i>จัดการลูกค้า
                            </a>
                            <a href="entry_types.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-tags me-1"></i>จัดการประเภท
                            </a>
                            <?php if ($is_admin): ?>
                                <a href="users.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-user-cog me-1"></i>จัดการผู้ใช้
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="?export_csv=1" class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i>Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="position-relative">
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="ค้นหา ชื่อลูกค้า ชื่อผู้ใช้ IP URL โน้ต..."
                                       value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3" style="color: #94a3b8; font-size: 0.8rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="entry_type" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">ทุกประเภท</option>
                                <?php while ($type_filter = $types_for_filter->fetch_assoc()): ?>
                                    <option value="<?php echo $type_filter['id']; ?>"
                                        <?php echo (isset($_GET['entry_type']) && $_GET['entry_type'] == $type_filter['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type_filter['type_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>ค้นหา
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus me-1"></i>เพิ่มรายการ
                                </button>
                                <?php if ($search_query || (isset($_GET['entry_type']) && $_GET['entry_type'] != '')): ?>
                                    <a href="?" class="btn btn-outline-secondary btn-sm">
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
                            <th class="text-center" style="width: 50px;">
                                <i class="fas fa-hashtag me-1"></i>ID
                            </th>
                            <th style="min-width: 160px;">
                                <i class="fas fa-user me-1"></i>ชื่อลูกค้า
                            </th>
                            <th class="text-center" style="width: 90px;">
                                <i class="fas fa-tag me-1"></i>ประเภท
                            </th>
                            <th style="min-width: 100px;">
                                <i class="fas fa-user-circle me-1"></i>ชื่อผู้ใช้
                            </th>
                            <th class="text-center" style="width: 120px;">
                                <i class="fas fa-key me-1"></i>รหัสผ่าน
                            </th>
                            <th style="min-width: 90px;">
                                <i class="fas fa-network-wired me-1"></i>IP
                            </th>
                            <th class="text-center" style="width: 50px;">
                                <i class="fas fa-link me-1"></i>URL
                            </th>
                            <th class="text-center" style="width: 60px;">
                                <i class="fas fa-plug me-1"></i>พอร์ต
                            </th>
                            <th style="min-width: 140px;">
                                <i class="fas fa-sticky-note me-1"></i>โน้ต
                            </th>
                            <th class="text-center" style="width: 130px;">
                                <i class="fas fa-cogs me-1"></i>การกระทำ
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-primary">
                                            <?php echo $row['id']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            <?php echo htmlspecialchars($row['customer_name']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($row['entry_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">
                                            <?php echo htmlspecialchars($row['username']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="password-field me-2">••••••••</span>
                                            <button class="btn btn-sm btn-outline-secondary"
                                                    onclick="togglePassword(this, '<?php echo htmlspecialchars(decryptData($row['password'])); ?>')"
                                                    title="แสดง/ซ่อนรหัสผ่าน">
                                                <i class="fas fa-eye" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-primary bg-light px-2 py-1 rounded" style="font-size: 0.7rem;">
                                            <?php echo htmlspecialchars($row['ip']); ?>
                                        </code>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['url']): ?>
                                            <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank"
                                               class="btn btn-sm btn-outline-primary" title="เปิดลิงก์ในแท็บใหม่">
                                                <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['port']): ?>
                                            <code class="text-success bg-light px-2 py-1 rounded">
                                                <?php echo htmlspecialchars($row['port']); ?>
                                            </code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 140px;" title="<?php echo htmlspecialchars($row['notes']); ?>">
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <?php echo htmlspecialchars(substr($row['notes'], 0, 30)) . (strlen($row['notes']) > 30 ? '...' : ''); ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-warning btn-sm edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-customer-id="<?php echo $row['customer_id']; ?>"
                                                    data-entry-type-id="<?php echo $row['entry_type_id']; ?>"
                                                    data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                                    data-password="<?php echo htmlspecialchars(decryptData($row['password'])); ?>"
                                                    data-ip="<?php echo htmlspecialchars($row['ip']); ?>"
                                                    data-url="<?php echo htmlspecialchars($row['url']); ?>"
                                                    data-port="<?php echo htmlspecialchars($row['port']); ?>"
                                                    data-notes="<?php echo htmlspecialchars($row['notes']); ?>"
                                                    title="แก้ไขรายการ"
                                                    style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                                <i class="fas fa-edit me-1" style="font-size: 0.65rem;"></i>แก้ไข
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้')">
                                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        title="ลบรายการ"
                                                        style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                                    <i class="fas fa-trash me-1" style="font-size: 0.65rem;"></i>ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                        <h5>ไม่พบข้อมูล</h5>
                                        <p>ไม่มีรายการที่ตรงกับการค้นหาของคุณ</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                            <i class="fas fa-plus me-1"></i>เพิ่มรายการแรก
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
    <?php include 'modals/add_modal.php'; ?>

    <!-- Edit Modal (Single Reusable Modal) -->
    <?php include 'modals/edit_modal.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/scripts.js"></script>

    <script>
        // Enhanced password toggle function
        function togglePassword(button, password) {
            const passwordField = button.previousElementSibling;
            const icon = button.querySelector('i');
            if (passwordField.textContent === '••••••••') {
                passwordField.textContent = password;
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                button.title = 'ซ่อนรหัสผ่าน';
            } else {
                passwordField.textContent = '••••••••';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                button.title = 'แสดงรหัสผ่าน';
            }
        }

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

        // Populate edit modal dynamically
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.querySelector('#editModal');
                modal.querySelector('input[name="id"]').value = this.getAttribute('data-id');
                modal.querySelector('select[name="customer_id"]').value = this.getAttribute('data-customer-id');
                modal.querySelector('select[name="entry_type_id"]').value = this.getAttribute('data-entry-type-id');
                modal.querySelector('input[name="username"]').value = this.getAttribute('data-username');
                modal.querySelector('input[name="password"]').value = this.getAttribute('data-password');
                modal.querySelector('input[name="ip"]').value = this.getAttribute('data-ip');
                modal.querySelector('input[name="url"]').value = this.getAttribute('data-url');
                modal.querySelector('input[name="port"]').value = this.getAttribute('data-port');
                modal.querySelector('textarea[name="notes"]').value = this.getAttribute('data-notes');
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
