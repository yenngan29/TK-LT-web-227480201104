<?php
require_once 'config.php';
session_start();
require_once 'auth.php';
$user = isLoggedIn() ? getCurrentUser() : null;

$conn = getDBConnection();

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $reservation_date = $conn->real_escape_string($_POST['reservation_date']);
    $reservation_time = $conn->real_escape_string($_POST['reservation_time']);
    $number_of_guests = intval($_POST['number_of_guests']);
    $notes = $conn->real_escape_string($_POST['notes']);
    $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    // Tìm bàn phù hợp
    $table_result = $conn->query("SELECT id FROM tables WHERE capacity >= $number_of_guests AND status = 'empty' ORDER BY capacity ASC LIMIT 1");
    
    if ($table_result->num_rows > 0) {
        $table = $table_result->fetch_assoc();
        $table_id = $table['id'];
        
        $sql = "INSERT INTO reservations (user_id, customer_name, phone, email, reservation_date, reservation_time, number_of_guests, table_id, notes) 
                VALUES (" . ($user_id ? $user_id : "NULL") . ", '$customer_name', '$phone', '$email', '$reservation_date', '$reservation_time', $number_of_guests, $table_id, '$notes')";
        
        if ($conn->query($sql)) {
            $conn->query("UPDATE tables SET status = 'reserved' WHERE id = $table_id");
            $success = true;
        } else {
            $error = "Có lỗi xảy ra khi đặt bàn";
        }
    } else {
        $error = "Không tìm thấy bàn phù hợp. Vui lòng liên hệ nhà hàng.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Bàn - QL Nhà Hàng</title>
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
                <li><a href="menu.php">Thực đơn</a></li>
                <li><a href="about.php">Giới thiệu</a></li>
                <li><a href="reservation.php" class="active">Đặt bàn</a></li>
                <?php if ($user): ?>
                    <li><a href="customer_dashboard.php">👤 <?php echo htmlspecialchars($user['full_name']); ?></a></li>
                    <li><a href="logout.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="login.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Đặt Bàn Trước</h1>
            <p>Đặt bàn ngay để đảm bảo có chỗ</p>
        </div>
    </div>

    <section class="reservation-section">
        <div class="container">
            <div class="form-container">
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <h3>✓ Đặt bàn thành công!</h3>
                    <p>Cảm ơn bạn đã đặt bàn. Chúng tôi sẽ liên hệ với bạn để xác nhận.</p>
                    <a href="index.php" class="btn btn-primary">Về trang chủ</a>
                </div>
                <?php else: ?>
                
                <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="reservation-form">
                    <?php if ($user): ?>
                    <div style="background: #e0e7ff; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin: 0;">👤 Đang đặt bàn với tài khoản: <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Họ và tên *</label>
                        <input type="text" name="customer_name" required class="form-control" 
                               value="<?php echo $user ? htmlspecialchars($user['full_name']) : ''; ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại *</label>
                            <input type="tel" name="phone" required class="form-control"
                                   value="<?php echo $user ? htmlspecialchars($user['phone']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Ngày đặt *</label>
                            <input type="date" name="reservation_date" required class="form-control" 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Giờ đặt *</label>
                            <input type="time" name="reservation_time" required class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Số lượng khách *</label>
                        <select name="number_of_guests" required class="form-control">
                            <option value="">Chọn số lượng</option>
                            <option value="1">1 người</option>
                            <option value="2">2 người (Bàn đôi)</option>
                            <option value="3">3 người</option>
                            <option value="4">4 người (Bàn 4)</option>
                            <option value="5">5 người</option>
                            <option value="6">6 người (Bàn 6)</option>
                            <option value="7">7 người</option>
                            <option value="8">8 người trở lên (Bàn lớn)</option>
                        </select>
                        <small style="color: #666; margin-top: 5px; display: block;">
                            💡 Chúng tôi sẽ tự động chọn bàn phù hợp với số lượng khách
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea name="notes" rows="4" class="form-control" 
                                  placeholder="Yêu cầu đặc biệt, món ăn đặt trước..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Đặt Bàn</button>
                </form>
                <?php endif; ?>
            </div>

            <div class="reservation-info">
                <h3>📋 Hướng Dẫn Đặt Bàn</h3>
                
                <div class="info-item" style="background: #e0e7ff; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <strong>🎯 Quy trình đặt bàn:</strong>
                    <ol style="margin: 10px 0 0 20px; line-height: 2;">
                        <li>Điền thông tin và số lượng khách</li>
                        <li>Hệ thống <strong>tự động chọn bàn</strong> phù hợp</li>
                        <li>Nhận xác nhận qua điện thoại</li>
                        <li>Đến nhà hàng đúng giờ</li>
                        <li>Nhân viên dẫn bạn đến bàn đã đặt</li>
                    </ol>
                </div>

                <div class="info-item">
                    <strong>🪑 Các loại bàn:</strong>
                    <p>• Bàn 2 người: Bàn đôi, ấm cúng</p>
                    <p>• Bàn 4 người: Bàn gia đình nhỏ</p>
                    <p>• Bàn 6 người: Bàn nhóm bạn</p>
                    <p>• Bàn 8+ người: Bàn tiệc, sự kiện</p>
                </div>
                
                <div class="info-item">
                    <strong>📍 Liên hệ:</strong>
                    <p>📞 (028) 1234 5678</p>
                    <p>📧 info@qlnhahang.com</p>
                    <p>🏠 123 Đường ABC, Q1, TP.HCM</p>
                </div>
                
                <div class="info-item">
                    <strong>⏰ Giờ mở cửa:</strong>
                    <p>T2-T6: 10:00 - 22:00</p>
                    <p>T7-CN: 09:00 - 23:00</p>
                </div>
                
                <div class="info-item" style="background: #fef3c7; padding: 10px; border-radius: 5px;">
                    <strong>💡 Mẹo:</strong>
                    <p>- Đặt trước 2-3 giờ để có bàn tốt</p>
                    <p>- Cuối tuần nên đặt trước 1 ngày</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

