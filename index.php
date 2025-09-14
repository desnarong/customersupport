<?php
session_start();
include 'config.php';

// ถ้า login แล้วให้ไปหน้า dashboard
if (isset($_SESSION['loggedin'])) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? AND status = 'active'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // อัปเดต last_login
                $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }
        } else {
            $error_message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ฐานข้อมูลสนับสนุนรีโมท</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 420px;
            margin: 0 auto;
            padding-top: 100px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-icon {
            width: 80px;
            height: 80px;
            background-color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        
        .login-title {
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-subtitle {
            color: #666;
            font-size: 0.9rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-control {
            padding-left: 45px;
        }
        
        .input-group .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 10;
        }
        
        .btn-login {
            background-color: #0d6efd;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(13, 110, 253, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
        }
        
        .footer-text {
            text-align: center;
            color: #666;
            font-size: 0.85rem;
            margin-top: 30px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
                padding-top: 50px;
            }
            
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h2 class="login-title">เข้าสู่ระบบ</h2>
                    <p class="login-subtitle">ฐานข้อมูลสนับสนุนรีโมท</p>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">ชื่อผู้ใช้</label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="username" class="form-control" 
                                   placeholder="กรอกชื่อผู้ใช้" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                   required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">รหัสผ่าน</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="กรอกรหัสผ่าน" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                </form>
            </div>
            
            <div class="footer-text">
                <i class="fas fa-shield-alt me-1"></i>
                ระบบปลอดภัยด้วยการเข้ารหัส
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ป้องกัน multiple submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังเข้าสู่ระบบ...';
            
            // เปิดใช้งานใหม่หลัง 3 วินาที (ในกรณีเกิดข้อผิดพลาด)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ';
            }, 3000);
        });
        
        // Auto focus ที่ช่องรหัสผ่านเมื่อกด Enter ที่ช่องชื่อผู้ใช้
        document.querySelector('input[name="username"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('input[name="password"]').focus();
            }
        });
        
        // เคลียร์ error message เมื่อเริ่มพิมพ์ใหม่
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const alert = document.querySelector('.alert-danger');
                if (alert) {
                    alert.style.opacity = '0.5';
                }
            });
        });
    </script>
</body>
</html>
