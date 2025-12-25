<?php
require_once 'config.php';
require_once 'promotions.php';
session_start();
$conn = getDBConnection();

// Lấy danh sách bàn đang có khách
$tables = $conn->query("SELECT * FROM tables WHERE status = 'occupied' ORDER BY table_number");

$selected_table = isset($_GET['table']) ? intval($_GET['table']) : 0;
$order_info = null;
$order_items = [];

if ($selected_table > 0) {
    // Lấy TẤT CẢ các đơn hàng chưa thanh toán của bàn (gộp pre-order và order tại bàn)
    $orders_result = $conn->query("
        SELECT o.*, t.table_number 
        FROM orders o
        JOIN tables t ON o.table_id = t.id
        WHERE o.table_id = $selected_table AND o.status != 'paid'
        ORDER BY o.order_date ASC
    ");
    
    $all_order_ids = [];
    $total_amount = 0;
    $order_info = null;
    
    if ($orders_result->num_rows > 0) {
        // Lấy thông tin bàn từ đơn hàng đầu tiên
        $first_order = $orders_result->fetch_assoc();
        $order_info = [
            'id' => $first_order['id'], // Dùng ID đơn hàng đầu tiên làm ID chính
            'table_id' => $first_order['table_id'],
            'table_number' => $first_order['table_number'],
            'order_date' => $first_order['order_date'],
            'total_amount' => 0 // Sẽ tính lại tổng
        ];
        
        // Reset lại con trỏ để duyệt lại
        $orders_result->data_seek(0);
        
        // Lấy tất cả các order_id và tính tổng
        while ($order = $orders_result->fetch_assoc()) {
            $all_order_ids[] = $order['id'];
            $total_amount += floatval($order['total_amount']);
        }
        
        $order_info['total_amount'] = $total_amount;
        $order_info['all_order_ids'] = $all_order_ids;
        
        // Tính giảm giá tự động (nếu có user_id)
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $applied_promotion = null;
        $discount_amount = 0;
        $final_amount = $total_amount;
        
        // Kiểm tra mã giảm giá từ session hoặc POST
        $promotion_code = isset($_POST['promotion_code']) ? trim($_POST['promotion_code']) : (isset($_SESSION['promotion_code']) ? $_SESSION['promotion_code'] : '');
        
        if (!empty($promotion_code)) {
            // Áp dụng mã giảm giá thủ công
            $result = applyPromotionCode($conn, $promotion_code, $user_id, $total_amount);
            if ($result['success']) {
                $applied_promotion = $result['promotion'];
                $discount_amount = calculateDiscount($applied_promotion, $total_amount);
                $final_amount = $total_amount - $discount_amount;
                $_SESSION['promotion_code'] = $promotion_code;
                $_SESSION['promotion_id'] = $applied_promotion['id'];
            }
        } else {
            // Tự động áp dụng giảm giá cho khách hàng thân thiết
            if ($user_id) {
                $auto_promotion = getAutoDiscount($conn, $user_id, $total_amount);
                if ($auto_promotion) {
                    $applied_promotion = $auto_promotion;
                    $discount_amount = calculateDiscount($auto_promotion, $total_amount);
                    $final_amount = $total_amount - $discount_amount;
                }
            }
        }
        
        $order_info['discount_amount'] = $discount_amount;
        $order_info['final_amount'] = $final_amount;
        $order_info['applied_promotion'] = $applied_promotion;
        
        // Lấy chi tiết TẤT CẢ các món từ tất cả đơn hàng
        if (!empty($all_order_ids)) {
            $order_ids_str = implode(',', $all_order_ids);
            $items_result = $conn->query("
                SELECT oi.*, d.name as dish_name, o.order_date as order_date, oi.order_id
                FROM order_items oi
                JOIN dishes d ON oi.dish_id = d.id
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.order_id IN ($order_ids_str)
                ORDER BY o.order_date ASC, oi.id ASC
            ");
            
            while ($item = $items_result->fetch_assoc()) {
                $order_items[] = $item;
            }
        }
    }
}

// Xử lý áp dụng mã giảm giá
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_promotion'])) {
    $promotion_code = trim($_POST['promotion_code']);
    if (!empty($promotion_code) && $selected_table > 0) {
        $_SESSION['promotion_code'] = $promotion_code;
        redirect("billing.php?table=$selected_table");
    }
}

// Xử lý xóa mã giảm giá
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_promotion'])) {
    unset($_SESSION['promotion_code']);
    unset($_SESSION['promotion_id']);
    if ($selected_table > 0) {
        redirect("billing.php?table=$selected_table");
    }
}

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay'])) {
    $table_id = intval($_POST['table_id']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $all_order_ids = json_decode($_POST['all_order_ids'], true);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $promotion_id = isset($_POST['promotion_id']) ? intval($_POST['promotion_id']) : null;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    if (!empty($all_order_ids) && is_array($all_order_ids)) {
        // Cập nhật TẤT CẢ các đơn hàng thành 'paid' và áp dụng giảm giá
        $order_ids_str = implode(',', array_map('intval', $all_order_ids));
        
        // Tính lại tổng tiền và giảm giá cho từng đơn hàng (tỷ lệ)
        $total_before_discount = 0;
        foreach ($all_order_ids as $oid) {
            $o = $conn->query("SELECT total_amount FROM orders WHERE id = $oid")->fetch_assoc();
            if ($o) {
                $total_before_discount += floatval($o['total_amount']);
            }
        }
        
        // Phân bổ giảm giá theo tỷ lệ cho từng đơn hàng
        foreach ($all_order_ids as $oid) {
            $o = $conn->query("SELECT total_amount FROM orders WHERE id = $oid")->fetch_assoc();
            if ($o) {
                $order_discount = $total_before_discount > 0 ? 
                    ($discount_amount * floatval($o['total_amount']) / $total_before_discount) : 0;
                $order_final = floatval($o['total_amount']) - $order_discount;
                
                $conn->query("
                    UPDATE orders 
                    SET status = 'paid', 
                        payment_method = '$payment_method',
                        discount_amount = $order_discount,
                        final_amount = $order_final
                    WHERE id = $oid
                ");
                
                // Lưu lịch sử sử dụng mã giảm giá (chỉ cho đơn hàng đầu tiên)
                if ($promotion_id && $oid == $all_order_ids[0]) {
                    recordPromotionUsage($conn, $promotion_id, $user_id, $oid, $order_discount);
                }
            }
        }
        
        // Xóa mã giảm giá khỏi session
        unset($_SESSION['promotion_code']);
        unset($_SESSION['promotion_id']);
        
        // Cập nhật trạng thái bàn
        $conn->query("UPDATE tables SET status = 'empty' WHERE id = $table_id");
        
        // Chuyển đến hóa đơn với order_id đầu tiên
        $main_order_id = $all_order_ids[0];
        redirect("invoice.php?order_id=$main_order_id&all_ids=" . implode(',', $all_order_ids));
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - QL Nhà Hàng</title>
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
                <li><a href="kitchen.php">Bếp</a></li>
                <li><a href="billing.php" class="active">Thanh toán</a></li>
                <li><a href="admin/">Quản lý</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Thanh Toán</h1>
        </div>
    </div>

    <div class="container">
        <div class="billing-container">
            <?php if ($selected_table == 0): ?>
            <div class="table-selection">
                <h2>Chọn Bàn Cần Thanh Toán</h2>
                <div class="tables-grid">
                    <?php while ($table = $tables->fetch_assoc()): ?>
                    <a href="?table=<?php echo $table['id']; ?>" class="table-card occupied">
                        <div class="table-number">Bàn <?php echo $table['table_number']; ?></div>
                        <div class="table-status">Có khách</div>
                    </a>
                    <?php endwhile; ?>
                    
                    <?php if ($tables->num_rows == 0): ?>
                    <p class="empty-message">Không có bàn nào cần thanh toán</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php elseif ($order_info): ?>
            <div class="billing-content">
                <div class="billing-header">
                    <h2>Bàn <?php echo $order_info['table_number']; ?></h2>
                    <span class="order-time"><?php echo formatDateTime($order_info['order_date']); ?></span>
                </div>

                <div class="invoice-items">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>Món ăn</th>
                                <th>SL</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $current_order_id = 0;
                            $order_counter = 0;
                            $has_multiple = count($order_info['all_order_ids']) > 1;
                            
                            foreach ($order_items as $item): 
                                $item_date = date('d/m/Y H:i', strtotime($item['order_date']));
                                
                                // Hiển thị phân loại nếu có nhiều đơn hàng và chuyển sang đơn hàng mới
                                if ($has_multiple && $current_order_id != $item['order_id']):
                                    $current_order_id = $item['order_id'];
                                    $order_counter++;
                                    $order_label = ($order_counter == 1) ? 'Đơn đặt online' : 'Đơn gọi thêm tại bàn';
                            ?>
                            <tr style="background: #f0f0f0;">
                                <td colspan="4" style="font-weight: 600; color: #667eea; padding: 10px;">
                                    📋 <?php echo $order_label; ?> - <?php echo $item_date; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['dish_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo formatCurrency($item['price']); ?></td>
                                <td><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <?php if (count($order_info['all_order_ids']) > 1): ?>
                            <tr>
                                <td colspan="4" style="text-align: right; padding: 10px; color: #666;">
                                    <small>💡 Đã gộp <?php echo count($order_info['all_order_ids']); ?> đơn hàng (đặt online + gọi thêm tại bàn)</small>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3"><strong>Tổng tiền:</strong></td>
                                <td><strong><?php echo formatCurrency($order_info['total_amount']); ?></strong></td>
                            </tr>
                            <?php if ($order_info['discount_amount'] > 0): ?>
                            <tr style="color: #10b981;">
                                <td colspan="3">
                                    <strong>Giảm giá:</strong>
                                    <?php if ($order_info['applied_promotion']): ?>
                                        <small style="display: block; color: #666;">
                                            <?php echo htmlspecialchars($order_info['applied_promotion']['name']); ?>
                                            <?php if ($order_info['applied_promotion']['code']): ?>
                                                (<?php echo htmlspecialchars($order_info['applied_promotion']['code']); ?>)
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><strong>-<?php echo formatCurrency($order_info['discount_amount']); ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <tr style="background: #f0f9ff; font-size: 1.1em;">
                                <td colspan="3"><strong>Thành tiền:</strong></td>
                                <td><strong style="color: #667eea; font-size: 1.2em;"><?php echo formatCurrency($order_info['final_amount']); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Form áp dụng mã giảm giá -->
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: #667eea;">🎁 Mã Giảm Giá</h3>
                    <?php if ($order_info['discount_amount'] > 0): ?>
                        <div style="background: #d1fae5; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #065f46;">✓ Đã áp dụng giảm giá:</strong>
                                    <div style="color: #666; margin-top: 0.5rem;">
                                        <?php echo htmlspecialchars($order_info['applied_promotion']['name']); ?>
                                        <?php if ($order_info['applied_promotion']['code']): ?>
                                            <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.9em; margin-left: 8px;">
                                                <?php echo htmlspecialchars($order_info['applied_promotion']['code']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="remove_promotion" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" style="display: flex; gap: 0.5rem;">
                            <input type="text" name="promotion_code" placeholder="Nhập mã giảm giá (VD: VIP10, LOYALTY5)" 
                                   class="form-control" style="flex: 1;">
                            <button type="submit" name="apply_promotion" class="btn btn-primary">Áp dụng</button>
                        </form>
                        <small style="color: #666; margin-top: 0.5rem; display: block;">
                            💡 Mã giảm giá sẽ tự động áp dụng cho khách hàng thân thiết
                        </small>
                    <?php endif; ?>
                </div>

                <form method="POST" class="payment-form">
                    <input type="hidden" name="table_id" value="<?php echo $order_info['table_id']; ?>">
                    <input type="hidden" name="all_order_ids" value="<?php echo htmlspecialchars(json_encode($order_info['all_order_ids'])); ?>">
                    <input type="hidden" name="discount_amount" value="<?php echo $order_info['discount_amount']; ?>">
                    <input type="hidden" name="promotion_id" value="<?php echo $order_info['applied_promotion'] ? $order_info['applied_promotion']['id'] : ''; ?>">
                    
                    <div class="form-group">
                        <label>Phương thức thanh toán:</label>
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <span>💵 Tiền mặt</span>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="card">
                                <span>💳 Thẻ</span>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="transfer">
                                <span>📱 Chuyển khoản</span>
                            </label>
                        </div>
                    </div>

                    <div class="payment-actions">
                        <a href="billing.php" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" name="pay" class="btn btn-primary btn-lg">Thanh Toán</button>
                    </div>
                </form>
            </div>
            
            <?php else: ?>
            <div class="alert alert-info">
                <p>Không tìm thấy đơn hàng cho bàn này</p>
                <a href="billing.php" class="btn btn-primary">Quay lại</a>
            </div>
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
</body>
</html>





