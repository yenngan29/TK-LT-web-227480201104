# Hệ Thống Quản Lý Nhà Hàng

Hệ thống quản lý nhà hàng đơn giản với đầy đủ chức năng đặt chỗ, gọi món, hiển thị bếp và thanh toán.

## Tính Năng

### 1. Trang Chủ Giới Thiệu
- Giới thiệu về nhà hàng
- Hiển thị các tính năng nổi bật
- Thông tin liên hệ

### 2. Ứng Dụng Chọn Món (Dành cho khách tại bàn)
- Chọn bàn
- Xem menu theo danh mục
- Thêm món vào giỏ hàng
- Gửi đơn hàng đến bếp

### 3. Màn Hình Bếp
- Hiển thị đơn hàng theo thời gian thực
- Cập nhật trạng thái món ăn
- Đánh dấu hoàn thành

### 4. Hệ Thống Đặt Bàn Trực Tuyến
- Form đặt bàn online
- Chọn ngày giờ, số lượng khách
- Tự động phân bổ bàn phù hợp

### 5. Thanh Toán & Hóa Đơn
- Tính tiền tự động
- Nhiều phương thức thanh toán
- In hóa đơn

### 6. Quản Lý Admin
- Quản lý món ăn
- Quản lý bàn
- Quản lý đơn hàng
- Quản lý đặt bàn
- Thống kê doanh thu

## Cài Đặt

### Yêu Cầu Hệ Thống
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web server (Apache/Nginx)

### Hướng Dẫn Cài Đặt

#### 1. Import Database
Mở phpMyAdmin hoặc MySQL command line và chạy file `database.sql`:

```bash
mysql -u root -p < database.sql
```

Hoặc trong phpMyAdmin:
- Truy cập http://localhost/phpmyadmin
- Click "Import"
- Chọn file `database.sql`
- Click "Go"

#### 2. Cấu Hình Database
Mở file `config.php` và chỉnh sửa thông tin kết nối nếu cần:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ql_nha_hang');
```

#### 3. Chạy Website
Truy cập: http://localhost/QL_Khach_San

## Cấu Trúc Thư Mục

```
QL_Khach_San/
├── admin/                  # Trang quản lý admin
│   ├── index.php          # Tổng quan
│   ├── dishes.php         # Quản lý món ăn
│   ├── tables.php         # Quản lý bàn
│   ├── orders.php         # Quản lý đơn hàng
│   ├── order_detail.php   # Chi tiết đơn hàng
│   └── reservations.php   # Quản lý đặt bàn
├── api/                   # API endpoints
│   ├── create_order.php   # Tạo đơn hàng
│   └── update_order_status.php  # Cập nhật trạng thái
├── assets/
│   └── css/
│       └── style.css      # CSS chính
├── config.php             # Cấu hình database
├── database.sql           # SQL tạo database
├── index.php              # Trang chủ
├── menu.php               # Xem menu
├── order.php              # Gọi món
├── reservation.php        # Đặt bàn
├── kitchen.php            # Màn hình bếp
├── billing.php            # Thanh toán
└── invoice.php            # Hóa đơn
```

## Hướng Dẫn Sử Dụng

### Dành cho Khách Hàng

#### Đặt Bàn Trực Tuyến
1. Vào trang "Đặt bàn"
2. Điền thông tin: Họ tên, SĐT, Email, Ngày giờ, Số khách
3. Thêm ghi chú nếu cần
4. Nhấn "Đặt Bàn"

#### Gọi Món Tại Bàn
1. Vào trang "Gọi món"
2. Chọn bàn của mình
3. Chọn món ăn từ menu
4. Thêm vào giỏ hàng và điều chỉnh số lượng
5. Nhấn "Gửi Đơn Hàng"

### Dành cho Nhân Viên Bếp

1. Truy cập trang "Màn hình bếp" (kitchen.php)
2. Xem các đơn hàng mới
3. Nhấn "Bắt Đầu Làm" khi bắt đầu chế biến
4. Nhấn "Hoàn Thành" khi món đã xong

### Dành cho Thu Ngân

1. Vào trang "Thanh toán" (billing.php)
2. Chọn bàn cần thanh toán
3. Kiểm tra hóa đơn
4. Chọn phương thức thanh toán
5. Nhấn "Thanh Toán"
6. In hóa đơn cho khách

### Dành cho Quản Lý

#### Quản Lý Món Ăn
1. Vào Admin > Món ăn
2. Thêm/Sửa/Xóa món ăn
3. Bật/Tắt trạng thái món ăn

#### Quản Lý Bàn
1. Vào Admin > Bàn
2. Thêm bàn mới
3. Cập nhật trạng thái bàn (Trống/Có khách/Đã đặt)

#### Quản Lý Đơn Hàng
1. Vào Admin > Đơn hàng
2. Xem danh sách đơn hàng
3. Xem chi tiết từng đơn
4. In lại hóa đơn

#### Quản Lý Đặt Bàn
1. Vào Admin > Đặt bàn
2. Xem danh sách đặt bàn
3. Xác nhận/Hủy đặt bàn

## Dữ Liệu Mẫu

Database đã có sẵn dữ liệu mẫu:
- 5 danh mục món ăn
- 14 món ăn
- 10 bàn (2-8 ghế)

## Công Nghệ Sử Dụng

- **Backend**: PHP (Native)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Thiết kế**: Responsive, Mobile-friendly

## Tính Năng Nổi Bật

✅ Giao diện đẹp, hiện đại  
✅ Responsive trên mọi thiết bị  
✅ Không cần framework phức tạp  
✅ Code đơn giản, dễ hiểu  
✅ Đầy đủ chức năng quản lý  
✅ Màn hình bếp realtime  
✅ Hệ thống thanh toán hoàn chỉnh  
✅ In hóa đơn  

## Lưu Ý

- Màn hình bếp tự động refresh mỗi 30 giây
- Nên sử dụng trên Laragon hoặc XAMPP
- Chạy tốt nhất trên Chrome/Edge
- Database sử dụng utf8mb4 để hỗ trợ tiếng Việt

## Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
1. Database đã import thành công chưa
2. Cấu hình trong config.php đúng chưa
3. Web server đã bật chưa
4. PHP extension mysqli đã bật chưa

## Tác Giả

Đề tài: ĐA2-TH046  
Học phần: Đồ án 2  
Mô tả: Xây dựng hệ thống đặt chỗ và chọn món ăn trong nhà hàng
