<!-- modals/add_modal.php - Modal สำหรับเพิ่มรายการใหม่ -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">
                    <i class="fas fa-plus me-2"></i>เพิ่มรายการใหม่
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">เลือกชื่อลูกค้า</option>
                                <?php
                                // Reset cursor หรือ query ใหม่เพื่อให้แสดงข้อมูล
                                $customers_add = $conn->query("SELECT id, name FROM customers WHERE status = 'active' ORDER BY name");
                                while ($cust = $customers_add->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $cust['id']; ?>"><?php echo htmlspecialchars($cust['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                            <select name="entry_type_id" class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <?php
                                // Reset cursor หรือ query ใหม่เพื่อให้แสดงข้อมูล
                                $types_add = $conn->query("SELECT id, type_name FROM entry_types WHERE status = 'active' ORDER BY type_name");
                                while ($type = $types_add->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['type_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ใช้ </label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่าน </label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IP Address </label>
                            <input type="text" name="ip" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL</label>
                            <input type="url" name="url" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">พอร์ต</label>
                            <input type="number" name="port" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">โน้ต</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
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
