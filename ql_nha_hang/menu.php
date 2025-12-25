<?php
require_once 'config.php';
session_start();
require_once 'auth.php';
$user = isLoggedIn() ? getCurrentUser() : null;

$conn = getDBConnection();

// Lấy danh sách danh mục
$categories = $conn->query("SELECT * FROM categories ORDER BY id");

// Lấy danh sách món ăn
$dishes = $conn->query("SELECT d.*, c.name as category_name FROM dishes d 
                        LEFT JOIN categories c ON d.category_id = c.id 
                        WHERE d.status = 'available' 
                        ORDER BY d.category_id, d.id");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thực Đơn - QL Nhà Hàng</title>
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
                <li><a href="menu.php" class="active">Thực đơn</a></li>
                <li><a href="about.php">Giới thiệu</a></li>
                <li><a href="reservation.php">Đặt bàn</a></li>
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
            <h1>Thực Đơn</h1>
            <p>Khám phá các món ăn ngon tại nhà hàng</p>
        </div>
    </div>

    <section class="menu-section">
        <div class="container">
            <?php
            $current_category = '';
            while ($dish = $dishes->fetch_assoc()) {
                if ($current_category != $dish['category_name']) {
                    if ($current_category != '') {
                        echo '</div>'; // Đóng dishes-grid
                    }
                    $current_category = $dish['category_name'];
                    echo '<h2 class="category-title">' . htmlspecialchars($current_category) . '</h2>';
                    echo '<div class="dishes-grid">';
                }
                ?>
                <div class="dish-card">
                    <div class="dish-image">
                        <?php if ($dish['image'] && file_exists($dish['image'])): ?>
                            <img src="<?php echo htmlspecialchars($dish['image']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="dish-image-placeholder">🍽️</div>
                        <?php endif; ?>
                    </div>
                    <div class="dish-info">
                        <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                        <p class="dish-description"><?php echo htmlspecialchars($dish['description']); ?></p>
                        <p class="dish-price"><?php echo formatCurrency($dish['price']); ?></p>
                    </div>
                </div>
                <?php
            }
            if ($current_category != '') {
                echo '</div>'; // Đóng dishes-grid cuối cùng
            }
            ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Liên Hệ</h3>
                    <p>📍 123 Đường ABC, Quận 1, TP.HCM</p>
                    <p>📞 (028) 1234 5678</p>
                </div>
                <div class="footer-section">
                    <h3>Giờ Mở Cửa</h3>
                    <p>Thứ 2 - Thứ 6: 10:00 - 22:00</p>
                    <p>Thứ 7 - Chủ nhật: 09:00 - 23:00</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
