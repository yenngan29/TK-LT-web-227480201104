<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    die("<h1>Bạn chưa đăng nhập</h1><p><a href='login.php'>Đăng nhập</a></p>");
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm Tra Tài Khoản</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .check-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .account-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .account-info div {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .account-info div:last-child {
            border-bottom: none;
        }
        .role-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .role-customer {
            background: #e0e7ff;
            color: #667eea;
        }
        .role-admin {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="check-container">
        <h1>🔍 Thông Tin Tài Khoản</h1>
        
        <div class="account-info">
            <div>
                <strong>Họ tên:</strong>
                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
            </div>
            <div>
                <strong>Email:</strong>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div>
                <strong>Số điện thoại:</strong>
                <span><?php echo htmlspecialchars($user['phone']); ?></span>
            </div>
            <div>
                <strong>Vai trò:</strong>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php 
                    echo $user['role'] === 'admin' ? '👨‍💼 ADMIN' : '👤 KHÁCH HÀNG'; 
                    ?>
                </span>
            </div>
        </div>

        <?php if ($user['role'] === 'customer'): ?>
        <div class="alert alert-success">
            <h3>✅ Bạn là KHÁCH HÀNG</h3>
            <p>Bạn có thể:</p>
            <ul style="margin: 10px 0 0 20px;">
                <li>Xem menu</li>
                <li>Đặt bàn</li>
                <li>Xem lịch sử cá nhân</li>
            </ul>
            <p style="margin-top: 15px;">
                <a href="customer_dashboard.php" class="btn btn-primary">Vào Dashboard</a>
            </p>
        </div>
        <?php else: ?>
        <div class="alert" style="background: #fef3c7; border: 2px solid #f59e0b;">
            <h3>👨‍💼 Bạn là ADMIN</h3>
            <p>Bạn có thể:</p>
            <ul style="margin: 10px 0 0 20px;">
                <li>Quản lý món ăn</li>
                <li>Quản lý bàn</li>
                <li>Xem tất cả đơn hàng</li>
                <li>Xác nhận đặt bàn</li>
            </ul>
            <p style="margin-top: 15px;">
                <a href="admin/" class="btn btn-primary">Vào Trang Quản Lý</a>
            </p>
        </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-secondary">Về trang chủ</a>
            <a href="logout.php" class="btn btn-secondary">Đăng xuất</a>
        </div>
    </div>
</body>
</html>





