<?php
require_once 'config.php';

$conn = getDBConnection();

// Tạo password hash mới cho 'admin123'
$new_password = password_hash('admin123', PASSWORD_DEFAULT);

// Cập nhật password
$sql = "UPDATE users SET password = '$new_password' WHERE email = 'admin@huongviet.com'";

if ($conn->query($sql)) {
    echo "<div style='max-width: 600px; margin: 50px auto; padding: 30px; background: #d1fae5; border: 3px solid #10b981; border-radius: 10px;'>";
    echo "<h1 style='color: #065f46;'>✅ Cập Nhật Thành Công!</h1>";
    echo "<p style='font-size: 18px;'>Password admin đã được đổi thành: <strong>admin123</strong></p>";
    echo "<hr style='margin: 20px 0;'>";
    echo "<h3>🔐 Tài khoản admin mới:</h3>";
    echo "<p style='background: white; padding: 15px; border-radius: 8px;'>";
    echo "<strong>Email:</strong> admin@huongviet.com<br>";
    echo "<strong>Password:</strong> <code style='background: #e5e7eb; padding: 2px 8px; border-radius: 3px;'>admin123</code>";
    echo "</p>";
    echo "<div style='text-align: center; margin-top: 30px;'>";
    echo "<a href='admin_login.php' style='padding: 15px 40px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-size: 18px; font-weight: bold;'>🔐 Đăng Nhập Admin</a>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div style='max-width: 600px; margin: 50px auto; padding: 30px; background: #fee2e2; border: 3px solid #ef4444; border-radius: 10px;'>";
    echo "<h1>❌ Lỗi!</h1>";
    echo "<p>Không thể cập nhật password: " . $conn->error . "</p>";
    echo "</div>";
}
?>





