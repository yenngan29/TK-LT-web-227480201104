-- Cập nhật database để thêm hệ thống đăng nhập
USE ql_nha_hang;

-- Bảng người dùng (khách hàng + admin)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('customer', 'admin') DEFAULT 'customer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Cập nhật bảng reservations để liên kết với user
ALTER TABLE reservations ADD COLUMN user_id INT NULL AFTER id;
ALTER TABLE reservations ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- Cập nhật bảng orders để liên kết với user  
ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id;
ALTER TABLE orders ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- Tạo tài khoản admin mặc định
-- Password: admin123 (đã hash)
INSERT INTO users (email, password, full_name, phone, role) VALUES 
('admin@huongviet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', '0901234567', 'admin');

-- Tạo tài khoản khách mẫu
-- Password: 123456 (đã hash)
INSERT INTO users (email, password, full_name, phone, role) VALUES 
('khach1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0912345678', 'customer'),
('khach2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0923456789', 'customer');





