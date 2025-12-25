-- ===================================================================
-- TRIGGERS VÀ STORED PROCEDURES CHO HỆ THỐNG QUẢN LÝ NHÀ HÀNG
-- Version: 2.0
-- LƯU Ý: File này cần import SAU KHI đã import database.sql
-- Import qua phpMyAdmin: Chọn database ql_nha_hang → SQL → Paste và chạy
-- ===================================================================

USE ql_nha_hang;

-- ===================================================================
-- TRIGGERS - Tự động cập nhật tổng tiền đơn hàng
-- ===================================================================

-- Xóa triggers cũ nếu tồn tại
DROP TRIGGER IF EXISTS trg_order_items_subtotal_insert;
DROP TRIGGER IF EXISTS trg_order_items_subtotal_update;
DROP TRIGGER IF EXISTS trg_update_order_total_after_insert;
DROP TRIGGER IF EXISTS trg_update_order_total_after_update;
DROP TRIGGER IF EXISTS trg_update_order_total_after_delete;
DROP TRIGGER IF EXISTS trg_generate_order_number;

-- Trigger: Tự động tính subtotal khi insert/update order_items
DELIMITER $$

CREATE TRIGGER trg_order_items_subtotal_insert
BEFORE INSERT ON order_items
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.price;
END$$

CREATE TRIGGER trg_order_items_subtotal_update
BEFORE UPDATE ON order_items
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.price;
END$$

-- Trigger: Tự động cập nhật total_amount của order khi có thay đổi order_items
CREATE TRIGGER trg_update_order_total_after_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(subtotal), 0) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    ),
    final_amount = (
        SELECT COALESCE(SUM(subtotal), 0) - COALESCE(discount_amount, 0)
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END$$

CREATE TRIGGER trg_update_order_total_after_update
AFTER UPDATE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(subtotal), 0) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    ),
    final_amount = (
        SELECT COALESCE(SUM(subtotal), 0) - COALESCE(discount_amount, 0)
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END$$

CREATE TRIGGER trg_update_order_total_after_delete
AFTER DELETE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(subtotal), 0) 
        FROM order_items 
        WHERE order_id = OLD.order_id
    ),
    final_amount = (
        SELECT COALESCE(SUM(subtotal), 0) - COALESCE(discount_amount, 0)
        FROM order_items 
        WHERE order_id = OLD.order_id
    )
    WHERE id = OLD.order_id;
END$$

-- Trigger: Tự động tạo order_number nếu chưa có
CREATE TRIGGER trg_generate_order_number
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_number IS NULL OR NEW.order_number = '' THEN
        SET NEW.order_number = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD((SELECT COALESCE(MAX(CAST(SUBSTRING(order_number, -6) AS UNSIGNED)), 0) + 1 FROM orders WHERE DATE(order_date) = CURDATE()), 6, '0'));
    END IF;
END$$

DELIMITER ;

-- ===================================================================
-- STORED PROCEDURES - Các thủ tục hữu ích
-- ===================================================================

-- Xóa procedures cũ nếu tồn tại
DROP PROCEDURE IF EXISTS sp_create_order;
DROP PROCEDURE IF EXISTS sp_add_order_item;
DROP PROCEDURE IF EXISTS sp_pay_order;

DELIMITER $$

-- Procedure: Tạo đơn hàng mới với chi tiết
CREATE PROCEDURE sp_create_order(
    IN p_user_id INT,
    IN p_table_id INT,
    IN p_payment_method VARCHAR(20),
    OUT p_order_id INT
)
BEGIN
    DECLARE v_order_number VARCHAR(20);
    
    -- Tạo order_number
    SET v_order_number = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD((SELECT COALESCE(MAX(CAST(SUBSTRING(order_number, -6) AS UNSIGNED)), 0) + 1 FROM orders WHERE DATE(order_date) = CURDATE()), 6, '0'));
    
    -- Tạo đơn hàng
    INSERT INTO orders (order_number, user_id, table_id, payment_method)
    VALUES (v_order_number, p_user_id, p_table_id, p_payment_method);
    
    SET p_order_id = LAST_INSERT_ID();
    
    -- Cập nhật trạng thái bàn
    UPDATE tables SET status = 'occupied' WHERE id = p_table_id;
END$$

-- Procedure: Thêm món vào đơn hàng
CREATE PROCEDURE sp_add_order_item(
    IN p_order_id INT,
    IN p_dish_id INT,
    IN p_quantity INT,
    IN p_notes TEXT
)
BEGIN
    DECLARE v_price DECIMAL(10, 2);
    DECLARE v_dish_name VARCHAR(200);
    
    -- Lấy giá và tên món
    SELECT price, name INTO v_price, v_dish_name
    FROM dishes
    WHERE id = p_dish_id AND status = 'available';
    
    IF v_price IS NOT NULL THEN
        INSERT INTO order_items (order_id, dish_id, dish_name, quantity, price, notes)
        VALUES (p_order_id, p_dish_id, v_dish_name, p_quantity, v_price, p_notes);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Món ăn không tồn tại hoặc không khả dụng';
    END IF;
END$$

-- Procedure: Thanh toán đơn hàng
CREATE PROCEDURE sp_pay_order(
    IN p_order_id INT,
    IN p_payment_method VARCHAR(20),
    IN p_transaction_id VARCHAR(100)
)
BEGIN
    DECLARE v_table_id INT;
    DECLARE v_final_amount DECIMAL(10, 2);
    
    -- Lấy thông tin đơn hàng
    SELECT table_id, final_amount INTO v_table_id, v_final_amount
    FROM orders
    WHERE id = p_order_id AND payment_status = 'unpaid';
    
    IF v_final_amount IS NOT NULL THEN
        -- Cập nhật trạng thái đơn hàng
        UPDATE orders 
        SET status = 'paid',
            payment_status = 'paid',
            payment_method = p_payment_method,
            paid_at = NOW()
        WHERE id = p_order_id;
        
        -- Ghi lại thanh toán
        INSERT INTO payments (order_id, amount, payment_method, transaction_id)
        VALUES (p_order_id, v_final_amount, p_payment_method, p_transaction_id);
        
        -- Cập nhật trạng thái bàn
        UPDATE tables SET status = 'empty' WHERE id = v_table_id;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Đơn hàng không tồn tại hoặc đã thanh toán';
    END IF;
END$$

DELIMITER ;

-- ===================================================================
-- HOÀN TẤT
-- ===================================================================

SELECT 'Triggers và Stored Procedures đã được tạo thành công!' AS message;


