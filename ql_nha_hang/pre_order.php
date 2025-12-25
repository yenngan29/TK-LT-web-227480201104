<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Yêu cầu đăng nhập
requireLogin();

$conn = getDBConnection();
$user = getCurrentUser();
$user_id = $user['id'];

// Lấy reservation_id từ URL
$reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;

if ($reservation_id == 0) {
    die("<div style='padding: 50px; text-align: center;'><h1>❌ Lỗi</h1><p>Vui lòng chọn lượt đặt bàn trước</p><a href='customer_dashboard.php'>Quay lại Dashboard</a></div>");
}

// Kiểm tra reservation có thuộc về user không
$reservation = $conn->query("
    SELECT r.*, t.table_number 
    FROM reservations r
    LEFT JOIN tables t ON r.table_id = t.id
    WHERE r.id = $reservation_id AND r.user_id = $user_id
")->fetch_assoc();

if (!$reservation) {
    die("<div style='padding: 50px; text-align: center;'><h1>❌ Không tìm thấy</h1><p>Lượt đặt bàn không tồn tại</p><a href='customer_dashboard.php'>Quay lại Dashboard</a></div>");
}

// Kiểm tra đã có đơn hàng chưa và lấy chi tiết
$existing_order = $conn->query("
    SELECT id FROM orders 
    WHERE user_id = $user_id 
    AND table_id = {$reservation['table_id']}
    AND DATE(order_date) = '{$reservation['reservation_date']}'
    LIMIT 1
")->fetch_assoc();

// Lấy các món đã đặt trước đó (nếu có)
$existing_items = [];
if ($existing_order) {
    $items_result = $conn->query("
        SELECT oi.*, d.name as dish_name
        FROM order_items oi
        JOIN dishes d ON oi.dish_id = d.id
        WHERE oi.order_id = {$existing_order['id']}
    ");
    
    while ($item = $items_result->fetch_assoc()) {
        $existing_items[] = [
            'id' => $item['dish_id'],
            'name' => $item['dish_name'],
            'price' => $item['price'],
            'quantity' => $item['quantity']
        ];
    }
}

// Lấy danh sách món ăn theo danh mục
$categories = $conn->query("SELECT * FROM categories ORDER BY id");
$dishes_by_category = [];
$dishes_result = $conn->query("SELECT * FROM dishes WHERE status = 'available' ORDER BY category_id, name");
while ($dish = $dishes_result->fetch_assoc()) {
    $dishes_by_category[$dish['category_id']][] = $dish;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Món Trước - QL Nhà Hàng</title>
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
                <li><a href="reservation.php">Đặt bàn</a></li>
                <li><a href="customer_dashboard.php">👤 <?php echo htmlspecialchars($user['full_name']); ?></a></li>
                <li><a href="logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>🍽️ Đặt Món Trước</h1>
            <p>Chọn món cho lượt đặt bàn của bạn</p>
        </div>
    </div>

    <div class="container">
        <div class="alert alert-success">
            <h3>📅 Thông Tin Đặt Bàn:</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 10px;">
                <div><strong>Ngày:</strong> <?php echo formatDate($reservation['reservation_date']); ?></div>
                <div><strong>Giờ:</strong> <?php echo date('H:i', strtotime($reservation['reservation_time'])); ?></div>
                <div><strong>Số khách:</strong> <?php echo $reservation['number_of_guests']; ?> người</div>
                <div><strong>Bàn:</strong> <?php echo $reservation['table_number']; ?></div>
            </div>
        </div>

        <?php if ($existing_order): ?>
        <div class="alert alert-info">
            <h3>ℹ️ Đang chỉnh sửa đơn hàng</h3>
            <p>Các món đã chọn trước đó đã được tải vào giỏ hàng.</p>
            <p><strong>Bạn có thể:</strong> Thêm món mới, xóa món, hoặc thay đổi số lượng</p>
        </div>
        <?php endif; ?>

        <div class="order-page">
            <div class="menu-section">
                <h2>📖 Chọn Món</h2>

                <div class="categories-tabs">
                    <?php
                    $categories->data_seek(0);
                    $first = true;
                    while ($cat = $categories->fetch_assoc()): 
                    ?>
                    <button class="category-tab <?php echo $first ? 'active' : ''; ?>" 
                            onclick="showCategory(<?php echo $cat['id']; ?>)">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                    <?php 
                    $first = false;
                    endwhile; 
                    ?>
                </div>

                <?php
                $categories->data_seek(0);
                $first = true;
                while ($cat = $categories->fetch_assoc()): 
                    if (isset($dishes_by_category[$cat['id']])):
                ?>
                <div class="category-content" id="category-<?php echo $cat['id']; ?>" 
                     style="display: <?php echo $first ? 'block' : 'none'; ?>;">
                    <div class="dishes-list">
                        <?php foreach ($dishes_by_category[$cat['id']] as $dish): ?>
                        <div class="dish-item">
                            <?php if ($dish['image'] && file_exists($dish['image'])): ?>
                            <div class="dish-item-image">
                                <img src="<?php echo htmlspecialchars($dish['image']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="dish-item-info">
                                <h4><?php echo htmlspecialchars($dish['name']); ?></h4>
                                <p><?php echo htmlspecialchars($dish['description']); ?></p>
                                <span class="price"><?php echo formatCurrency($dish['price']); ?></span>
                            </div>
                            <button class="btn btn-primary btn-sm" 
                                    onclick="addToCart(<?php echo $dish['id']; ?>, '<?php echo addslashes(htmlspecialchars($dish['name'])); ?>', <?php echo $dish['price']; ?>)">
                                Thêm
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php 
                    $first = false;
                    endif;
                endwhile; 
                ?>
            </div>

            <div class="cart-section">
                <h3>🛒 Món Đã Chọn</h3>
                <div id="cart-items"></div>
                <div class="cart-total">
                    <strong>Tổng cộng:</strong>
                    <strong id="cart-total">0 đ</strong>
                </div>
                <button class="btn btn-primary btn-block" onclick="submitPreOrder()">
                    ✅ Xác Nhận Đặt Món
                </button>
                <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
                    Món sẽ được chuẩn bị sẵn khi bạn đến nhà hàng
                </p>
                <a href="customer_dashboard.php" class="btn btn-secondary btn-block" style="margin-top: 10px;">
                    Quay lại Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Tải món đã đặt trước (nếu có)
        let cart = <?php echo json_encode($existing_items); ?>;
        const reservationId = <?php echo $reservation_id; ?>;
        const tableId = <?php echo $reservation['table_id']; ?>;
        
        // Hiển thị giỏ hàng ngay khi load trang
        window.addEventListener('DOMContentLoaded', function() {
            updateCartDisplay();
        });

        function showCategory(categoryId) {
            document.querySelectorAll('.category-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.category-tab').forEach(el => el.classList.remove('active'));
            
            document.getElementById('category-' + categoryId).style.display = 'block';
            event.target.classList.add('active');
        }

        function addToCart(dishId, dishName, price) {
            const existingItem = cart.find(item => item.id === dishId);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: dishId,
                    name: dishName,
                    price: price,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
        }

        function updateQuantity(dishId, change) {
            const item = cart.find(item => item.id === dishId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    cart = cart.filter(item => item.id !== dishId);
                }
                updateCartDisplay();
            }
        }

        function updateCartDisplay() {
            const cartItemsEl = document.getElementById('cart-items');
            const cartTotalEl = document.getElementById('cart-total');
            
            if (cart.length === 0) {
                cartItemsEl.innerHTML = '<p class="empty-cart">Chưa chọn món nào</p>';
                cartTotalEl.textContent = '0 đ';
                return;
            }
            
            let html = '';
            let total = 0;
            
            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                
                html += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${escapeHtml(item.name)}</div>
                            <div class="cart-item-price">${formatCurrency(item.price)}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button onclick="updateQuantity(${item.id}, -1); event.stopPropagation();" class="btn-quantity">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 1); event.stopPropagation();" class="btn-quantity">+</button>
                        </div>
                    </div>
                `;
            });
            
            cartItemsEl.innerHTML = html;
            cartTotalEl.textContent = formatCurrency(total);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        function submitPreOrder() {
            if (cart.length === 0) {
                alert('Vui lòng chọn ít nhất một món');
                return;
            }
            
            if (!confirm('Xác nhận đặt món trước?\n\nMón sẽ được chuẩn bị khi bạn đến nhà hàng.')) {
                return;
            }

            console.log('Sending data:', {
                reservation_id: reservationId,
                table_id: tableId,
                items: cart
            });

            fetch('api/create_pre_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    reservation_id: reservationId,
                    table_id: tableId,
                    items: cart
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server trả về không phải JSON: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Parsed data:', data);
                if (data.success) {
                    alert('✅ Đặt món thành công!\n\nKhi bạn đến nhà hàng, món sẽ được chuẩn bị sẵn.');
                    window.location.href = 'customer_dashboard.php';
                } else {
                    alert('❌ Lỗi: ' + (data.message || 'Không rõ lý do'));
                }
            })
            .catch(error => {
                alert('❌ Lỗi khi gửi đơn:\n\n' + error.message + '\n\nMở Console (F12) để xem chi tiết');
                console.error('Error details:', error);
            });
        }
    </script>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

