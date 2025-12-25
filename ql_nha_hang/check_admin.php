<?php
require_once 'config.php';

$conn = getDBConnection();

echo "<h1>🔍 Kiểm Tra Tài Khoản Admin</h1>";
echo "<hr>";

// Kiểm tra bảng users có tồn tại không
$tables = $conn->query("SHOW TABLES LIKE 'users'");
if ($tables->num_rows == 0) {
    echo "<div style='background: #fee2e2; padding: 20px; border-radius: 10px; border: 2px solid #ef4444;'>";
    echo "<h2>❌ Bảng 'users' chưa tồn tại!</h2>";
    echo "<p>Bạn cần import lại database.</p>";
    echo "<ol>";
    echo "<li>Vào: <a href='http://localhost:8082/phpmyadmin'>phpMyAdmin</a></li>";
    echo "<li>Xóa database 'ql_nha_hang' (nếu có)</li>";
    echo "<li>Import lại file <code>database.sql</code></li>";
    echo "</ol>";
    echo "</div>";
    exit;
}

echo "<div style='background: #d1fae5; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<p>✅ Bảng 'users' đã tồn tại</p>";
echo "</div>";

// Lấy tất cả users
$users = $conn->query("SELECT id, email, full_name, role, status, created_at FROM users ORDER BY role, id");

echo "<h2>👥 Danh Sách Người Dùng:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th>ID</th><th>Email</th><th>Họ tên</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th>";
echo "</tr>";

$admin_count = 0;
$customer_count = 0;

while ($user = $users->fetch_assoc()) {
    $bg_color = $user['role'] == 'admin' ? '#fef3c7' : '#e0e7ff';
    echo "<tr style='background: $bg_color;'>";
    echo "<td>{$user['id']}</td>";
    echo "<td><strong>{$user['email']}</strong></td>";
    echo "<td>{$user['full_name']}</td>";
    echo "<td><strong>" . ($user['role'] == 'admin' ? '👨‍💼 ADMIN' : '👤 Khách') . "</strong></td>";
    echo "<td>{$user['status']}</td>";
    echo "<td>" . date('d/m/Y H:i', strtotime($user['created_at'])) . "</td>";
    echo "</tr>";
    
    if ($user['role'] == 'admin') $admin_count++;
    else $customer_count++;
}

echo "</table>";

echo "<div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;'>";
echo "<p>📊 Tổng: <strong>$admin_count</strong> Admin, <strong>$customer_count</strong> Khách hàng</p>";
echo "</div>";

// Kiểm tra admin account cụ thể
echo "<hr>";
echo "<h2>🔐 Kiểm Tra Tài Khoản Admin Cụ Thể:</h2>";

$admin_email = 'admin@huongviet.com';
$admin = $conn->query("SELECT * FROM users WHERE email = '$admin_email'")->fetch_assoc();

if ($admin) {
    echo "<div style='background: #d1fae5; padding: 20px; border-radius: 10px;'>";
    echo "<h3>✅ Tài khoản admin TỒN TẠI</h3>";
    echo "<p><strong>Email:</strong> {$admin['email']}</p>";
    echo "<p><strong>Họ tên:</strong> {$admin['full_name']}</p>";
    echo "<p><strong>Role:</strong> {$admin['role']}</p>";
    echo "<p><strong>Status:</strong> {$admin['status']}</p>";
    echo "<p><strong>Password hash:</strong> " . substr($admin['password'], 0, 30) . "...</p>";
    
    // Test password
    echo "<hr>";
    echo "<h3>🔑 Test Password:</h3>";
    $test_passwords = ['admin123', '123456', 'password', 'admin'];
    
    foreach ($test_passwords as $pwd) {
        if (password_verify($pwd, $admin['password'])) {
            echo "<p style='color: green; font-size: 18px;'>✅ <strong>PASSWORD ĐÚNG: '$pwd'</strong></p>";
        } else {
            echo "<p style='color: gray;'>⭕ Không phải: '$pwd'</p>";
        }
    }
    
    echo "</div>";
} else {
    echo "<div style='background: #fee2e2; padding: 20px; border-radius: 10px; border: 2px solid #ef4444;'>";
    echo "<h3>❌ KHÔNG TÌM THẤY tài khoản admin@huongviet.com</h3>";
    echo "<p>Bạn cần tạo tài khoản admin thủ công.</p>";
    echo "<p><strong>Cách tạo:</strong></p>";
    echo "<ol>";
    echo "<li>Mở phpMyAdmin: <a href='http://localhost:8082/phpmyadmin'>Link</a></li>";
    echo "<li>Chọn database 'ql_nha_hang'</li>";
    echo "<li>Chọn bảng 'users'</li>";
    echo "<li>Click 'Insert'</li>";
    echo "<li>Điền:<br>";
    echo "   - email: admin@huongviet.com<br>";
    echo "   - password: " . password_hash('admin123', PASSWORD_DEFAULT) . "<br>";
    echo "   - full_name: Quản Trị Viên<br>";
    echo "   - phone: 0901234567<br>";
    echo "   - role: admin<br>";
    echo "   - status: active</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='text-align: center;'>";
echo "<a href='admin_login.php' style='padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px;'>🔐 Thử đăng nhập Admin</a>";
echo "<a href='index.php' style='padding: 15px 30px; background: #6b7280; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px;'>🏠 Về trang chủ</a>";
echo "</div>";
?>





