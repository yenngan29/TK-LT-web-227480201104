-- ===================================================================
-- BẢNG KHUYẾN MÃI / MÃ GIẢM GIÁ
-- ===================================================================
CREATE TABLE IF NOT EXISTS promotions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE COMMENT 'Mã giảm giá (VD: VIP10, LOYALTY5)',
    name VARCHAR(200) NOT NULL COMMENT 'Tên chương trình khuyến mãi',
    description TEXT COMMENT 'Mô tả chi tiết',
    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage' COMMENT 'Loại giảm giá: phần trăm hoặc số tiền cố định',
    discount_value DECIMAL(10, 2) NOT NULL COMMENT 'Giá trị giảm giá (phần trăm hoặc số tiền)',
    min_order_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Đơn hàng tối thiểu để áp dụng',
    max_discount_amount DECIMAL(10, 2) DEFAULT NULL COMMENT 'Số tiền giảm tối đa (cho loại percentage)',
    min_reservations INT DEFAULT 0 COMMENT 'Số lần đặt bàn tối thiểu để áp dụng',
    min_orders INT DEFAULT 0 COMMENT 'Số lần đặt món tối thiểu để áp dụng',
    start_date DATE DEFAULT NULL COMMENT 'Ngày bắt đầu',
    end_date DATE DEFAULT NULL COMMENT 'Ngày kết thúc',
    usage_limit INT DEFAULT NULL COMMENT 'Giới hạn số lần sử dụng (NULL = không giới hạn)',
    used_count INT DEFAULT 0 COMMENT 'Số lần đã sử dụng',
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active' COMMENT 'Trạng thái',
    auto_apply BOOLEAN DEFAULT FALSE COMMENT 'Tự động áp dụng cho khách hàng đủ điều kiện',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_status (status),
    INDEX idx_auto_apply (auto_apply),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng khuyến mãi và mã giảm giá';

-- ===================================================================
-- BẢNG LỊCH SỬ SỬ DỤNG MÃ GIẢM GIÁ
-- ===================================================================
CREATE TABLE IF NOT EXISTS promotion_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promotion_id INT NOT NULL COMMENT 'ID mã giảm giá',
    user_id INT DEFAULT NULL COMMENT 'ID người dùng',
    order_id INT NOT NULL COMMENT 'ID đơn hàng',
    discount_amount DECIMAL(10, 2) NOT NULL COMMENT 'Số tiền đã giảm',
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian sử dụng',
    
    FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    INDEX idx_promotion_id (promotion_id),
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id),
    INDEX idx_used_at (used_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch sử sử dụng mã giảm giá';

-- ===================================================================
-- CHÈN DỮ LIỆU MẪU - CÁC CHƯƠNG TRÌNH KHUYẾN MÃI TỰ ĐỘNG
-- ===================================================================

-- Giảm giá 5% cho khách hàng đặt bàn từ 3 lần trở lên
INSERT INTO promotions (code, name, description, discount_type, discount_value, min_reservations, auto_apply, status) 
VALUES ('LOYALTY5', 'Khách hàng thân thiết - 5%', 'Giảm giá 5% cho khách hàng đã đặt bàn từ 3 lần trở lên', 'percentage', 5, 3, TRUE, 'active');

-- Giảm giá 10% cho khách hàng đặt bàn từ 5 lần trở lên
INSERT INTO promotions (code, name, description, discount_type, discount_value, min_reservations, auto_apply, status) 
VALUES ('LOYALTY10', 'Khách hàng VIP - 10%', 'Giảm giá 10% cho khách hàng đã đặt bàn từ 5 lần trở lên', 'percentage', 10, 5, TRUE, 'active');

-- Giảm giá 15% cho khách hàng đặt bàn từ 10 lần trở lên
INSERT INTO promotions (code, name, description, discount_type, discount_value, min_reservations, auto_apply, status) 
VALUES ('LOYALTY15', 'Khách hàng VVIP - 15%', 'Giảm giá 15% cho khách hàng đã đặt bàn từ 10 lần trở lên', 'percentage', 15, 10, TRUE, 'active');

-- Giảm giá 50.000đ cho đơn hàng từ 500.000đ trở lên
INSERT INTO promotions (code, name, description, discount_type, discount_value, min_order_amount, auto_apply, status) 
VALUES ('BIGORDER50K', 'Đơn hàng lớn - 50K', 'Giảm giá 50.000đ cho đơn hàng từ 500.000đ trở lên', 'fixed', 50000, 500000, TRUE, 'active');

-- Mã giảm giá thủ công (không tự động áp dụng)
INSERT INTO promotions (code, name, description, discount_type, discount_value, min_order_amount, auto_apply, status, usage_limit) 
VALUES ('WELCOME10', 'Chào mừng - 10%', 'Giảm giá 10% cho đơn hàng đầu tiên', 'percentage', 10, 0, FALSE, 'active', 100);

-- ===================================================================
-- FUNCTION: Tính số lần đặt bàn của khách hàng
-- ===================================================================
DELIMITER $$

DROP FUNCTION IF EXISTS fn_get_user_reservation_count$$
CREATE FUNCTION fn_get_user_reservation_count(p_user_id INT)
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_count INT;
    SELECT COUNT(*) INTO v_count
    FROM reservations
    WHERE user_id = p_user_id 
    AND status IN ('confirmed', 'completed');
    RETURN IFNULL(v_count, 0);
END$$

-- ===================================================================
-- FUNCTION: Tính số lần đặt món của khách hàng
-- ===================================================================
DROP FUNCTION IF EXISTS fn_get_user_order_count$$
CREATE FUNCTION fn_get_user_order_count(p_user_id INT)
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_count INT;
    SELECT COUNT(*) INTO v_count
    FROM orders
    WHERE user_id = p_user_id 
    AND status = 'paid';
    RETURN IFNULL(v_count, 0);
END$$

DELIMITER ;

-- ===================================================================
-- HOÀN TẤT
-- ===================================================================
SELECT 'Bảng promotions và functions đã được tạo thành công!' AS message;

