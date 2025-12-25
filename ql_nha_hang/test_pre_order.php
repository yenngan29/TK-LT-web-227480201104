<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    die("Vui lòng đăng nhập");
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

echo "<h1>🔍 Test Đặt Món Trước</h1>";
echo "<hr>";

// Kiểm tra có đặt bàn nào không
$reservations = $conn->query("
    SELECT r.*, t.table_number 
    FROM reservations r
    LEFT JOIN tables t ON r.table_id = t.id
    WHERE r.user_id = $user_id
    AND r.status IN ('pending', 'confirmed')
    ORDER BY r.reservation_date DESC
");

echo "<h2>📅 Các lượt đặt bàn của bạn:</h2>";

if ($reservations->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Ngày</th><th>Giờ</th><th>Số khách</th><th>Bàn</th><th>Table ID</th><th>Status</th><th>Action</th></tr>";
    
    while ($res = $reservations->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$res['id']}</td>";
        echo "<td>" . date('d/m/Y', strtotime($res['reservation_date'])) . "</td>";
        echo "<td>" . date('H:i', strtotime($res['reservation_time'])) . "</td>";
        echo "<td>{$res['number_of_guests']}</td>";
        echo "<td>{$res['table_number']}</td>";
        echo "<td><strong>{$res['table_id']}</strong></td>";
        echo "<td>{$res['status']}</td>";
        echo "<td><a href='pre_order.php?reservation_id={$res['id']}' style='background: #667eea; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px;'>Đặt món</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Bạn chưa có lượt đặt bàn nào!</p>";
    echo "<p><a href='reservation.php'>Đặt bàn ngay</a></p>";
}

echo "<hr>";
echo "<h2>🍽️ Đơn hàng đã đặt trước:</h2>";

$orders = $conn->query("
    SELECT o.*, t.table_number,
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o
    LEFT JOIN tables t ON o.table_id = t.id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
");

if ($orders->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Mã ĐH</th><th>Bàn</th><th>Ngày giờ</th><th>Số món</th><th>Tổng tiền</th><th>Trạng thái</th></tr>";
    
    while ($order = $orders->fetch_assoc()) {
        echo "<tr>";
        echo "<td>#" . str_pad($order['id'], 4, '0', STR_PAD_LEFT) . "</td>";
        echo "<td>{$order['table_number']}</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($order['order_date'])) . "</td>";
        echo "<td>{$order['item_count']} món</td>";
        echo "<td>" . number_format($order['total_amount'], 0, ',', '.') . " đ</td>";
        echo "<td>{$order['status']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Chưa có đơn hàng nào</p>";
}

echo "<hr>";
echo "<p><a href='customer_dashboard.php'>← Quay lại Dashboard</a></p>";
?>





