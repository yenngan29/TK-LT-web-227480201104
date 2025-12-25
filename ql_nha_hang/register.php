<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Nếu đã đăng nhập, chuyển về dashboard
if (isLoggedIn()) {
    redirect('customer_dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    
    // Validate
    if (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } else {
        $result = register($email, $password, $full_name, $phone);
        
        if ($result['success']) {
            redirect('customer_dashboard.php');
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
    <title>Đăng Ký - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container {
            max-width: 500px;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
                <li><a href="register.php" class="active">Đăng ký</a></li>
                <li><a href="login.php">Đăng nhập</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-header">
            <h1>Đăng Ký Tài Khoản</h1>
            <p style="color: #666;">Tạo tài khoản để quản lý đặt bàn và đơn hàng</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="full_name">Họ và tên *</label>
                <input type="text" id="full_name" name="full_name" required class="form-control"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                       placeholder="Nguyễn Văn A">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Số điện thoại *</label>
                    <input type="tel" id="phone" name="phone" required class="form-control"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                           placeholder="0912345678">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required class="form-control"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="email@example.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mật khẩu *</label>
                    <input type="password" id="password" name="password" required class="form-control"
                           placeholder="Tối thiểu 6 ký tự">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Xác nhận MK *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="form-control"
                           placeholder="Nhập lại mật khẩu">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                ✅ Đăng Ký
            </button>
        </form>

        <div class="auth-links">
            <p>Đã có tài khoản? <a href="login.php" style="color: #667eea; font-weight: 600;">Đăng nhập ngay</a></p>
        </div>

        <div style="background: #e0e7ff; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px;">
            <strong>✨ Lợi ích khi đăng ký:</strong>
            <ul style="margin: 10px 0 0 20px; line-height: 2;">
                <li>Xem lịch sử đặt bàn</li>
                <li>Theo dõi đơn hàng</li>
                <li>Đặt bàn nhanh hơn</li>
                <li>Nhận ưu đãi đặc biệt</li>
            </ul>
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
