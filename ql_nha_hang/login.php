<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Nếu đã đăng nhập, chuyển về dashboard
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/' : 'customer_dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Xử lý đăng nhập
        $result = login($_POST['email'], $_POST['password']);
        
        if ($result['success']) {
            // Chuyển về trang trước đó hoặc dashboard
            $redirect_url = $_SESSION['redirect_after_login'] ?? (isAdmin() ? 'admin/' : 'customer_dashboard.php');
            unset($_SESSION['redirect_after_login']);
            redirect($redirect_url);
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - QL Nhà Hàng</title>
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
            background: #e0e7ff;
            color: #667eea;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
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
                <li><a href="menu.php">Thực đơn</a></li>
                <li><a href="register.php">Đăng ký</a></li>
                <li><a href="login.php" class="active">Đăng nhập</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-header">
            <span class="role-badge">👤 Khách Hàng</span>
            <h1>Đăng Nhập</h1>
            <p style="color: #666;">Đăng nhập để xem lịch sử đặt bàn và đơn hàng</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required class="form-control" 
                       placeholder="example@email.com">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu *</label>
                <input type="password" id="password" name="password" required class="form-control"
                       placeholder="Nhập mật khẩu">
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-block">
                🔐 Đăng Nhập
            </button>
        </form>

        <div class="auth-links">
            <p>Chưa có tài khoản? <a href="register.php" style="color: #667eea; font-weight: 600;">Đăng ký ngay</a></p>
            <p style="margin-top: 15px;">
                <a href="admin_login.php" style="color: #666;">👨‍💼 Đăng nhập Admin</a>
            </p>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px;">
            <strong>🔒 Tài khoản demo:</strong><br>
            Email: <code>khach1@gmail.com</code><br>
            Password: <code>123456</code>
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
