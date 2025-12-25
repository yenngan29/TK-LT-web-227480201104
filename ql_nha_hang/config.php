<?php
// Cấu hình kết nối database
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ql_nha_hang');
define('DB_PORT', 3309);  // ← PORT MYSQL CỦA BẠN

// Kết nối database
function getDBConnection() {
    // Tắt exception tự động để xử lý lỗi tốt hơn
    mysqli_report(MYSQLI_REPORT_ERROR);
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        // Hiển thị lỗi chi tiết
        $error_message = "
        <div style='font-family: Arial; padding: 20px; background: #fee; border: 2px solid #f00; border-radius: 10px; margin: 20px;'>
            <h2 style='color: #c00;'>❌ Lỗi Kết Nối Database</h2>
            <p><strong>Lỗi:</strong> {$e->getMessage()}</p>
            <hr>
            <h3>🔧 Cách khắc phục:</h3>
            <ol style='line-height: 2;'>
                <li><strong>Kiểm tra Laragon đã chạy chưa:</strong>
                    <ul>
                        <li>Mở Laragon</li>
                        <li>Click nút <strong>\"Start All\"</strong></li>
                        <li>Đợi cho đến khi thấy <strong>\"MySQL: Running\"</strong></li>
                    </ul>
                </li>
                <li><strong>Kiểm tra MySQL đang chạy:</strong>
                    <ul>
                        <li>Thử truy cập: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>
                        <li>Nếu vào được → MySQL OK, cần import database</li>
                        <li>Nếu không vào được → MySQL chưa chạy</li>
                    </ul>
                </li>
                <li><strong>Import Database:</strong>
                    <ul>
                        <li>Mở phpMyAdmin</li>
                        <li>Click tab \"Import\"</li>
                        <li>Chọn file <code>database.sql</code></li>
                        <li>Click \"Go\"</li>
                    </ul>
                </li>
                <li><strong>Thử đổi DB_HOST trong config.php:</strong>
                    <ul>
                        <li>Từ <code>'127.0.0.1'</code> → <code>'localhost'</code></li>
                        <li>Hoặc thử: <code>'localhost:3306'</code></li>
                    </ul>
                </li>
            </ol>
            <p style='background: #ffc; padding: 10px; border-radius: 5px;'>
                📖 <strong>Xem hướng dẫn chi tiết:</strong> Mở file <code>FIX_CONNECTION_ERROR.txt</code> trong thư mục dự án
            </p>
        </div>
        ";
        die($error_message);
    }
}

// Hàm helper
function redirect($url) {
    header("Location: $url");
    exit();
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
?>
