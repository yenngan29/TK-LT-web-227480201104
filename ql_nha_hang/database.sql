-- ===================================================================
-- HỆ THỐNG QUẢN LÝ NHÀ HÀNG - DATABASE HOÀN CHỈNH
-- Version: 2.0 - PHPMyAdmin Compatible
-- Created: 2025
-- Description: Database đầy đủ với indexes, constraints, views
-- LƯU Ý: Triggers và Stored Procedures được tách ra file riêng
-- ===================================================================

-- Xóa database cũ nếu tồn tại (CẨN THẬN: Chỉ dùng khi cần reset)
-- DROP DATABASE IF EXISTS ql_nha_hang;

-- Tạo database mới
CREATE DATABASE IF NOT EXISTS ql_nha_hang 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE ql_nha_hang;

-- ===================================================================
-- BẢNG NGƯỜI DÙNG (Khách hàng + Admin)
-- ===================================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email đăng nhập (unique)',
    password VARCHAR(255) NOT NULL COMMENT 'Password đã hash (bcrypt)',
    full_name VARCHAR(100) NOT NULL COMMENT 'Họ và tên đầy đủ',
    phone VARCHAR(20) DEFAULT NULL COMMENT 'Số điện thoại',
    role ENUM('customer', 'admin') DEFAULT 'customer' COMMENT 'Vai trò: customer hoặc admin',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
    avatar VARCHAR(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện',
    address TEXT DEFAULT NULL COMMENT 'Địa chỉ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật',
    last_login TIMESTAMP NULL DEFAULT NULL COMMENT 'Lần đăng nhập cuối',
    
    -- Indexes cho performance
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý người dùng';

-- ===================================================================
-- BẢNG DANH MỤC MÓN ĂN
-- ===================================================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Tên danh mục',
    description TEXT DEFAULT NULL COMMENT 'Mô tả danh mục',
    image VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh danh mục',
    display_order INT DEFAULT 0 COMMENT 'Thứ tự hiển thị',
    status ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Trạng thái',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh mục món ăn';

-- ===================================================================
-- BẢNG MÓN ĂN
-- ===================================================================
CREATE TABLE dishes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL COMMENT 'Tên món ăn',
    description TEXT DEFAULT NULL COMMENT 'Mô tả chi tiết món ăn',
    price DECIMAL(10, 2) NOT NULL COMMENT 'Giá bán (VNĐ)',
    cost_price DECIMAL(10, 2) DEFAULT NULL COMMENT 'Giá vốn (để tính lợi nhuận)',
    image VARCHAR(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh món ăn',
    category_id INT NOT NULL COMMENT 'ID danh mục',
    status ENUM('available', 'unavailable', 'out_of_stock') DEFAULT 'available' COMMENT 'Trạng thái món',
    is_featured BOOLEAN DEFAULT FALSE COMMENT 'Món nổi bật',
    preparation_time INT DEFAULT NULL COMMENT 'Thời gian chế biến (phút)',
    calories INT DEFAULT NULL COMMENT 'Calories (nếu có)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key với CASCADE
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_is_featured (is_featured),
    INDEX idx_price (price),
    INDEX idx_name (name(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng món ăn';

-- ===================================================================
-- BẢNG BÀN
-- ===================================================================
CREATE TABLE tables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_number VARCHAR(10) NOT NULL UNIQUE COMMENT 'Số bàn (VD: B01, B02)',
    capacity INT NOT NULL COMMENT 'Sức chứa (số người)',
    floor INT DEFAULT 1 COMMENT 'Tầng (1, 2, 3...)',
    location VARCHAR(100) DEFAULT NULL COMMENT 'Vị trí (VD: Gần cửa sổ, Góc yên tĩnh)',
    status ENUM('empty', 'occupied', 'reserved', 'cleaning', 'maintenance') DEFAULT 'empty' COMMENT 'Trạng thái bàn',
    notes TEXT DEFAULT NULL COMMENT 'Ghi chú về bàn',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_table_number (table_number),
    INDEX idx_status (status),
    INDEX idx_floor (floor),
    INDEX idx_capacity (capacity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý bàn';

-- ===================================================================
-- BẢNG ĐẶT BÀN (Reservations)
-- ===================================================================
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL COMMENT 'ID người dùng (nếu đã đăng nhập)',
    customer_name VARCHAR(100) NOT NULL COMMENT 'Tên khách hàng',
    phone VARCHAR(20) NOT NULL COMMENT 'Số điện thoại',
    email VARCHAR(100) DEFAULT NULL COMMENT 'Email',
    reservation_date DATE NOT NULL COMMENT 'Ngày đặt bàn',
    reservation_time TIME NOT NULL COMMENT 'Giờ đặt bàn',
    number_of_guests INT NOT NULL COMMENT 'Số lượng khách',
    table_id INT DEFAULT NULL COMMENT 'ID bàn được phân bổ',
    notes TEXT DEFAULT NULL COMMENT 'Ghi chú đặc biệt',
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending' COMMENT 'Trạng thái đặt bàn',
    confirmed_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian xác nhận',
    cancelled_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian hủy',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id)
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_table_id (table_id),
    INDEX idx_reservation_date (reservation_date),
    INDEX idx_status (status),
    INDEX idx_phone (phone),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đặt bàn';

-- ===================================================================
-- BẢNG ĐƠN HÀNG (Orders)
-- ===================================================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(20) DEFAULT NULL UNIQUE COMMENT 'Mã đơn hàng (tự động tạo)',
    user_id INT DEFAULT NULL COMMENT 'ID người dùng (nếu đã đăng nhập)',
    table_id INT NOT NULL COMMENT 'ID bàn',
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian đặt hàng',
    total_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Tổng tiền',
    discount_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Số tiền giảm giá',
    final_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Thành tiền cuối cùng',
    status ENUM('pending', 'preparing', 'ready', 'served', 'completed', 'paid', 'cancelled') DEFAULT 'pending' COMMENT 'Trạng thái đơn hàng',
    payment_method ENUM('cash', 'card', 'transfer', 'momo', 'zalopay') DEFAULT 'cash' COMMENT 'Phương thức thanh toán',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid' COMMENT 'Trạng thái thanh toán',
    paid_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian thanh toán',
    notes TEXT DEFAULT NULL COMMENT 'Ghi chú đơn hàng',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_order_number (order_number),
    INDEX idx_user_id (user_id),
    INDEX idx_table_id (table_id),
    INDEX idx_order_date (order_date),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đơn hàng';

-- ===================================================================
-- BẢNG CHI TIẾT ĐƠN HÀNG (Order Items)
-- ===================================================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL COMMENT 'ID đơn hàng',
    dish_id INT NOT NULL COMMENT 'ID món ăn',
    dish_name VARCHAR(200) NOT NULL COMMENT 'Tên món ăn (lưu lại để tránh thay đổi giá sau)',
    quantity INT NOT NULL DEFAULT 1 COMMENT 'Số lượng',
    price DECIMAL(10, 2) NOT NULL COMMENT 'Giá tại thời điểm đặt (VNĐ)',
    subtotal DECIMAL(10, 2) NOT NULL COMMENT 'Thành tiền (quantity * price)',
    status ENUM('pending', 'preparing', 'ready', 'served', 'cancelled') DEFAULT 'pending' COMMENT 'Trạng thái món',
    notes TEXT DEFAULT NULL COMMENT 'Ghi chú đặc biệt cho món',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian thêm vào đơn',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    prepared_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian bắt đầu chế biến',
    ready_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian hoàn thành',
    served_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian phục vụ',
    
    -- Foreign keys với CASCADE
    FOREIGN KEY (order_id) REFERENCES orders(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (dish_id) REFERENCES dishes(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_order_id (order_id),
    INDEX idx_dish_id (dish_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chi tiết đơn hàng';

-- ===================================================================
-- BẢNG THANH TOÁN (Payments) - Tùy chọn, để theo dõi chi tiết thanh toán
-- ===================================================================
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL COMMENT 'ID đơn hàng',
    amount DECIMAL(10, 2) NOT NULL COMMENT 'Số tiền thanh toán',
    payment_method ENUM('cash', 'card', 'transfer', 'momo', 'zalopay') NOT NULL COMMENT 'Phương thức',
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian thanh toán',
    transaction_id VARCHAR(100) DEFAULT NULL COMMENT 'Mã giao dịch (nếu có)',
    notes TEXT DEFAULT NULL COMMENT 'Ghi chú',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    INDEX idx_order_id (order_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thanh toán';

-- ===================================================================
-- VIEWS - Các view hữu ích cho báo cáo và thống kê
-- ===================================================================

-- View: Tổng hợp đơn hàng với thông tin khách hàng và bàn
CREATE OR REPLACE VIEW vw_orders_detail AS
SELECT 
    o.id,
    o.order_number,
    o.order_date,
    o.total_amount,
    o.discount_amount,
    o.final_amount,
    o.status,
    o.payment_method,
    o.payment_status,
    u.full_name AS customer_name,
    u.email AS customer_email,
    u.phone AS customer_phone,
    t.table_number,
    t.capacity AS table_capacity,
    COUNT(oi.id) AS total_items,
    SUM(oi.quantity) AS total_quantity
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN tables t ON o.table_id = t.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- View: Doanh thu theo ngày
CREATE OR REPLACE VIEW vw_daily_revenue AS
SELECT 
    DATE(order_date) AS date,
    COUNT(*) AS total_orders,
    SUM(final_amount) AS total_revenue,
    AVG(final_amount) AS avg_order_value,
    SUM(CASE WHEN payment_status = 'paid' THEN final_amount ELSE 0 END) AS paid_revenue
FROM orders
WHERE status IN ('completed', 'paid')
GROUP BY DATE(order_date)
ORDER BY date DESC;

-- View: Top món ăn bán chạy
CREATE OR REPLACE VIEW vw_top_dishes AS
SELECT 
    d.id,
    d.name,
    d.price,
    c.name AS category_name,
    SUM(oi.quantity) AS total_sold,
    SUM(oi.subtotal) AS total_revenue,
    COUNT(DISTINCT oi.order_id) AS total_orders
FROM dishes d
LEFT JOIN order_items oi ON d.id = oi.dish_id
LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'
LEFT JOIN categories c ON d.category_id = c.id
GROUP BY d.id, d.name, d.price, c.name
ORDER BY total_sold DESC;

-- View: Thống kê bàn
CREATE OR REPLACE VIEW vw_table_statistics AS
SELECT 
    t.id,
    t.table_number,
    t.capacity,
    t.status,
    COUNT(DISTINCT o.id) AS total_orders,
    SUM(CASE WHEN o.status = 'paid' THEN o.final_amount ELSE 0 END) AS total_revenue,
    MAX(o.order_date) AS last_order_date
FROM tables t
LEFT JOIN orders o ON t.id = o.table_id
GROUP BY t.id, t.table_number, t.capacity, t.status;

-- ===================================================================
-- DỮ LIỆU MẪU
-- ===================================================================

-- Tài khoản người dùng
-- Password hash cho "admin123": $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- Password hash cho "123456": $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (email, password, full_name, phone, role, status) VALUES 
('admin@huongviet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', '0901234567', 'admin', 'active'),
('khach1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0912345678', 'customer', 'active'),
('khach2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0923456789', 'customer', 'active'),
('khach3@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0934567890', 'customer', 'active');

-- Danh mục món ăn
INSERT INTO categories (name, description, display_order, status) VALUES 
('Khai vị', 'Các món khai vị, salad, gỏi', 1, 'active'),
('Món chính', 'Các món chính, món nướng, món xào', 2, 'active'),
('Món phụ', 'Các món phụ, rau củ, đồ chiên', 3, 'active'),
('Tráng miệng', 'Kem, bánh ngọt, trái cây', 4, 'active'),
('Đồ uống', 'Nước ngọt, nước ép, cà phê, trà', 5, 'active'),
('Đặc biệt', 'Món đặc biệt của nhà hàng', 6, 'active');

-- Món ăn mẫu
INSERT INTO dishes (name, description, price, cost_price, category_id, status, is_featured, preparation_time, image) VALUES 
-- Khai vị
('Salad trộn', 'Salad rau củ tươi ngon với sốt đặc biệt', 45000, 20000, 1, 'available', TRUE, 10, 'salad.jpg'),
('Gỏi cuốn', 'Gỏi cuốn tôm thịt tươi ngon', 35000, 15000, 1, 'available', FALSE, 15, 'goi-cuon.jpg'),
('Nem nướng', 'Nem nướng Nha Trang đặc biệt', 55000, 25000, 1, 'available', TRUE, 20, 'nem-nuong.jpg'),
('Chả giò', 'Chả giò giòn tan, nhân đầy đặn', 40000, 18000, 1, 'available', FALSE, 15, 'cha-gio.jpg'),

-- Món chính
('Bò bít tết', 'Bít tết bò Úc cao cấp, thịt mềm', 250000, 120000, 2, 'available', TRUE, 30, 'bo-bitet.jpg'),
('Cá hồi nướng', 'Cá hồi Na Uy nướng muối ớt', 280000, 140000, 2, 'available', TRUE, 25, 'ca-hoi.jpg'),
('Tôm hùm nướng', 'Tôm hùm tươi sống nướng bơ tỏi', 350000, 180000, 2, 'available', TRUE, 35, 'tom-hum.jpg'),
('Cơm chiên dương châu', 'Cơm chiên hải sản đầy đặn', 65000, 30000, 2, 'available', FALSE, 15, 'com-chien.jpg'),
('Mì Ý sốt bò bằm', 'Mì Ý spaghetti sốt bò bằm thơm ngon', 85000, 40000, 2, 'available', FALSE, 20, 'mi-y.jpg'),
('Phở bò', 'Phở bò truyền thống, nước dùng đậm đà', 75000, 35000, 2, 'available', FALSE, 15, 'pho-bo.jpg'),
('Bún chả', 'Bún chả Hà Nội chính hiệu', 65000, 30000, 2, 'available', FALSE, 15, 'bun-cha.jpg'),

-- Món phụ
('Rau củ xào', 'Rau củ xào thập cẩm tươi ngon', 45000, 20000, 3, 'available', FALSE, 10, 'rau-xao.jpg'),
('Khoai tây chiên', 'Khoai tây chiên giòn, vàng đẹp', 35000, 15000, 3, 'available', FALSE, 10, 'khoai-chien.jpg'),
('Rau muống xào tỏi', 'Rau muống xào tỏi giòn ngon', 30000, 12000, 3, 'available', FALSE, 8, 'rau-muong.jpg'),

-- Tráng miệng
('Kem vani', 'Kem vani Ý cao cấp', 35000, 15000, 4, 'available', FALSE, 5, 'kem.jpg'),
('Tiramisu', 'Bánh Tiramisu truyền thống Ý', 55000, 25000, 4, 'available', TRUE, 5, 'tiramisu.jpg'),
('Chè đậu xanh', 'Chè đậu xanh mát lạnh', 25000, 10000, 4, 'available', FALSE, 5, 'che-dau-xanh.jpg'),
('Trái cây theo mùa', 'Trái cây tươi theo mùa', 40000, 20000, 4, 'available', FALSE, 5, 'trai-cay.jpg'),

-- Đồ uống
('Nước ngọt', 'Coca/Pepsi/7Up (lon 330ml)', 15000, 8000, 5, 'available', FALSE, 2, 'nuoc-ngot.jpg'),
('Nước cam ép', 'Nước cam tươi ép nguyên chất', 25000, 12000, 5, 'available', FALSE, 5, 'nuoc-cam.jpg'),
('Nước chanh dây', 'Nước chanh dây mát lạnh', 25000, 12000, 5, 'available', FALSE, 5, 'chanh-day.jpg'),
('Trà đá', 'Trà đá miễn phí', 0, 0, 5, 'available', FALSE, 2, 'tra-da.jpg'),
('Cà phê đen', 'Cà phê đen nóng/đá', 20000, 8000, 5, 'available', FALSE, 5, 'ca-phe.jpg'),
('Cà phê sữa', 'Cà phê sữa đá', 25000, 10000, 5, 'available', FALSE, 5, 'ca-phe-sua.jpg'),
('Sinh tố', 'Sinh tố các loại (dâu, xoài, bơ...)', 35000, 15000, 5, 'available', FALSE, 8, 'sinh-to.jpg'),

-- Đặc biệt
('Lẩu hải sản', 'Lẩu hải sản tươi sống đầy đặn (2-3 người)', 450000, 250000, 6, 'available', TRUE, 40, 'lau-hai-san.jpg'),
('Set menu cao cấp', 'Set menu đầy đủ 5 món cho 2 người', 800000, 400000, 6, 'available', TRUE, 60, 'set-menu.jpg');

-- Bàn
INSERT INTO tables (table_number, capacity, floor, location, status) VALUES 
('B01', 2, 1, 'Gần cửa sổ', 'empty'),
('B02', 2, 1, 'Góc yên tĩnh', 'empty'),
('B03', 4, 1, 'Giữa phòng', 'empty'),
('B04', 4, 1, 'Gần cửa sổ', 'empty'),
('B05', 6, 1, 'Phòng riêng nhỏ', 'empty'),
('B06', 6, 2, 'Góc yên tĩnh', 'empty'),
('B07', 8, 2, 'Phòng VIP', 'empty'),
('B08', 4, 2, 'Gần ban công', 'empty'),
('B09', 2, 2, 'Góc yên tĩnh', 'empty'),
('B10', 4, 2, 'Giữa phòng', 'empty'),
('B11', 10, 3, 'Phòng tiệc lớn', 'empty'),
('B12', 12, 3, 'Phòng tiệc VIP', 'empty');

-- Đặt bàn mẫu
INSERT INTO reservations (user_id, customer_name, phone, email, reservation_date, reservation_time, number_of_guests, table_id, status, notes) VALUES 
(2, 'Nguyễn Văn A', '0912345678', 'khach1@gmail.com', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', 4, 3, 'confirmed', 'Kỷ niệm sinh nhật'),
(3, 'Trần Thị B', '0923456789', 'khach2@gmail.com', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '19:30:00', 2, 1, 'pending', 'Bàn gần cửa sổ'),
(NULL, 'Lê Văn C', '0934567890', 'khach3@gmail.com', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '20:00:00', 6, 5, 'pending', NULL);

-- Đơn hàng mẫu
INSERT INTO orders (order_number, user_id, table_id, order_date, status, payment_method, payment_status) VALUES 
('ORD20250108001', 2, 3, DATE_SUB(NOW(), INTERVAL 2 DAY), 'paid', 'cash', 'paid'),
('ORD20250108002', 3, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'completed', 'card', 'paid'),
('ORD20250108003', NULL, 4, NOW(), 'preparing', 'cash', 'unpaid');

-- Chi tiết đơn hàng mẫu (tính subtotal thủ công)
INSERT INTO order_items (order_id, dish_id, dish_name, quantity, price, subtotal, status) VALUES 
-- Đơn hàng 1
(1, 1, 'Salad trộn', 2, 45000, 90000, 'served'),
(1, 5, 'Bò bít tết', 1, 250000, 250000, 'served'),
(1, 14, 'Kem vani', 2, 35000, 70000, 'served'),
(1, 19, 'Nước ngọt', 2, 15000, 30000, 'served'),

-- Đơn hàng 2
(2, 2, 'Gỏi cuốn', 1, 35000, 35000, 'served'),
(2, 6, 'Cá hồi nướng', 1, 280000, 280000, 'served'),
(2, 12, 'Rau củ xào', 1, 45000, 45000, 'served'),
(2, 15, 'Tiramisu', 1, 55000, 55000, 'served'),
(2, 19, 'Nước cam ép', 2, 25000, 50000, 'served'),

-- Đơn hàng 3 (đang chuẩn bị)
(3, 3, 'Nem nướng', 2, 55000, 110000, 'preparing'),
(3, 7, 'Tôm hùm nướng', 1, 350000, 350000, 'pending'),
(3, 13, 'Khoai tây chiên', 1, 35000, 35000, 'preparing'),
(3, 20, 'Cà phê đen', 2, 20000, 40000, 'pending');

-- Cập nhật total_amount và final_amount cho các đơn hàng
UPDATE orders SET 
    total_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = 1),
    final_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = 1)
WHERE id = 1;

UPDATE orders SET 
    total_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = 2),
    final_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = 2)
WHERE id = 2;

UPDATE orders SET 
    total_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = 3),
    final_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = 3)
WHERE id = 3;

-- Thanh toán mẫu
INSERT INTO payments (order_id, amount, payment_method, payment_date, transaction_id) VALUES 
(1, 440000, 'cash', DATE_SUB(NOW(), INTERVAL 2 DAY), NULL),
(2, 465000, 'card', DATE_SUB(NOW(), INTERVAL 1 DAY), 'TXN20250108002');

-- ===================================================================
-- HOÀN TẤT
-- ===================================================================

-- Kiểm tra dữ liệu đã import
SELECT 'Database đã được tạo thành công!' AS message;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_categories FROM categories;
SELECT COUNT(*) AS total_dishes FROM dishes;
SELECT COUNT(*) AS total_tables FROM tables;
SELECT COUNT(*) AS total_orders FROM orders;
SELECT COUNT(*) AS total_order_items FROM order_items;

-- ===================================================================
-- THÔNG TIN TÀI KHOẢN MẶC ĐỊNH
-- ===================================================================
-- 👨‍💼 ADMIN:
--    Email: admin@huongviet.com
--    Password: admin123
--
-- 👤 KHÁCH HÀNG:
--    Email: khach1@gmail.com
--    Password: 123456
--
--    Email: khach2@gmail.com
--    Password: 123456
--
--    Email: khach3@gmail.com
--    Password: 123456
-- ===================================================================
--
-- LƯU Ý: File này đã loại bỏ Triggers và Stored Procedures để tương thích với phpMyAdmin
-- Nếu cần Triggers và Stored Procedures, import file database_triggers_procedures.sql sau
-- ===================================================================
