<?php
// Test kết nối đơn giản
echo "<h1>Test Kết Nối MySQL</h1>";
echo "<hr>";

// Thử kết nối
echo "<h2>Đang thử kết nối...</h2>";

$hosts = ['127.0.0.1', 'localhost', 'localhost:3306', '127.0.0.1:3306'];

foreach ($hosts as $host) {
    echo "<p><strong>Thử với: $host</strong><br>";
    
    try {
        $conn = @new mysqli($host, 'root', '');
        
        if ($conn->connect_error) {
            echo "❌ KHÔNG KẾT NỐI ĐƯỢC<br>";
            echo "Lỗi: " . $conn->connect_error . "</p>";
        } else {
            echo "✅ <span style='color: green;'>KẾT NỐI THÀNH CÔNG!</span><br>";
            echo "MySQL Version: " . $conn->server_info . "<br>";
            
            // Kiểm tra database
            $result = $conn->query("SHOW DATABASES LIKE 'ql_nha_hang'");
            if ($result && $result->num_rows > 0) {
                echo "✅ <span style='color: green;'>Database 'ql_nha_hang' đã tồn tại!</span><br>";
                echo "👉 <strong>Hệ thống sẵn sàng! Hãy sửa config.php dùng: '$host'</strong></p>";
                
                echo "<hr><h2 style='color: green;'>🎉 THÀNH CÔNG!</h2>";
                echo "<p>Làm theo 2 bước:</p>";
                echo "<ol style='font-size: 18px;'>";
                echo "<li>Mở file <code>config.php</code></li>";
                echo "<li>Sửa dòng 6: <code>define('DB_HOST', '$host');</code></li>";
                echo "<li>Lưu file và truy cập: <a href='index.php'>index.php</a></li>";
                echo "</ol>";
                break;
            } else {
                echo "⚠️ <span style='color: orange;'>Database 'ql_nha_hang' CHƯA TỒN TẠI</span><br>";
                echo "👉 Cần import file database.sql</p>";
                
                echo "<hr><h2>📝 Cách tạo database:</h2>";
                echo "<h3>Cách 1: Dùng phpMyAdmin (Dễ nhất)</h3>";
                echo "<ol>";
                echo "<li>Mở: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
                echo "<li>Click tab 'Import'</li>";
                echo "<li>Click 'Choose File' và chọn file <code>database.sql</code> trong thư mục: <code>C:\\laragon\\www\\QL_Khach_San\\database.sql</code></li>";
                echo "<li>Click nút 'Go' ở cuối trang</li>";
                echo "<li>Đợi thông báo 'Import has been successfully finished'</li>";
                echo "<li>Quay lại trang này và refresh (F5)</li>";
                echo "</ol>";
                
                echo "<h3>Cách 2: Dùng Command (Nhanh hơn)</h3>";
                echo "<p>Copy và chạy lệnh này trong PowerShell:</p>";
                echo "<pre style='background: #f0f0f0; padding: 10px;'>cd C:\\laragon\\www\\QL_Khach_San\nmysql -u root < database.sql</pre>";
                
                echo "<p><strong>Sau khi import xong, sửa config.php dùng: '$host'</strong></p>";
                break;
            }
            
            $conn->close();
        }
    } catch (Exception $e) {
        echo "❌ LỖI: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='test_simple.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>🔄 Kiểm tra lại</a></p>";
?>





