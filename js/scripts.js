// Enhanced Password toggle functionality
function togglePassword(button, password) {
    const passwordField = button.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (passwordField.textContent === '••••••••') {
        passwordField.textContent = password;
        passwordField.style.fontFamily = "'SF Mono', 'Monaco', 'Cascadia Code', monospace";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
        button.classList.replace('btn-outline-secondary', 'btn-outline-primary');
        button.title = 'ซ่อนรหัสผ่าน';
        
        // Add copy functionality
        passwordField.style.cursor = 'pointer';
        passwordField.onclick = function() {
            navigator.clipboard.writeText(password).then(() => {
                showToast('คัดลอกรหัสผ่านเรียบร้อย!', 'success');
            });
        };
    } else {
        passwordField.textContent = '••••••••';
        passwordField.style.fontFamily = "'SF Mono', 'Monaco', 'Cascadia Code', monospace";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
        button.classList.replace('btn-outline-primary', 'btn-outline-secondary');
        button.title = 'แสดงรหัสผ่าน';
        passwordField.style.cursor = 'default';
        passwordField.onclick = null;
    }
}

// Toast notification system
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Enhanced form loading state
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            
            // Add spinner
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังดำเนินการ...';
            submitBtn.classList.add('loading');
            
            // Re-enable after 3 seconds (in case of errors)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                submitBtn.classList.remove('loading');
            }, 3000);
        }
    });
});

// Auto submit filter with loading state
document.querySelector('select[name="entry_type"]').addEventListener('change', function() {
    this.style.opacity = '0.6';
    this.form.submit();
});

// Enhanced confirm delete
document.querySelectorAll('.btn-danger').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Create custom confirmation modal
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                        </h5>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                        <p class="mb-3">คุณแน่ใจหรือไม่ที่จะลบรายการนี้?</p>
                        <p class="text-muted small">การดำเนินการนี้ไม่สามารถยกเลิกได้</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>ยกเลิก
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fas fa-trash me-1"></i>ลบรายการ
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.querySelector('#confirmDelete').addEventListener('click', () => {
            this.closest('form').submit();
        });
        
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    });
});

// Search shortcut and enhancements
document.addEventListener('keydown', function(e) {
    // Ctrl+F for search focus
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="search"]');
        searchInput.focus();
        searchInput.select();
    }
    
    // Ctrl+N for new entry
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        document.querySelector('[data-bs-target="#addModal"]').click();
    }
    
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput.value) {
            searchInput.value = '';
            searchInput.form.submit();
        }
    }
});

// Enhanced table interactions
document.querySelectorAll('.table-hover tbody tr').forEach(row => {
    // Add row click to edit functionality
    row.addEventListener('click', function(e) {
        if (!e.target.closest('button') && !e.target.closest('form') && !e.target.closest('a')) {
            const editBtn = this.querySelector('.btn-warning');
            if (editBtn) {
                editBtn.click();
            }
        }
    });
    
    // Add context menu
    row.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        const editBtn = this.querySelector('.btn-warning');
        if (editBtn) {
            editBtn.click();
        }
    });
});

// Auto-save form data to localStorage (for recovery)
document.querySelectorAll('form input, form textarea, form select').forEach(field => {
    const formId = field.closest('form').id || 'default-form';
    const fieldId = field.name || field.id;
    
    // Load saved data
    const savedValue = localStorage.getItem(`${formId}-${fieldId}`);
    if (savedValue && field.type !== 'password') {
        field.value = savedValue;
    }
    
    // Save data on change
    field.addEventListener('input', function() {
        if (this.type !== 'password') {
            localStorage.setItem(`${formId}-${fieldId}`, this.value);
        }
    });
});

// Clear saved form data on successful submit
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const formId = this.id || 'default-form';
        this.querySelectorAll('input, textarea, select').forEach(field => {
            const fieldId = field.name || field.id;
            localStorage.removeItem(`${formId}-${fieldId}`);
        });
    });
});

// Clear form when modal is hidden
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', function() {
        const form = this.querySelector('form');
        if (form && form.querySelector('input[name="action"]') && 
            form.querySelector('input[name="action"]').value === 'add') {
            form.reset();
            
            // Clear localStorage for this form
            const formId = form.id || 'default-form';
            form.querySelectorAll('input, textarea, select').forEach(field => {
                const fieldId = field.name || field.id;
                localStorage.removeItem(`${formId}-${fieldId}`);
            });
        }
    });
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Add smooth scrolling for better UX
document.documentElement.style.scrollBehavior = 'smooth';

// Performance: Lazy load heavy content
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate');
        }
    });
});

document.querySelectorAll('.card, .table-container').forEach(el => {
    observer.observe(el);
});
