<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

// Thống kê
$total_orders_today = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(order_date) = CURDATE()")->fetch_assoc()['count'];
$revenue_today = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(order_date) = CURDATE() AND status = 'paid'")->fetch_assoc()['total'] ?? 0;
$occupied_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE status = 'occupied'")->fetch_assoc()['count'];
$pending_reservations = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'")->fetch_assoc()['count'];

// Đơn hàng gần đây
$recent_orders = $conn->query("
    SELECT o.*, t.table_number 
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    ORDER BY o.order_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý - QL Nhà Hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ Quản Lý Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Tổng quan</a></li>
                <li><a href="dishes.php">Món ăn</a></li>
                <li><a href="tables.php">Bàn</a></li>
                <li><a href="reservations.php">Đặt bàn</a></li>
                <li><a href="orders.php">Đơn hàng</a></li>
                <li><a href="../index.php">Trang chủ</a></li>
                <li><a href="../logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Tổng Quan Hệ Thống</h1>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <h3><?php echo $total_orders_today; ?></h3>
                    <p>Đơn hàng hôm nay</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3><?php echo formatCurrency($revenue_today); ?></h3>
                    <p>Doanh thu hôm nay</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🪑</div>
                <div class="stat-info">
                    <h3><?php echo $occupied_tables; ?></h3>
                    <p>Bàn đang sử dụng</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-info">
                    <h3><?php echo $pending_reservations; ?></h3>
                    <p>Đặt bàn chờ xác nhận</p>
                </div>
            </div>
        </div>

        <div class="admin-section">
            <h2>Đơn Hàng Gần Đây</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Bàn</th>
                            <th>Thời gian</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
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

