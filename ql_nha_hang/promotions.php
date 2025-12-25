<?php
/**
 * Hàm tính toán và áp dụng giảm giá
 */

require_once 'config.php';

/**
 * Lấy mã giảm giá tự động phù hợp nhất cho khách hàng
 */
function getAutoDiscount($conn, $user_id, $order_amount) {
    if (!$user_id) {
        return null;
    }
    
    // Lấy số lần đặt bàn và đặt món của khách hàng
    $reservation_count = $conn->query("
        SELECT COUNT(*) as count 
        FROM reservations 
        WHERE user_id = $user_id 
        AND status IN ('confirmed', 'completed')
    ")->fetch_assoc()['count'] ?? 0;
    
    $order_count = $conn->query("
        SELECT COUNT(*) as count 
        FROM orders 
        WHERE user_id = $user_id 
        AND status = 'paid'
    ")->fetch_assoc()['count'] ?? 0;
    
    // Tìm mã giảm giá tự động phù hợp nhất
    $promotions = $conn->query("
        SELECT * FROM promotions 
        WHERE auto_apply = TRUE 
        AND status = 'active'
        AND (start_date IS NULL OR start_date <= CURDATE())
        AND (end_date IS NULL OR end_date >= CURDATE())
        AND (usage_limit IS NULL OR used_count < usage_limit)
        AND min_reservations <= $reservation_count
        AND min_orders <= $order_count
        AND min_order_amount <= $order_amount
        ORDER BY discount_value DESC, min_reservations DESC
        LIMIT 1
    ");
    
    if ($promotions->num_rows > 0) {
        return $promotions->fetch_assoc();
    }
    
    return null;
}

/**
 * Áp dụng mã giảm giá
 */
function applyPromotionCode($conn, $code, $user_id, $order_amount) {
    $code = $conn->real_escape_string($code);
    
    $promotion = $conn->query("
        SELECT * FROM promotions 
        WHERE code = '$code'
        AND status = 'active'
        AND (start_date IS NULL OR start_date <= CURDATE())
        AND (end_date IS NULL OR end_date >= CURDATE())
        AND (usage_limit IS NULL OR used_count < usage_limit)
        AND min_order_amount <= $order_amount
    ")->fetch_assoc();
    
    if (!$promotion) {
        return ['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'];
    }
    
    // Kiểm tra điều kiện số lần đặt bàn/đặt món
    if ($promotion['min_reservations'] > 0 || $promotion['min_orders'] > 0) {
        if ($user_id) {
            $reservation_count = $conn->query("
                SELECT COUNT(*) as count 
                FROM reservations 
                WHERE user_id = $user_id 
                AND status IN ('confirmed', 'completed')
            ")->fetch_assoc()['count'] ?? 0;
            
            $order_count = $conn->query("
                SELECT COUNT(*) as count 
                FROM orders 
                WHERE user_id = $user_id 
                AND status = 'paid'
            ")->fetch_assoc()['count'] ?? 0;
            
            if ($promotion['min_reservations'] > $reservation_count || 
                $promotion['min_orders'] > $order_count) {
                return ['success' => false, 'message' => 'Bạn chưa đủ điều kiện để sử dụng mã này'];
            }
        } else {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá này'];
        }
    }
    
    return ['success' => true, 'promotion' => $promotion];
}

/**
 * Tính số tiền giảm giá
 */
function calculateDiscount($promotion, $order_amount) {
    if (!$promotion) {
        return 0;
    }
    
    $discount = 0;
    
    if ($promotion['discount_type'] == 'percentage') {
        // Giảm giá theo phần trăm
        $discount = ($order_amount * $promotion['discount_value']) / 100;
        
        // Áp dụng giới hạn tối đa nếu có
        if ($promotion['max_discount_amount'] && $discount > $promotion['max_discount_amount']) {
            $discount = $promotion['max_discount_amount'];
        }
    } else {
        // Giảm giá số tiền cố định
        $discount = $promotion['discount_value'];
    }
    
    // Không được giảm nhiều hơn tổng tiền
    if ($discount > $order_amount) {
        $discount = $order_amount;
    }
    
    return round($discount, 2);
}

/**
 * Lưu lịch sử sử dụng mã giảm giá
 */
function recordPromotionUsage($conn, $promotion_id, $user_id, $order_id, $discount_amount) {
    $promotion_id = intval($promotion_id);
    $user_id = $user_id ? intval($user_id) : 'NULL';
    $order_id = intval($order_id);
    $discount_amount = floatval($discount_amount);
    
    // Lưu lịch sử
    $conn->query("
        INSERT INTO promotion_usage (promotion_id, user_id, order_id, discount_amount)
        VALUES ($promotion_id, $user_id, $order_id, $discount_amount)
    ");
    
    // Tăng số lần sử dụng
    $conn->query("
        UPDATE promotions 
        SET used_count = used_count + 1 
        WHERE id = $promotion_id
    ");
}

?>

