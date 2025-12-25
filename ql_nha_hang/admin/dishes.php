<?php
session_start();
require_once '../config.php';
require_once '../auth.php';

// Yêu cầu đăng nhập admin
requireAdmin();

$conn = getDBConnection();

$success = '';
$error = '';

// Xử lý upload ảnh
function uploadImage($file) {
    $upload_dir = '../assets/images/dishes/';
    
    // Tạo thư mục nếu chưa có
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception("Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)");
    }
    
    if ($file['size'] > 5000000) { // 5MB
        throw new Exception("File ảnh quá lớn (tối đa 5MB)");
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'assets/images/dishes/' . $filename;
    } else {
        throw new Exception("Không thể upload ảnh");
    }
}

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // THÊM MÓN MỚI
        if (isset($_POST['add'])) {
            $name = $conn->real_escape_string($_POST['name']);
            $description = $conn->real_escape_string($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            $image = '';
            
            // Upload ảnh nếu có
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = uploadImage($_FILES['image']);
            }
            
            $sql = "INSERT INTO dishes (name, description, price, category_id, image) VALUES ('$name', '$description', $price, $category_id, '$image')";
            if ($conn->query($sql)) {
                $success = "Thêm món ăn thành công!";
            }
        } 
        // CẬP NHẬT MÓN
        elseif (isset($_POST['update'])) {
            $id = intval($_POST['id']);
            $name = $conn->real_escape_string($_POST['name']);
            $description = $conn->real_escape_string($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            
            // Kiểm tra có upload ảnh mới không
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Xóa ảnh cũ nếu có
                $old_image = $conn->query("SELECT image FROM dishes WHERE id = $id")->fetch_assoc()['image'];
                if ($old_image && file_exists('../' . $old_image)) {
                    unlink('../' . $old_image);
                }
                
                $image = uploadImage($_FILES['image']);
                $sql = "UPDATE dishes SET name='$name', description='$description', price=$price, category_id=$category_id, image='$image' WHERE id=$id";
            } else {
                $sql = "UPDATE dishes SET name='$name', description='$description', price=$price, category_id=$category_id WHERE id=$id";
            }
            
            if ($conn->query($sql)) {
                $success = "Cập nhật món ăn thành công!";
            }
        }
        // XÓA MÓN
        elseif (isset($_POST['delete'])) {
            $id = intval($_POST['id']);
            
            // Xóa ảnh trước
            $dish = $conn->query("SELECT image FROM dishes WHERE id = $id")->fetch_assoc();
            if ($dish && $dish['image'] && file_exists('../' . $dish['image'])) {
                unlink('../' . $dish['image']);
            }
            
            $conn->query("DELETE FROM dishes WHERE id = $id");
            $success = "Xóa món ăn thành công!";
        }
        // BẬT/TẮT TRẠNG THÁI
        elseif (isset($_POST['toggle_status'])) {
            $id = intval($_POST['id']);
            $conn->query("UPDATE dishes SET status = IF(status = 'available', 'unavailable', 'available') WHERE id = $id");
            $success = "Đã cập nhật trạng thái!";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Lấy món đang edit
$edit_dish = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_dish = $conn->query("SELECT * FROM dishes WHERE id = $edit_id")->fetch_assoc();
}

// Lấy danh sách món ăn
$dishes = $conn->query("SELECT d.*, c.name as category_name FROM dishes d LEFT JOIN categories c ON d.category_id = c.id ORDER BY d.category_id, d.name");

// Lấy danh mục
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Món Ăn - QL Nhà Hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            border-radius: 8px;
            display: none;
        }
        .dish-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .form-image {
            margin: 15px 0;
        }
        .current-image {
            max-width: 150px;
            border-radius: 8px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ Quản Lý Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Tổng quan</a></li>
                <li><a href="dishes.php" class="active">Món ăn</a></li>
                <li><a href="tables.php">Bàn</a></li>
                <li><a href="reservations.php">Đặt bàn</a></li>
                <li><a href="orders.php">Đơn hàng</a></li>
                <li><a href="../index.php">Trang chủ</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Quản Lý Món Ăn</h1>
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
            <h2><?php echo $edit_dish ? 'Chỉnh Sửa Món Ăn' : 'Thêm Món Ăn Mới'; ?></h2>
            
            <?php if ($edit_dish): ?>
            <p><a href="dishes.php" class="btn btn-secondary btn-sm">← Hủy chỉnh sửa</a></p>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="form-inline">
                <?php if ($edit_dish): ?>
                <input type="hidden" name="id" value="<?php echo $edit_dish['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row" style="display: block;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <input type="text" name="name" placeholder="Tên món" required class="form-control" 
                               value="<?php echo $edit_dish ? htmlspecialchars($edit_dish['name']) : ''; ?>">
                        
                        <input type="number" name="price" placeholder="Giá (VNĐ)" step="1000" required class="form-control"
                               value="<?php echo $edit_dish ? $edit_dish['price'] : ''; ?>">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <input type="text" name="description" placeholder="Mô tả món ăn" class="form-control"
                               value="<?php echo $edit_dish ? htmlspecialchars($edit_dish['description']) : ''; ?>">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <select name="category_id" required class="form-control">
                            <option value="">Chọn danh mục</option>
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($edit_dish && $edit_dish['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                        
                        <div class="form-image">
                            <label for="image" style="cursor: pointer; padding: 0.8rem; border: 2px dashed #667eea; border-radius: 5px; display: block; text-align: center;">
                                📷 Chọn ảnh món ăn
                            </label>
                            <input type="file" name="image" id="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        </div>
                    </div>
                    
                    <?php if ($edit_dish && $edit_dish['image']): ?>
                    <div style="margin-bottom: 1rem;">
                        <strong>Ảnh hiện tại:</strong><br>
                        <img src="../<?php echo $edit_dish['image']; ?>" class="current-image">
                    </div>
                    <?php endif; ?>
                    
                    <img id="preview" class="image-preview">
                    
                    <button type="submit" name="<?php echo $edit_dish ? 'update' : 'add'; ?>" class="btn btn-primary">
                        <?php echo $edit_dish ? '💾 Cập nhật' : '➕ Thêm món'; ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-section">
            <h2>Danh Sách Món Ăn (<?php echo $dishes->num_rows; ?> món)</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tên món</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($dish = $dishes->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($dish['image'] && file_exists('../' . $dish['image'])): ?>
                                    <img src="../<?php echo $dish['image']; ?>" class="dish-image-small">
                                <?php else: ?>
                                    <div class="dish-image-small" style="background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 24px;">🍽️</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($dish['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($dish['description']); ?></td>
                            <td><strong><?php echo formatCurrency($dish['price']); ?></strong></td>
                            <td><?php echo htmlspecialchars($dish['category_name']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $dish['status']; ?>">
                                    <?php echo $dish['status'] == 'available' ? 'Có sẵn' : 'Hết'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $dish['id']; ?>" class="btn btn-sm btn-primary">✏️ Sửa</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">
                                    <button type="submit" name="toggle_status" class="btn btn-sm btn-secondary">
                                        <?php echo $dish['status'] == 'available' ? '🔒 Ẩn' : '✅ Hiện'; ?>
                                    </button>
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Xác nhận xóa món <?php echo htmlspecialchars($dish['name']); ?>?')">
                                        🗑️ Xóa
                                    </button>
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

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
