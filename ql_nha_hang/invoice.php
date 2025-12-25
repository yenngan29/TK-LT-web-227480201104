<?php
require_once 'config.php';
$conn = getDBConnection();

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$all_ids_param = isset($_GET['all_ids']) ? $_GET['all_ids'] : '';

if ($order_id == 0) {
    redirect('index.php');
}

// Lấy thông tin đơn hàng chính
$order_result = $conn->query("
    SELECT o.*, t.table_number 
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    WHERE o.id = $order_id
");

if ($order_result->num_rows == 0) {
    redirect('index.php');
}

$order = $order_result->fetch_assoc();

// Nếu có all_ids, lấy tất cả món từ các đơn hàng đã gộp
$all_order_ids = [];
if (!empty($all_ids_param)) {
    $all_order_ids = array_map('intval', explode(',', $all_ids_param));
} else {
    $all_order_ids = [$order_id];
}

// Tính tổng tiền và giảm giá từ tất cả đơn hàng
$total_amount = 0;
$total_discount = 0;
$total_final = 0;
foreach ($all_order_ids as $oid) {
    $o = $conn->query("SELECT total_amount, discount_amount, final_amount FROM orders WHERE id = $oid")->fetch_assoc();
    if ($o) {
        $total_amount += floatval($o['total_amount']);
        $total_discount += floatval($o['discount_amount'] ?? 0);
        $total_final += floatval($o['final_amount'] ?? $o['total_amount']);
    }
}

// Cập nhật thông tin cho order chính để hiển thị
$order['total_amount'] = $total_amount;
$order['discount_amount'] = $total_discount;
$order['final_amount'] = $total_final;

// Lấy thông tin mã giảm giá đã sử dụng (nếu có)
$promotion_info = null;
if ($total_discount > 0) {
    $promotion_usage = $conn->query("
        SELECT pu.*, p.code, p.name, p.discount_type, p.discount_value
        FROM promotion_usage pu
        JOIN promotions p ON pu.promotion_id = p.id
        WHERE pu.order_id = $order_id
        LIMIT 1
    ");
    if ($promotion_usage->num_rows > 0) {
        $promotion_info = $promotion_usage->fetch_assoc();
    }
}

// Lấy chi tiết TẤT CẢ các món từ tất cả đơn hàng
$order_ids_str = implode(',', $all_order_ids);
$items = $conn->query("
    SELECT oi.*, d.name as dish_name, o.order_date as order_date, oi.order_id
    FROM order_items oi
    JOIN dishes d ON oi.dish_id = d.id
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.order_id IN ($order_ids_str)
    ORDER BY o.order_date ASC, oi.id ASC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="invoice-page">
        <div class="invoice-container">
            <div class="invoice-header">
                <h1>🍽️ QL NHÀ HÀNG</h1>
                <p>123 Đường ABC, Quận 1, TP.HCM</p>
                <p>Điện thoại: (028) 1234 5678</p>
                <hr>
                <h2>HÓA ĐƠN THANH TOÁN</h2>
            </div>

            <div class="invoice-info">
                <div class="invoice-row">
                    <span>Mã hóa đơn:</span>
                    <strong>#HD<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                </div>
                <div class="invoice-row">
                    <span>Bàn số:</span>
                    <strong><?php echo $order['table_number']; ?></strong>
                </div>
                <div class="invoice-row">
                    <span>Ngày giờ:</span>
                    <strong><?php echo formatDateTime($order['order_date']); ?></strong>
                </div>
                <div class="invoice-row">
                    <span>Phương thức thanh toán:</span>
                    <strong>
                        <?php 
                        $methods = [
                            'cash' => 'Tiền mặt',
                            'card' => 'Thẻ',
                            'transfer' => 'Chuyển khoản'
                        ];
                        echo $methods[$order['payment_method']];
                        ?>
                    </strong>
                </div>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Món ăn</th>
                        <th>SL</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stt = 1;
                    $current_order_id = 0;
                    $order_counter = 0;
                    $has_multiple_orders = count($all_order_ids) > 1;
                    
                    while ($item = $items->fetch_assoc()): 
                        $item_date = date('d/m/Y H:i', strtotime($item['order_date']));
                        
                        // Hiển thị phân loại nếu có nhiều đơn hàng và chuyển sang đơn hàng mới
                        if ($has_multiple_orders && $current_order_id != $item['order_id']):
                            $current_order_id = $item['order_id'];
                            $order_counter++;
                            $order_label = ($order_counter == 1) ? 'Đơn đặt online' : 'Đơn gọi thêm tại bàn';
                    ?>
                    <tr style="background: #f8f9fa;">
                        <td colspan="5" style="font-weight: 600; color: #667eea; padding: 12px; border-top: 2px solid #667eea;">
                            📋 <?php echo $order_label; ?> - <?php echo $item_date; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><?php echo htmlspecialchars($item['dish_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatCurrency($item['price']); ?></td>
                        <td><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <?php if ($has_multiple_orders): ?>
                    <tr>
                        <td colspan="5" style="text-align: right; padding: 10px; color: #666; font-size: 0.9rem;">
                            <em>💡 Hóa đơn đã gộp <?php echo count($all_order_ids); ?> đơn hàng (đặt online + gọi thêm tại bàn)</em>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="4"><strong>TỔNG TIỀN:</strong></td>
                        <td><strong><?php echo formatCurrency($order['total_amount']); ?></strong></td>
                    </tr>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <tr style="color: #10b981;">
                        <td colspan="4">
                            <strong>GIẢM GIÁ:</strong>
                            <?php if ($promotion_info): ?>
                                <div style="font-size: 0.85em; color: #666; margin-top: 5px;">
                                    <?php echo htmlspecialchars($promotion_info['name']); ?>
                                    <?php if ($promotion_info['code']): ?>
                                        <span style="background: #10b981; color: white; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">
                                            <?php echo htmlspecialchars($promotion_info['code']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong>-<?php echo formatCurrency($order['discount_amount']); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    <tr style="background: #f0f9ff; font-size: 1.1em; border-top: 2px solid #667eea;">
                        <td colspan="4"><strong>THÀNH TIỀN:</strong></td>
                        <td><strong style="color: #667eea; font-size: 1.3em;"><?php echo formatCurrency($order['final_amount']); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="invoice-footer">
                <p>Cảm ơn quý khách đã sử dụng dịch vụ!</p>
                <p>Hẹn gặp lại quý khách!</p>
            </div>

            <div class="invoice-actions">
                <button onclick="window.print()" class="btn btn-primary">In hóa đơn</button>
                <a href="index.php" class="btn btn-secondary">Về trang chủ</a>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .invoice-actions, .navbar, .footer {
                display: none;
            }
            .invoice-container {
                box-shadow: none;
            }
        }
    </style>
</body>
</html>





