<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm Tra Kết Nối Database</title>
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
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { border-left: 5px solid #10b981; background: #d1fae5; }
        .error { border-left: 5px solid #ef4444; background: #fee2e2; }
        .warning { border-left: 5px solid #f59e0b; background: #fef3c7; }
        .info { border-left: 5px solid #3b82f6; background: #dbeafe; }
        h1 { color: #333; }
        h2 { color: #667eea; margin-top: 0; }
        code { background: #e5e7eb; padding: 2px 8px; border-radius: 3px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover { background: #5568d3; }
        ul { line-height: 2; }
    </style>
</head>
<body>
    <h1>🔍 Kiểm Tra Kết Nối Database</h1>

    <?php
    // Thông tin cấu hình
    $config = [
        'host' => '127.0.0.1',
        'user' => 'root',
        'pass' => '',
        'db' => 'ql_nha_hang',
        'port' => 3306
    ];

    // Thử kết nối với 127.0.0.1
    echo '<div class="box info">';
    echo '<h2>📋 Thông Tin Cấu Hình</h2>';
    echo '<ul>';
    echo '<li><strong>Host:</strong> ' . $config['host'] . '</li>';
    echo '<li><strong>User:</strong> ' . $config['user'] . '</li>';
    echo '<li><strong>Password:</strong> ' . (empty($config['pass']) ? '(trống)' : '***') . '</li>';
    echo '<li><strong>Database:</strong> ' . $config['db'] . '</li>';
    echo '<li><strong>Port:</strong> ' . $config['port'] . '</li>';
    echo '<li><strong>PHP Version:</strong> ' . phpversion() . '</li>';
    echo '<li><strong>MySQLi Extension:</strong> ' . (extension_loaded('mysqli') ? '✅ Có' : '❌ Không có') . '</li>';
    echo '</ul>';
    echo '</div>';

    // Kiểm tra extension
    if (!extension_loaded('mysqli')) {
        echo '<div class="box error">';
        echo '<h2>❌ MySQLi Extension Không Có</h2>';
        echo '<p>PHP MySQLi extension chưa được kích hoạt. Vui lòng kích hoạt trong php.ini</p>';
        echo '</div>';
        exit;
    }

    // Test kết nối với 127.0.0.1
    echo '<div class="box">';
    echo '<h2>🔌 Test 1: Kết nối với 127.0.0.1</h2>';
    mysqli_report(MYSQLI_REPORT_ERROR);
    try {
        $conn = @new mysqli($config['host'], $config['user'], $config['pass'], '', $config['port']);
        if ($conn->connect_error) {
            throw new Exception($conn->connect_error);
        }
        echo '<p style="color: green;">✅ <strong>Kết nối MySQL thành công!</strong></p>';
        echo '<p>Server Version: ' . $conn->server_info . '</p>';
        
        // Kiểm tra database có tồn tại không
        $result = $conn->query("SHOW DATABASES LIKE '{$config['db']}'");
        if ($result->num_rows > 0) {
            echo '<p style="color: green;">✅ Database <code>' . $config['db'] . '</code> đã tồn tại</p>';
            
            // Kết nối vào database để kiểm tra tables
            $conn->select_db($config['db']);
            $tables = $conn->query("SHOW TABLES");
            echo '<p>Số lượng bảng: <strong>' . $tables->num_rows . '</strong></p>';
            
            if ($tables->num_rows > 0) {
                echo '<details><summary>Xem danh sách bảng</summary><ul>';
                while ($table = $tables->fetch_array()) {
                    echo '<li>' . $table[0] . '</li>';
                }
                echo '</ul></details>';
                
                echo '<p style="color: green; font-size: 18px;">🎉 <strong>HỆ THỐNG SẴN SÀNG SỬ DỤNG!</strong></p>';
                echo '<a href="index.php" class="btn">🏠 Về Trang Chủ</a>';
                echo '<a href="admin/" class="btn">👨‍💼 Trang Admin</a>';
            } else {
                echo '<div class="box warning">';
                echo '<h3>⚠️ Database trống</h3>';
                echo '<p>Database đã tạo nhưng chưa có bảng nào. Cần import file <code>database.sql</code></p>';
                echo '<ol>';
                echo '<li>Truy cập: <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>';
                echo '<li>Chọn database <code>ql_nha_hang</code> bên trái</li>';
                echo '<li>Click tab "Import"</li>';
                echo '<li>Chọn file <code>database.sql</code></li>';
                echo '<li>Click "Go"</li>';
                echo '</ol>';
                echo '</div>';
            }
        } else {
            echo '<div class="box warning">';
            echo '<h3>⚠️ Database chưa tồn tại</h3>';
            echo '<p>Database <code>' . $config['db'] . '</code> chưa được tạo</p>';
            echo '<h4>Cách khắc phục:</h4>';
            echo '<ol>';
            echo '<li>Truy cập: <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>';
            echo '<li>Click tab "Import"</li>';
            echo '<li>Chọn file <code>database.sql</code> trong thư mục dự án</li>';
            echo '<li>Click "Go"</li>';
            echo '</ol>';
            echo '</div>';
        }
        
        $conn->close();
    } catch (Exception $e) {
        echo '<p style="color: red;">❌ <strong>Không thể kết nối MySQL</strong></p>';
        echo '<p><strong>Lỗi:</strong> ' . $e->getMessage() . '</p>';
        
        echo '<div class="box error">';
        echo '<h3>🔧 Cách khắc phục:</h3>';
        echo '<ol>';
        echo '<li><strong>Mở Laragon</strong> và click <strong>"Start All"</strong></li>';
        echo '<li>Đợi cho đến khi thấy <strong>"MySQL: Running"</strong></li>';
        echo '<li>Refresh trang này (F5)</li>';
        echo '</ol>';
        echo '<p>Nếu vẫn lỗi, thử các cách sau:</p>';
        echo '<ul>';
        echo '<li>Restart Laragon: Stop All → đợi 5s → Start All</li>';
        echo '<li>Kiểm tra port MySQL trong Laragon (Menu → MySQL → Port)</li>';
        echo '<li>Nếu port không phải 3306, sửa trong <code>config.php</code></li>';
        echo '</ul>';
        echo '</div>';
    }
    echo '</div>';

    // Test kết nối với localhost
    echo '<div class="box">';
    echo '<h2>🔌 Test 2: Kết nối với localhost</h2>';
    try {
        $conn2 = @new mysqli('localhost', $config['user'], $config['pass'], '', $config['port']);
        if ($conn2->connect_error) {
            throw new Exception($conn2->connect_error);
        }
        echo '<p style="color: green;">✅ Kết nối với <code>localhost</code> thành công</p>';
        echo '<p>👉 Bạn có thể dùng <code>localhost</code> trong config.php</p>';
        $conn2->close();
    } catch (Exception $e) {
        echo '<p style="color: orange;">⚠️ Không kết nối được với <code>localhost</code></p>';
        echo '<p>👉 Hãy dùng <code>127.0.0.1</code> trong config.php (đã mặc định)</p>';
    }
    echo '</div>';

    // Port scanning
    echo '<div class="box">';
    echo '<h2>🔍 Kiểm Tra Port MySQL</h2>';
    $ports = [3306, 3307, 3308];
    foreach ($ports as $port) {
        $connection = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
        if ($connection) {
            echo '<p style="color: green;">✅ Port <strong>' . $port . '</strong> đang mở (có thể là MySQL)</p>';
            fclose($connection);
        } else {
            echo '<p style="color: gray;">⭕ Port ' . $port . ' không phản hồi</p>';
        }
    }
    echo '</div>';
    ?>

    <div class="box info">
        <h2>📚 Tài Liệu Tham Khảo</h2>
        <ul>
            <li>📄 <strong>FIX_CONNECTION_ERROR.txt</strong> - Hướng dẫn khắc phục lỗi chi tiết</li>
            <li>📄 <strong>HUONG_DAN_CAI_DAT.txt</strong> - Hướng dẫn cài đặt từ đầu</li>
            <li>📄 <strong>README.md</strong> - Tài liệu đầy đủ về hệ thống</li>
        </ul>
    </div>

    <div class="box">
        <h2>🔄 Làm mới</h2>
        <a href="check_connection.php" class="btn">🔄 Kiểm tra lại</a>
        <a href="index.php" class="btn">🏠 Về trang chủ</a>
    </div>
</body>
</html>





