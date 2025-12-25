<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id == 0) {
    redirect('orders.php');
}

// Lấy thông tin đơn hàng
$order = $conn->query("
    SELECT o.*, t.table_number 
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    WHERE o.id = $order_id
")->fetch_assoc();

if (!$order) {
    redirect('orders.php');
}

// Lấy chi tiết đơn hàng
$items = $conn->query("
    SELECT oi.*, d.name as dish_name
    FROM order_items oi
    JOIN dishes d ON oi.dish_id = d.id
    WHERE oi.order_id = $order_id
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng - QL Nhà Hàng</title>
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
                <li><a href="../logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Chi Tiết Đơn Hàng #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></h1>
        </div>
    </div>

    <div class="container">
        <div class="order-detail-container">
            <div class="order-info-card">
                <h3>Thông Tin Đơn Hàng</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Bàn:</strong>
                        <span><?php echo $order['table_number']; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Thời gian:</strong>
                        <span><?php echo formatDateTime($order['order_date']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Trạng thái:</strong>
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
                    <div class="info-item">
                        <strong>Tổng tiền:</strong>
                        <span class="price-large"><?php echo formatCurrency($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>

            <div class="order-items-card">
                <h3>Chi Tiết Món Ăn</h3>
                
                <?php
                // Lấy tất cả món
                $items->data_seek(0);
                $items_array = [];
                
                while ($item = $items->fetch_assoc()) {
                    $items_array[] = $item;
                }
                ?>
                
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <?php foreach ($items_array as $item): ?>
                    <div style="display: flex; align-items: center; padding: 1rem; background: white; border: 2px solid 
                        <?php 
                        $border_colors = [
                            'pending' => '#e5e7eb',
                            'preparing' => '#fde047',
                            'ready' => '#86efac',
                            'served' => '#93c5fd'
                        ];
                        echo $border_colors[$item['status']];
                        ?>; border-radius: 8px;">
                        <div style="flex: 1;">
                            <div style="font-weight: bold; font-size: 1.1rem; margin-bottom: 4px;">
                                <?php echo htmlspecialchars($item['dish_name']); ?>
                                <span style="background: #e5e7eb; padding: 2px 10px; border-radius: 12px; font-size: 0.9rem; margin-left: 8px;">
                                    x<?php echo $item['quantity']; ?>
                                </span>
                            </div>
                            <div style="color: #666; font-size: 0.95rem;">
                                <?php echo formatCurrency($item['price']); ?> × <?php echo $item['quantity']; ?> = 
                                <strong><?php echo formatCurrency($item['price'] * $item['quantity']); ?></strong>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <?php if ($order['status'] != 'paid'): ?>
                                <?php if ($item['status'] == 'pending'): ?>
                                    <button onclick="updateItemStatus(<?php echo $item['id']; ?>, 'preparing')" 
                                            style="padding: 8px 16px; background: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                        🔥 Bắt đầu làm
                                    </button>
                                <?php elseif ($item['status'] == 'preparing'): ?>
                                    <button onclick="updateItemStatus(<?php echo $item['id']; ?>, 'ready')" 
                                            style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                        ✅ Đã xong
                                    </button>
                                <?php elseif ($item['status'] == 'ready'): ?>
                                    <button onclick="updateItemStatus(<?php echo $item['id']; ?>, 'served')" 
                                            style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                        🍽️ Đã lên bàn
                                    </button>
                                <?php else: ?>
                                    <span style="padding: 6px 16px; background: #dbeafe; color: #1e40af; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">
                                        ✓ Đã phục vụ
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem;
                                    <?php 
                                    $status_styles = [
                                        'pending' => 'background: #f3f4f6; color: #374151;',
                                        'preparing' => 'background: #fef3c7; color: #92400e;',
                                        'ready' => 'background: #d1fae5; color: #065f46;',
                                        'served' => 'background: #dbeafe; color: #1e40af;'
                                    ];
                                    echo $status_styles[$item['status']];
                                    ?>">
                                    <?php 
                                    $icons = [
                                        'pending' => '⏳',
                                        'preparing' => '🔥',
                                        'ready' => '✅',
                                        'served' => '🍽️'
                                    ];
                                    $labels = [
                                        'pending' => 'Chờ làm',
                                        'preparing' => 'Đang làm',
                                        'ready' => 'Đã xong',
                                        'served' => 'Đã lên bàn'
                                    ];
                                    echo $icons[$item['status']] . ' ' . $labels[$item['status']];
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="order-actions">
                <a href="orders.php" class="btn btn-secondary">Quay lại</a>
                <?php if ($order['status'] == 'completed'): ?>
                <a href="../billing.php?table=<?php echo $order['table_id']; ?>" class="btn btn-primary">Thanh toán</a>
                <?php endif; ?>
                <?php if ($order['status'] == 'paid'): ?>
                <a href="../invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-success" target="_blank">Xem hóa đơn</a>
                <?php endif; ?>
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

    <script>
        function updateItemStatus(itemId, status) {
            if (!confirm('Xác nhận cập nhật trạng thái món?')) {
                return;
            }
            
            fetch('../api/update_item_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra');
                }
            });
        }
        
        // Tự động refresh mỗi 10 giây nếu đơn chưa thanh toán
        <?php if ($order['status'] != 'paid'): ?>
        setTimeout(function() {
            location.reload();
        }, 10000);
        <?php endif; ?>
    </script>
</body>
</html>

