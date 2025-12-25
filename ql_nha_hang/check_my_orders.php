<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    die("<h1>Vui lòng đăng nhập</h1><p><a href='login.php'>Đăng nhập</a></p>");
}

$conn = getDBConnection();
$user = getCurrentUser();
$user_id = $_SESSION['user_id'];

// Lấy TẤT CẢ đơn hàng của user
$my_orders = $conn->query("
    SELECT o.*, t.table_number
    FROM orders o
    LEFT JOIN tables t ON o.table_id = t.id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
");

// Lấy đơn hàng KHÔNG có user_id (có thể là của bạn nhưng gọi trước khi đăng nhập)
$no_user_orders = $conn->query("
    SELECT o.*, t.table_number
    FROM orders o
    LEFT JOIN tables t ON o.table_id = t.id
    WHERE o.user_id IS NULL
    ORDER BY o.order_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm Tra Đơn Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 1000px; margin: 50px auto;">
        <div class="admin-section">
            <h1>🔍 Kiểm Tra Đơn Hàng Của: <?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p>User ID: <strong><?php echo $user_id; ?></strong></p>
            <p>Email: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
        </div>

        <div class="admin-section">
            <h2>✅ Đơn Hàng CỦA BẠN (Có user_id = <?php echo $user_id; ?>)</h2>
            
            <?php if ($my_orders->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Bàn</th>
                            <th>Ngày giờ</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $my_orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo $order['table_number']; ?></td>
                            <td><?php echo formatDateTime($order['order_date']); ?></td>
                            <td><strong><?php echo formatCurrency($order['total_amount']); ?></strong></td>
                            <td>
                                <span class="badge badge-<?php echo $order['status']; ?>">
                                    <?php 
                                    $status = [
                                        'pending' => 'Chờ',
                                        'preparing' => 'Đang làm',
                                        'completed' => 'Xong',
                                        'paid' => 'Đã thanh toán'
                                    ];
                                    echo $status[$order['status']];
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-success">
                <p>✅ <strong><?php echo $my_orders->num_rows; ?> đơn hàng</strong> được tìm thấy!</p>
                <p>Những đơn này sẽ hiển thị trong Dashboard của bạn.</p>
            </div>
            <?php else: ?>
            <div class="alert alert-error">
                <h3>❌ Không tìm thấy đơn hàng nào!</h3>
                <p><strong>Lý do có thể:</strong></p>
                <ul style="margin: 10px 0 0 20px; line-height: 2;">
                    <li>Bạn chưa gọi món lần nào</li>
                    <li>Hoặc bạn gọi món khi CHƯA đăng nhập</li>
                    <li>Hoặc gọi món TRƯỚC KHI code được sửa</li>
                </ul>
                <p style="margin-top: 15px;"><strong>Giải pháp:</strong></p>
                <ol style="margin: 10px 0 0 20px; line-height: 2;">
                    <li>Đảm bảo đã đăng nhập (đã có tên trên navigation)</li>
                    <li>Vào trang <a href="order.php">Gọi món</a></li>
                    <li>Chọn bàn và gọi món</li>
                    <li>Quay lại trang này → Sẽ thấy đơn!</li>
                </ol>
            </div>
            <?php endif; ?>
        </div>

        <div class="admin-section">
            <h2>⚠️ Đơn Hàng KHÔNG CÓ user_id (Gọi khi chưa login)</h2>
            
            <?php if ($no_user_orders->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Bàn</th>
                            <th>Ngày giờ</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $no_user_orders->fetch_assoc()): ?>
                        <tr style="background: #fef3c7;">
                            <td><strong>#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo $order['table_number']; ?></td>
                            <td><?php echo formatDateTime($order['order_date']); ?></td>
                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $order['status']; ?>">
                                    <?php 
                                    $status = [
                                        'pending' => 'Chờ',
                                        'preparing' => 'Đang làm',
                                        'completed' => 'Xong',
                                        'paid' => 'Đã thanh toán'
                                    ];
                                    echo $status[$order['status']];
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info">
                <p>⚠️ Những đơn này <strong>KHÔNG thuộc về ai</strong> (không có user_id)</p>
                <p>Có thể là:</p>
                <ul style="margin: 10px 0 0 20px;">
                    <li>Gọi món khi chưa đăng nhập</li>
                    <li>Gọi món trước khi code được sửa</li>
                    <li>Khách vãng lai (không có tài khoản)</li>
                </ul>
            </div>
            <?php else: ?>
            <p style="text-align: center; color: #666;">Không có đơn nào không có user_id</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="customer_dashboard.php" class="btn btn-primary">📊 Về Dashboard</a>
            <a href="order.php" class="btn btn-secondary">🍽️ Gọi món mới</a>
        </div>
    </div>
</body>
</html>





