<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

// Xử lý cập nhật trạng thái đặt bàn
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE reservations SET status = '$status' WHERE id = $id");
    
    if ($status == 'cancelled') {
        $reservation = $conn->query("SELECT table_id FROM reservations WHERE id = $id")->fetch_assoc();
        if ($reservation) {
            $conn->query("UPDATE tables SET status = 'empty' WHERE id = {$reservation['table_id']}");
        }
    }
    
    $success = "Cập nhật trạng thái thành công!";
}

// Lấy danh sách đặt bàn
$reservations = $conn->query("
    SELECT r.*, t.table_number 
    FROM reservations r
    LEFT JOIN tables t ON r.table_id = t.id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đặt Bàn - QL Nhà Hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ Quản Lý Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Tổng quan</a></li>
                <li><a href="dishes.php">Món ăn</a></li>
                <li><a href="tables.php">Bàn</a></li>
                <li><a href="reservations.php" class="active">Đặt bàn</a></li>
                <li><a href="orders.php">Đơn hàng</a></li>
                <li><a href="../index.php">Trang chủ</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Quản Lý Đặt Bàn</h1>
        </div>
    </div>

    <div class="container">
        <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="admin-section">
            <h2>Danh Sách Đặt Bàn</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Điện thoại</th>
                            <th>Ngày</th>
                            <th>Giờ</th>
                            <th>Số khách</th>
                            <th>Bàn</th>
                            <th>Ghi chú</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($res = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $res['id']; ?></td>
                            <td><?php echo htmlspecialchars($res['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($res['phone']); ?></td>
                            <td><?php echo formatDate($res['reservation_date']); ?></td>
                            <td><?php echo date('H:i', strtotime($res['reservation_time'])); ?></td>
                            <td><?php echo $res['number_of_guests']; ?></td>
                            <td><?php echo $res['table_number'] ?? '-'; ?></td>
                            <td style="max-width: 300px; word-wrap: break-word;">
                                <?php 
                                $notes = $res['notes'] ?? '';
                                if ($notes) {
                                    // Hiển thị ghi chú với định dạng tốt hơn
                                    $notes_lines = explode("\n", $notes);
                                    foreach ($notes_lines as $line) {
                                        $line = trim($line);
                                        if ($line) {
                                            // Làm nổi bật thông tin về đặt món trước
                                            if (strpos($line, 'Đã đặt món trước') !== false) {
                                                echo '<div style="color: #10b981; font-weight: 600; margin-bottom: 5px;">📋 ' . htmlspecialchars($line) . '</div>';
                                            } else {
                                                echo '<div style="margin-bottom: 3px;">' . htmlspecialchars($line) . '</div>';
                                            }
                                        }
                                    }
                                } else {
                                    echo '<span style="color: #999;">-</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $res['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => 'Chờ xác nhận',
                                        'confirmed' => 'Đã xác nhận',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    echo $status_text[$res['status']];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $res['id']; ?>">
                                    <select name="status" class="form-control-sm">
                                        <option value="pending" <?php echo $res['status'] == 'pending' ? 'selected' : ''; ?>>Chờ</option>
                                        <option value="confirmed" <?php echo $res['status'] == 'confirmed' ? 'selected' : ''; ?>>Xác nhận</option>
                                        <option value="completed" <?php echo $res['status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                        <option value="cancelled" <?php echo $res['status'] == 'cancelled' ? 'selected' : ''; ?>>Hủy</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary">Cập nhật</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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

