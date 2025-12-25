<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

$success = '';
$error = '';

// Xử lý thêm/sửa/xóa mã giảm giá
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_promotion'])) {
        $code = $conn->real_escape_string($_POST['code']);
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $discount_type = $conn->real_escape_string($_POST['discount_type']);
        $discount_value = floatval($_POST['discount_value']);
        $min_order_amount = floatval($_POST['min_order_amount'] ?? 0);
        $max_discount_amount = !empty($_POST['max_discount_amount']) ? floatval($_POST['max_discount_amount']) : 'NULL';
        $min_reservations = intval($_POST['min_reservations'] ?? 0);
        $min_orders = intval($_POST['min_orders'] ?? 0);
        $start_date = !empty($_POST['start_date']) ? "'" . $conn->real_escape_string($_POST['start_date']) . "'" : 'NULL';
        $end_date = !empty($_POST['end_date']) ? "'" . $conn->real_escape_string($_POST['end_date']) . "'" : 'NULL';
        $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 'NULL';
        $auto_apply = isset($_POST['auto_apply']) ? 1 : 0;
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "INSERT INTO promotions (code, name, description, discount_type, discount_value, min_order_amount, max_discount_amount, 
                min_reservations, min_orders, start_date, end_date, usage_limit, auto_apply, status) 
                VALUES ('$code', '$name', '$description', '$discount_type', $discount_value, $min_order_amount, $max_discount_amount,
                $min_reservations, $min_orders, $start_date, $end_date, $usage_limit, $auto_apply, '$status')";
        
        if ($conn->query($sql)) {
            $success = "Thêm mã giảm giá thành công!";
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
    
    if (isset($_POST['update_promotion'])) {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $discount_type = $conn->real_escape_string($_POST['discount_type']);
        $discount_value = floatval($_POST['discount_value']);
        $min_order_amount = floatval($_POST['min_order_amount'] ?? 0);
        $max_discount_amount = !empty($_POST['max_discount_amount']) ? floatval($_POST['max_discount_amount']) : 'NULL';
        $min_reservations = intval($_POST['min_reservations'] ?? 0);
        $min_orders = intval($_POST['min_orders'] ?? 0);
        $start_date = !empty($_POST['start_date']) ? "'" . $conn->real_escape_string($_POST['start_date']) . "'" : 'NULL';
        $end_date = !empty($_POST['end_date']) ? "'" . $conn->real_escape_string($_POST['end_date']) . "'" : 'NULL';
        $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 'NULL';
        $auto_apply = isset($_POST['auto_apply']) ? 1 : 0;
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "UPDATE promotions SET 
                name = '$name', description = '$description', discount_type = '$discount_type', 
                discount_value = $discount_value, min_order_amount = $min_order_amount, 
                max_discount_amount = $max_discount_amount, min_reservations = $min_reservations, 
                min_orders = $min_orders, start_date = $start_date, end_date = $end_date, 
                usage_limit = $usage_limit, auto_apply = $auto_apply, status = '$status'
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            $success = "Cập nhật mã giảm giá thành công!";
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
    
    if (isset($_POST['delete_promotion'])) {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM promotions WHERE id = $id");
        $success = "Xóa mã giảm giá thành công!";
    }
}

// Lấy danh sách mã giảm giá
$promotions = $conn->query("SELECT * FROM promotions ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Mã Giảm Giá - QL Nhà Hàng</title>
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
                <li><a href="reservations.php">Đặt bàn</a></li>
                <li><a href="orders.php">Đơn hàng</a></li>
                <li><a href="promotions.php" class="active">Mã giảm giá</a></li>
                <li><a href="../index.php">Trang chủ</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Quản Lý Mã Giảm Giá</h1>
        </div>
    </div>

    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="admin-section">
            <h2>Thêm Mã Giảm Giá Mới</h2>
            <form method="POST" class="reservation-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã giảm giá *</label>
                        <input type="text" name="code" required class="form-control" placeholder="VD: VIP10, LOYALTY5">
                    </div>
                    <div class="form-group">
                        <label>Tên chương trình *</label>
                        <input type="text" name="name" required class="form-control" placeholder="VD: Khách hàng VIP - 10%">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Loại giảm giá *</label>
                        <select name="discount_type" required class="form-control">
                            <option value="percentage">Phần trăm (%)</option>
                            <option value="fixed">Số tiền cố định (VNĐ)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Giá trị giảm giá *</label>
                        <input type="number" name="discount_value" required class="form-control" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Đơn hàng tối thiểu (VNĐ)</label>
                        <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Giảm tối đa (VNĐ) - chỉ cho loại %</label>
                        <input type="number" name="max_discount_amount" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Số lần đặt bàn tối thiểu</label>
                        <input type="number" name="min_reservations" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Số lần đặt món tối thiểu</label>
                        <input type="number" name="min_orders" class="form-control" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Giới hạn số lần sử dụng</label>
                        <input type="number" name="usage_limit" class="form-control" min="1" placeholder="Để trống = không giới hạn">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái *</label>
                        <select name="status" required class="form-control">
                            <option value="active">Kích hoạt</option>
                            <option value="inactive">Tạm ngưng</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="auto_apply" value="1">
                        Tự động áp dụng cho khách hàng đủ điều kiện
                    </label>
                </div>
                
                <button type="submit" name="add_promotion" class="btn btn-primary">Thêm Mã Giảm Giá</button>
            </form>
        </div>

        <div class="admin-section">
            <h2>Danh Sách Mã Giảm Giá</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tên</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Điều kiện</th>
                            <th>Đã dùng</th>
                            <th>Tự động</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($promo = $promotions->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($promo['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($promo['name']); ?></td>
                            <td><?php echo $promo['discount_type'] == 'percentage' ? 'Phần trăm' : 'Số tiền'; ?></td>
                            <td>
                                <?php 
                                if ($promo['discount_type'] == 'percentage') {
                                    echo $promo['discount_value'] . '%';
                                } else {
                                    echo formatCurrency($promo['discount_value']);
                                }
                                ?>
                            </td>
                            <td>
                                <small>
                                    <?php if ($promo['min_reservations'] > 0): ?>
                                        Đặt bàn: <?php echo $promo['min_reservations']; ?> lần<br>
                                    <?php endif; ?>
                                    <?php if ($promo['min_orders'] > 0): ?>
                                        Đặt món: <?php echo $promo['min_orders']; ?> lần<br>
                                    <?php endif; ?>
                                    <?php if ($promo['min_order_amount'] > 0): ?>
                                        Đơn tối thiểu: <?php echo formatCurrency($promo['min_order_amount']); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <?php echo $promo['used_count']; ?>
                                <?php if ($promo['usage_limit']): ?>
                                    / <?php echo $promo['usage_limit']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($promo['auto_apply']): ?>
                                    <span class="badge badge-success">Có</span>
                                <?php else: ?>
                                    <span class="badge badge-occupied">Không</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $promo['status'] == 'active' ? 'success' : 'occupied'; ?>">
                                    <?php echo $promo['status'] == 'active' ? 'Kích hoạt' : 'Tạm ngưng'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?');">
                                    <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                                    <button type="submit" name="delete_promotion" class="btn btn-sm btn-danger">Xóa</button>
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

