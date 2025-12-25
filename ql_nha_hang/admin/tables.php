<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

// Xử lý cập nhật trạng thái bàn
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $id = intval($_POST['id']);
        $status = $conn->real_escape_string($_POST['status']);
        $conn->query("UPDATE tables SET status = '$status' WHERE id = $id");
        $success = "Cập nhật trạng thái thành công!";
    } elseif (isset($_POST['add'])) {
        $table_number = $conn->real_escape_string($_POST['table_number']);
        $capacity = intval($_POST['capacity']);
        $conn->query("INSERT INTO tables (table_number, capacity) VALUES ('$table_number', $capacity)");
        $success = "Thêm bàn thành công!";
    }
}

// Lấy danh sách bàn
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bàn - QL Nhà Hàng</title>
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
                <li><a href="tables.php" class="active">Bàn</a></li>
                <li><a href="reservations.php">Đặt bàn</a></li>
                <li><a href="orders.php">Đơn hàng</a></li>
                <li><a href="../index.php">Trang chủ</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Quản Lý Bàn</h1>
        </div>
    </div>

    <div class="container">
        <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="admin-section">
            <h2>Thêm Bàn Mới</h2>
            <form method="POST" class="form-inline">
                <div class="form-row">
                    <input type="text" name="table_number" placeholder="Số bàn (vd: B11)" required class="form-control">
                    <input type="number" name="capacity" placeholder="Số ghế" min="1" required class="form-control">
                    <button type="submit" name="add" class="btn btn-primary">Thêm Bàn</button>
                </div>
            </form>
        </div>

        <div class="admin-section">
            <h2>Danh Sách Bàn</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Số bàn</th>
                            <th>Sức chứa</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($table = $tables->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $table['table_number']; ?></strong></td>
                            <td><?php echo $table['capacity']; ?> người</td>
                            <td>
                                <span class="badge badge-<?php echo $table['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'empty' => 'Trống',
                                        'occupied' => 'Có khách',
                                        'reserved' => 'Đã đặt'
                                    ];
                                    echo $status_text[$table['status']];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $table['id']; ?>">
                                    <select name="status" class="form-control-sm">
                                        <option value="empty" <?php echo $table['status'] == 'empty' ? 'selected' : ''; ?>>Trống</option>
                                        <option value="occupied" <?php echo $table['status'] == 'occupied' ? 'selected' : ''; ?>>Có khách</option>
                                        <option value="reserved" <?php echo $table['status'] == 'reserved' ? 'selected' : ''; ?>>Đã đặt</option>
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

