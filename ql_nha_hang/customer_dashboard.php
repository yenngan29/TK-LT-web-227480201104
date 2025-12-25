<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Yêu cầu đăng nhập
requireLogin();

$conn = getDBConnection();
$user = getCurrentUser();
$user_id = $_SESSION['user_id'];

$success_message = '';

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_order'])) {
    $order_id = intval($_POST['order_id']);
    
    // Kiểm tra đơn hàng có thuộc về user không
    $check = $conn->query("SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id AND status != 'paid'")->fetch_assoc();
    
    if ($check) {
        // Thanh toán đơn hàng (mặc định là tiền mặt)
        $conn->query("UPDATE orders SET status = 'paid', payment_method = 'cash' WHERE id = $order_id");
        
        // Cập nhật trạng thái bàn
        $conn->query("UPDATE tables SET status = 'empty' WHERE id = {$check['table_id']}");
        
        $success_message = "✅ Thanh toán thành công! Cảm ơn bạn đã sử dụng dịch vụ.";
    }
}

// Lấy danh sách đặt bàn
$reservations = $conn->query("
    SELECT r.*, t.table_number 
    FROM reservations r
    LEFT JOIN tables t ON r.table_id = t.id
    WHERE r.user_id = $user_id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
    LIMIT 20
");

// Lấy danh sách đơn hàng
$orders = $conn->query("
    SELECT o.*, t.table_number
    FROM orders o
    LEFT JOIN tables t ON o.table_id = t.id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
    LIMIT 20
");

// Thống kê
$total_reservations = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE user_id = $user_id")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id")->fetch_assoc()['count'];
$total_spent = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE user_id = $user_id AND status = 'paid'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản Của Tôi - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ QL Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="menu.php">Thực đơn</a></li>
                <li><a href="reservation.php">Đặt bàn</a></li>
                <li><a href="customer_dashboard.php" class="active">👤 <?php echo htmlspecialchars($user['full_name']); ?></a></li>
                <li><a href="logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>👤 Tài Khoản Của Tôi</h1>
            <p>Xin chào, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        </div>
    </div>

    <div class="container">
        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <!-- Thống kê -->
        <div class="stats-grid" style="margin-bottom: 2rem;">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-info">
                    <h3><?php echo $total_reservations; ?></h3>
                    <p>Lượt đặt bàn</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🍽️</div>
                <div class="stat-info">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Đơn hàng</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3><?php echo formatCurrency($total_spent); ?></h3>
                    <p>Tổng chi tiêu</p>
                </div>
            </div>
        </div>

        <!-- Thông tin cá nhân -->
        <div class="admin-section">
            <h2>📋 Thông Tin Cá Nhân</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Họ tên:</strong>
                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Số điện thoại:</strong>
                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
            </div>
        </div>

        <!-- Lịch sử đặt bàn -->
        <div class="admin-section">
            <h2>📅 Lịch Sử Đặt Bàn</h2>
            <?php if ($reservations->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Giờ</th>
                            <th>Số khách</th>
                            <th>Bàn</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($res = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo formatDate($res['reservation_date']); ?></td>
                            <td><strong><?php echo date('H:i', strtotime($res['reservation_time'])); ?></strong></td>
                            <td><?php echo $res['number_of_guests']; ?> người</td>
                            <td><?php echo $res['table_number'] ?? 'Chưa phân bàn'; ?></td>
                            <td>
                                <span class="badge badge-<?php echo $res['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => 'Chờ xác nhận',
                                        'confirmed' => 'Đã xác nhận',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    echo $status_text[$res['status']];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                // Kiểm tra đã đặt món chưa
                                $has_order = $conn->query("
                                    SELECT id FROM orders 
                                    WHERE user_id = $user_id 
                                    AND table_id = {$res['table_id']}
                                    AND DATE(order_date) = '{$res['reservation_date']}'
                                ")->num_rows > 0;
                                
                                // Chỉ cho đặt món nếu status là pending hoặc confirmed
                                if (in_array($res['status'], ['pending', 'confirmed'])): 
                                ?>
                                    <a href="pre_order.php?reservation_id=<?php echo $res['id']; ?>" 
                                       class="btn btn-sm <?php echo $has_order ? 'btn-secondary' : 'btn-primary'; ?>">
                                        <?php echo $has_order ? '✏️ Sửa món' : '🍽️ Đặt món trước'; ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: #666;">
                Bạn chưa có lượt đặt bàn nào.<br>
                <a href="reservation.php" class="btn btn-primary" style="margin-top: 1rem;">Đặt bàn ngay</a>
            </p>
            <?php endif; ?>
        </div>

        <!-- Lịch sử đơn hàng -->
        <div class="admin-section">
            <h2>🍽️ Lịch Sử Đơn Hàng</h2>
            <?php if ($orders->num_rows > 0): ?>
            <?php 
            $orders->data_seek(0); // Reset pointer
            while ($order = $orders->fetch_assoc()): 
                // Lấy chi tiết món ăn
                $order_items = $conn->query("
                    SELECT oi.*, d.name as dish_name
                    FROM order_items oi
                    JOIN dishes d ON oi.dish_id = d.id
                    WHERE oi.order_id = {$order['id']}
                ");
            ?>
            <div class="order-history-card" style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0;">
                            Đơn hàng #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?>
                        </h3>
                        <p style="margin: 0; color: #666;">
                            📅 <?php echo formatDateTime($order['order_date']); ?> | 
                            🪑 Bàn <?php echo $order['table_number'] ?? '-'; ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.3rem; font-weight: bold; color: #667eea; margin-bottom: 0.5rem;">
                            <?php echo formatCurrency($order['total_amount']); ?>
                        </div>
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
                    </div>
                </div>

                <?php 
                // Lấy tất cả món
                $order_items->data_seek(0);
                $items_array = [];
                
                while ($item = $order_items->fetch_assoc()) {
                    $items_array[] = $item;
                }
                ?>

                <details style="margin-top: 1rem;" <?php echo ($order['status'] != 'paid' ? 'open' : ''); ?>>
                    <summary style="cursor: pointer; font-weight: 600; color: #667eea; padding: 0.5rem; background: #f8f9fa; border-radius: 5px;">
                        📋 Chi tiết món ăn (<?php echo count($items_array); ?> loại)
                    </summary>
                    <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                        <?php foreach ($items_array as $item): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.8rem; margin-bottom: 0.5rem; background: white; border-radius: 5px; border-left: 4px solid 
                            <?php 
                            $status_colors = [
                                'pending' => '#6b7280',
                                'preparing' => '#f59e0b', 
                                'ready' => '#10b981',
                                'served' => '#3b82f6'
                            ];
                            echo $status_colors[$item['status']];
                            ?>;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <strong><?php echo htmlspecialchars($item['dish_name']); ?></strong>
                                    <span style="background: #e5e7eb; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                        x<?php echo $item['quantity']; ?>
                                    </span>
                                    <span class="badge" style="font-size: 11px; padding: 3px 10px;
                                        <?php 
                                        $status_styles = [
                                            'pending' => 'background: #e5e7eb; color: #374151;',
                                            'preparing' => 'background: #fef3c7; color: #92400e;',
                                            'ready' => 'background: #d1fae5; color: #065f46;',
                                            'served' => 'background: #dbeafe; color: #1e40af;'
                                        ];
                                        echo $status_styles[$item['status']];
                                        ?>">
                                        <?php 
                                        $status_text = [
                                            'pending' => '⏳ Chờ làm',
                                            'preparing' => '🔥 Đang làm',
                                            'ready' => '✅ Đã xong',
                                            'served' => '🍽️ Đã lên bàn'
                                        ];
                                        echo $status_text[$item['status']];
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right; font-weight: bold;">
                                <?php echo formatCurrency($item['price'] * $item['quantity']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </details>

                <?php if ($order['status'] == 'paid'): ?>
                <div style="margin-top: 1rem; padding: 1rem; background: #d1fae5; border-radius: 5px;">
                    <strong>💰 Đã thanh toán:</strong>
                    <?php 
                    $methods = [
                        'cash' => '💵 Tiền mặt',
                        'card' => '💳 Thẻ',
                        'transfer' => '📱 Chuyển khoản'
                    ];
                    echo $methods[$order['payment_method']] ?? '-';
                    ?>
                </div>
                <?php elseif ($order['status'] == 'completed'): ?>
                <div style="margin-top: 1rem;">
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Xác nhận thanh toán đơn hàng này?')">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="pay_order" class="btn btn-success" style="width: 100%;">
                            💰 Thanh Toán Ngay
                        </button>
                    </form>
                    <p style="text-align: center; font-size: 12px; color: #666; margin-top: 5px;">
                        Click để xác nhận đã thanh toán
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: #666;">
                Bạn chưa có đơn hàng nào.<br>
                <a href="reservation.php" class="btn btn-primary" style="margin-top: 1rem;">Đặt bàn và đặt món</a>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Tự động refresh mỗi 15 giây để cập nhật trạng thái món
        let autoRefreshInterval;
        
        function startAutoRefresh() {
            // Chỉ refresh nếu có đơn hàng chưa thanh toán
            const hasUnpaidOrders = document.querySelectorAll('.badge-pending, .badge-preparing, .badge-completed').length > 0;
            
            if (hasUnpaidOrders) {
                autoRefreshInterval = setInterval(function() {
                    // Refresh trang nhẹ nhàng
                    location.reload();
                }, 15000); // 15 giây
                
                // Hiển thị thông báo
                showRefreshNotice();
            }
        }
        
        function showRefreshNotice() {
            const notice = document.createElement('div');
            notice.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: #667eea; color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 1000;';
            notice.innerHTML = '🔄 Tự động cập nhật mỗi 15s';
            document.body.appendChild(notice);
            
            // Ẩn sau 3 giây
            setTimeout(() => {
                notice.style.opacity = '0';
                notice.style.transition = 'opacity 0.5s';
                setTimeout(() => notice.remove(), 500);
            }, 3000);
        }
        
        // Khởi động khi trang load
        window.addEventListener('DOMContentLoaded', startAutoRefresh);
    </script>
</body>
</html>

