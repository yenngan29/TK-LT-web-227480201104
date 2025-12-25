<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

// Lấy danh sách đơn hàng
$orders = $conn->query("
    SELECT o.*, t.table_number 
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    ORDER BY o.order_date DESC
    LIMIT 100
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - QL Nhà Hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ Quản Lý Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Tổng quan</a></li>
                <li><a href="dishes.php">Món ăn</a></li>
                <li><a href="tables.php">Bàn</a></li>
                <li><a href="reservations.php">Đặt bàn</a></li>
                <li><a href="orders.php" class="active">Đơn hàng</a></li>
                <li><a href="../index.php">Trang chủ</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Quản Lý Đơn Hàng</h1>
        </div>
    </div>

    <div class="container">
        <div class="admin-section">
            <h2>Danh Sách Đơn Hàng</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Bàn</th>
                            <th>Thời gian</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo $order['table_number']; ?></td>
                            <td><?php echo formatDateTime($order['order_date']); ?></td>
                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $order['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => 'Chờ xử lý',
                                        'preparing' => 'Đang làm',
                                        'completed' => 'Hoàn thành',
                                        'paid' => 'Đã thanh toán'
                                    ];
                                    echo $status_text[$order['status']];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if ($order['status'] == 'paid') {
                                    $methods = [
                                        'cash' => '💵 Tiền mặt',
                                        'card' => '💳 Thẻ',
                                        'transfer' => '📱 Chuyển khoản'
                                    ];
                                    echo $methods[$order['payment_method']] ?? '-';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Chi tiết</a>
                                <?php if ($order['status'] == 'paid'): ?>
                                <a href="../invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-secondary" target="_blank">Hóa đơn</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

