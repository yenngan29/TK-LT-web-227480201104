<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { 
            background: #d1fae5; 
            border: 3px solid #10b981; 
            color: #065f46;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .error { 
            background: #fee2e2; 
            border: 3px solid #ef4444; 
            color: #991b1b;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-size: 16px;
            font-weight: bold;
        }
        .btn:hover { background: #5568d3; }
        h1 { color: #333; }
        pre {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>🗄️ Import Database Tự Động</h1>
        <hr>

        <?php
        if (isset($_POST['import'])) {
            // Cấu hình
            $host = '127.0.0.1';
            $user = 'root';
            $pass = '';
            $port = 3309;
            
            try {
                // Kết nối MySQL
                $conn = new mysqli($host, $user, $pass, '', $port);
                
                if ($conn->connect_error) {
                    throw new Exception("Kết nối thất bại: " . $conn->connect_error);
                }
                
                echo "<div class='success'>";
                echo "<h2>✅ Bước 1: Kết nối MySQL thành công!</h2>";
                echo "<p>Host: $host:$port</p>";
                echo "</div>";
                
                // Đọc file SQL
                $sql_file = 'database.sql';
                if (!file_exists($sql_file)) {
                    throw new Exception("Không tìm thấy file database.sql");
                }
                
                $sql = file_get_contents($sql_file);
                
                echo "<div class='success'>";
                echo "<h2>✅ Bước 2: Đọc file SQL thành công!</h2>";
                echo "<p>File: $sql_file</p>";
                echo "</div>";
                
                // Tách các câu lệnh SQL
                $conn->multi_query($sql);
                
                // Đợi tất cả queries hoàn thành
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
                
                echo "<div class='success'>";
                echo "<h2>✅ Bước 3: Import database thành công!</h2>";
                echo "</div>";
                
                // Kiểm tra database đã tạo
                $result = $conn->query("SHOW DATABASES LIKE 'ql_nha_hang'");
                if ($result && $result->num_rows > 0) {
                    echo "<div class='success'>";
                    echo "<h2>✅ Bước 4: Database 'ql_nha_hang' đã được tạo!</h2>";
                    echo "</div>";
                    
                    // Kiểm tra tables
                    $conn->select_db('ql_nha_hang');
                    $tables = $conn->query("SHOW TABLES");
                    
                    echo "<div class='success'>";
                    echo "<h2>✅ Bước 5: Đã tạo " . $tables->num_rows . " bảng!</h2>";
                    echo "<details><summary>Xem danh sách bảng</summary><ul>";
                    while ($table = $tables->fetch_array()) {
                        echo "<li>" . $table[0] . "</li>";
                    }
                    echo "</ul></details>";
                    echo "</div>";
                    
                    echo "<div class='success' style='text-align: center; font-size: 20px;'>";
                    echo "<h1>🎉 HOÀN TẤT!</h1>";
                    echo "<p>Database đã được import thành công!</p>";
                    echo "<p>Hệ thống sẵn sàng sử dụng!</p>";
                    echo "<hr style='margin: 20px 0;'>";
                    echo "<h3>📝 Tài khoản đã tạo:</h3>";
                    echo "<div style='text-align: left; display: inline-block; margin: 20px auto;'>";
                    echo "<p><strong>👨‍💼 ADMIN:</strong><br>";
                    echo "Email: <code>admin@huongviet.com</code><br>";
                    echo "Password: <code>admin123</code></p>";
                    echo "<p><strong>👤 KHÁCH HÀNG:</strong><br>";
                    echo "Email: <code>khach1@gmail.com</code><br>";
                    echo "Password: <code>123456</code></p>";
                    echo "</div>";
                    echo "<hr style='margin: 20px 0;'>";
                    echo "<a href='login.php' class='btn'>👤 ĐĂNG NHẬP KHÁCH</a>";
                    echo "<a href='admin_login.php' class='btn'>👨‍💼 ĐĂNG NHẬP ADMIN</a>";
                    echo "</div>";
                } else {
                    throw new Exception("Database chưa được tạo");
                }
                
                $conn->close();
                
            } catch (Exception $e) {
                echo "<div class='error'>";
                echo "<h2>❌ Có lỗi xảy ra!</h2>";
                echo "<p><strong>Lỗi:</strong> " . $e->getMessage() . "</p>";
                echo "<h3>Cách khắc phục:</h3>";
                echo "<ol>";
                echo "<li>Đảm bảo Laragon đã Start (MySQL: Running)</li>";
                echo "<li>Kiểm tra port MySQL đúng là 3309</li>";
                echo "<li>Thử import qua phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
                echo "</ol>";
                echo "</div>";
            }
        } else {
            ?>
            <h2>Sẵn sàng import database</h2>
            <p>Hệ thống sẽ tự động:</p>
            <ul style="line-height: 2;">
                <li>✅ Kết nối MySQL (Port: 3309)</li>
                <li>✅ Đọc file database.sql</li>
                <li>✅ Tạo database 'ql_nha_hang'</li>
                <li>✅ Tạo tất cả bảng và dữ liệu mẫu</li>
            </ul>
            
            <form method="POST">
                <button type="submit" name="import" class="btn" style="font-size: 20px; padding: 20px 40px;">
                    🚀 BẮT ĐẦU IMPORT
                </button>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h3>📝 Hoặc import thủ công qua phpMyAdmin:</h3>
            <ol>
                <li>Mở: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                <li>Click tab "Import"</li>
                <li>Click "Choose File" và chọn: <code>C:\laragon\www\QL_Khach_San\database.sql</code></li>
                <li>Click "Go"</li>
            </ol>
            <?php
        }
        ?>
    </div>
</body>
</html>

