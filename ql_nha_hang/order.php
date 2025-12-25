<?php
require_once 'config.php';
$conn = getDBConnection();

// Lấy danh sách bàn
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");

// Xử lý chọn bàn
$selected_table = isset($_GET['table']) ? intval($_GET['table']) : 0;

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
    <title>Gọi Món - QL Nhà Hàng</title>
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
                <li><a href="order.php" class="active">Gọi món</a></li>
                <li><a href="admin/">Quản lý</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Gọi Món</h1>
            <p>Chọn món ăn và số lượng</p>
        </div>
    </div>

    <div class="container">
        <div class="order-container">
            <?php if ($selected_table == 0): ?>
            <div class="table-selection">
                <h2>Chọn Bàn</h2>
                <div class="tables-grid">
                    <?php while ($table = $tables->fetch_assoc()): ?>
                    <a href="?table=<?php echo $table['id']; ?>" class="table-card <?php echo $table['status']; ?>">
                        <div class="table-number">Bàn <?php echo $table['table_number']; ?></div>
                        <div class="table-capacity"><?php echo $table['capacity']; ?> người</div>
                        <div class="table-status">
                            <?php 
                            $status_text = [
                                'empty' => 'Trống',
                                'occupied' => 'Có khách',
                                'reserved' => 'Đã đặt'
                            ];
                            echo $status_text[$table['status']];
                            ?>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="order-page">
                <div class="menu-section">
                    <div class="order-header">
                        <?php
                        $table_info = $conn->query("SELECT * FROM tables WHERE id = $selected_table")->fetch_assoc();
                        ?>
                        <h2>Bàn <?php echo $table_info['table_number']; ?></h2>
                        <a href="order.php" class="btn btn-secondary btn-sm">Đổi bàn</a>
                    </div>

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
                                        onclick="addToCart(<?php echo $dish['id']; ?>, '<?php echo htmlspecialchars($dish['name']); ?>', <?php echo $dish['price']; ?>)">
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
                    <h3>Đơn Hàng</h3>
                    <div id="cart-items"></div>
                    <div class="cart-total">
                        <strong>Tổng cộng:</strong>
                        <strong id="cart-total">0 đ</strong>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="submitOrder()">Gửi Đơn Hàng</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let cart = [];
        const tableId = <?php echo $selected_table; ?>;

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
                cartItemsEl.innerHTML = '<p class="empty-cart">Chưa có món nào</p>';
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
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${formatCurrency(item.price)}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button onclick="updateQuantity(${item.id}, -1)" class="btn-quantity">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 1)" class="btn-quantity">+</button>
                        </div>
                    </div>
                `;
            });
            
            cartItemsEl.innerHTML = html;
            cartTotalEl.textContent = formatCurrency(total);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        function submitOrder() {
            if (cart.length === 0) {
                alert('Vui lòng chọn món trước khi gửi đơn hàng');
                return;
            }
            
            fetch('api/create_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    table_id: tableId,
                    items: cart
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đơn hàng đã được gửi thành công!');
                    cart = [];
                    updateCartDisplay();
                    window.location.href = 'order.php';
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                alert('Có lỗi xảy ra khi gửi đơn hàng');
                console.error(error);
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
