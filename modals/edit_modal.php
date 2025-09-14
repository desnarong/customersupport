<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit me-2"></i>แก้ไขรายการ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">เลือกชื่อลูกค้า</option>
                                <?php
                                $customers_result->data_seek(0);
                                while ($cust = $customers_result->fetch_assoc()): ?>
                                    <option value="<?php echo $cust['id']; ?>">
                                        <?php echo htmlspecialchars($cust['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                            <select name="entry_type_id" class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <?php
                                $types_result->data_seek(0);
                                while ($type = $types_result->fetch_assoc()): ?>
                                    <option value="<?php echo $type['id']; ?>">
                                        <?php echo htmlspecialchars($type['type_name']); ?>
                                    </option>
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
                            <i class="fas fa-save me-1"></i>อัปเดต
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
