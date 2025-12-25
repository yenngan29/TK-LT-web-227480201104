<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Nếu đã đăng nhập admin, chuyển về admin
if (isAdmin()) {
    redirect('admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = login($_POST['email'], $_POST['password']);
    
    if ($result['success']) {
        if ($result['user']['role'] === 'admin') {
            redirect('admin/');
        } else {
            logout();
            $error = 'Tài khoản này không có quyền admin';
        }
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Admin - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-header h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ QL Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="login.php">Đăng nhập KH</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-header">
            <span class="role-badge">👨‍💼 QUẢN TRỊ VIÊN</span>
            <h1>Đăng Nhập Admin</h1>
            <p style="color: #666;">Dành cho nhân viên quản lý</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Admin *</label>
                <input type="email" id="email" name="email" required class="form-control" 
                       placeholder="admin@huongviet.com">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu *</label>
                <input type="password" id="password" name="password" required class="form-control"
                       placeholder="Nhập mật khẩu admin">
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                🔐 Đăng Nhập Admin
            </button>
        </form>

        <div class="auth-links">
            <p>
                <a href="login.php" style="color: #667eea;">👤 Đăng nhập Khách hàng</a>
            </p>
        </div>

        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px;">
            <strong>🔒 Tài khoản admin demo:</strong><br>
            Email: <code>admin@huongviet.com</code><br>
            Password: <code>admin123</code>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>





