<?php
require_once 'config.php';

$conn = getDBConnection();
$success = [];
$errors = [];

// Đọc và thực thi file SQL
$sql_file = 'update_database_auth.sql';

if (file_exists($sql_file)) {
    $sql = file_get_contents($sql_file);
    
    // Tách các câu lệnh SQL
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
        $success[] = "Đã tạo bảng users thành công";
        $success[] = "Đã thêm cột user_id vào bảng reservations và orders";
        $success[] = "Đã tạo tài khoản admin và khách hàng mẫu";
    } else {
        $errors[] = "Lỗi: " . $conn->error;
    }
} else {
    $errors[] = "Không tìm thấy file update_database_auth.sql";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Hệ Thống Đăng Nhập</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 50px auto;">
        <div class="admin-section">
            <h1>🔐 Import Hệ Thống Đăng Nhập</h1>
            
            <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <h3>✅ Thành công!</h3>
                <?php foreach ($success as $msg): ?>
                <p>✓ <?php echo $msg; ?></p>
                <?php endforeach; ?>
            </div>
            
            <div style="background: #e0e7ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h3>📝 Tài khoản đã tạo:</h3>
                
                <h4 style="color: #667eea; margin-top: 15px;">👨‍💼 ADMIN:</h4>
                <p>Email: <code>admin@huongviet.com</code><br>
                Password: <code>admin123</code></p>
                
                <h4 style="color: #667eea; margin-top: 15px;">👤 KHÁCH HÀNG:</h4>
                <p>Email: <code>khach1@gmail.com</code><br>
                Password: <code>123456</code></p>
                
                <p>Email: <code>khach2@gmail.com</code><br>
                Password: <code>123456</code></p>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="login.php" class="btn btn-primary btn-lg">👤 Đăng nhập Khách hàng</a>
                <a href="admin_login.php" class="btn btn-secondary btn-lg">👨‍💼 Đăng nhập Admin</a>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <h3>❌ Có lỗi xảy ra:</h3>
                <?php foreach ($errors as $error): ?>
                <p>× <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>





