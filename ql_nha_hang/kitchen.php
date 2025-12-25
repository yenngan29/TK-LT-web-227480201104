<?php
require_once 'config.php';
$conn = getDBConnection();

// Lấy các đơn hàng đang chờ và đang chuẩn bị
$orders = $conn->query("
    SELECT o.*, t.table_number 
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    WHERE o.status IN ('pending', 'preparing')
    ORDER BY o.order_date ASC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Màn Hình Bếp - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta http-equiv="refresh" content="30">
</head>
<body class="kitchen-page">
    <div class="kitchen-header">
        <h1>🍳 Màn Hình Bếp</h1>
        <div class="kitchen-time" id="current-time"></div>
    </div>

    <div class="kitchen-container">
        <?php if ($orders->num_rows == 0): ?>
        <div class="no-orders">
            <h2>Không có đơn hàng mới</h2>
            <p>Đang chờ đơn hàng từ khách...</p>
        </div>
        <?php else: ?>
        
        <div class="orders-grid">
            <?php while ($order = $orders->fetch_assoc()): ?>
            <?php
            // Lấy chi tiết đơn hàng
            $order_items = $conn->query("
                SELECT oi.*, d.name as dish_name
                FROM order_items oi
                JOIN dishes d ON oi.dish_id = d.id
                WHERE oi.order_id = {$order['id']}
                ORDER BY oi.id
            ");
            ?>
            
            <div class="kitchen-order-card <?php echo $order['status']; ?>">
                <div class="order-card-header">
                    <div class="order-table">Bàn <?php echo $order['table_number']; ?></div>
                    <div class="order-time"><?php echo date('H:i', strtotime($order['order_date'])); ?></div>
                </div>
                
                <div class="order-items-list">
                    <?php while ($item = $order_items->fetch_assoc()): ?>
                    <div class="kitchen-item <?php echo $item['status']; ?>" id="item-<?php echo $item['id']; ?>">
                        <div class="item-quantity">x<?php echo $item['quantity']; ?></div>
                        <div class="item-name"><?php echo htmlspecialchars($item['dish_name']); ?></div>
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <?php if ($item['status'] == 'pending'): ?>
                                <button onclick="updateItemStatus(<?php echo $item['id']; ?>, 'preparing')" 
                                        class="btn btn-sm" style="background: #f59e0b; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">
                                    🔥 Bắt đầu
                                </button>
                            <?php elseif ($item['status'] == 'preparing'): ?>
                                <button onclick="updateItemStatus(<?php echo $item['id']; ?>, 'ready')" 
                                        class="btn btn-sm" style="background: #10b981; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">
                                    ✅ Xong
                                </button>
                            <?php elseif ($item['status'] == 'ready'): ?>
                                <button onclick="updateItemStatus(<?php echo $item['id']; ?>, 'served')" 
                                        class="btn btn-sm" style="background: #3b82f6; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">
                                    🍽️ Đã lên
                                </button>
                            <?php else: ?>
                                <span class="item-status-badge" style="background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 5px;">
                                    ✓ Đã phục vụ
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div class="order-actions">
                    <?php if ($order['status'] == 'pending'): ?>
                    <button class="btn btn-primary" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')">
                        Bắt Đầu Làm
                    </button>
                    <?php elseif ($order['status'] == 'preparing'): ?>
                    <button class="btn btn-success" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                        Hoàn Thành
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('current-time').textContent = timeStr;
        }

        setInterval(updateTime, 1000);
        updateTime();

        function updateOrderStatus(orderId, status) {
            fetch('api/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
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
        
        function updateItemStatus(itemId, status) {
            fetch('api/update_item_status.php', {
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
    </script>
</body>
</html>
