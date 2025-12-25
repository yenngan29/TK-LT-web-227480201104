<?php
// File xử lý đăng nhập/đăng ký
// Session phải được start trước khi include file này
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// Kiểm tra user đã đăng nhập chưa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kiểm tra user là admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Kiểm tra user là customer
function isCustomer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer';
}

// Lấy thông tin user hiện tại
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT id, email, full_name, phone, role FROM users WHERE id = $user_id");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Đăng nhập
function login($email, $password) {
    $conn = getDBConnection();
    $email = $conn->real_escape_string($email);
    
    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND status = 'active'");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra password
        if (password_verify($password, $user['password'])) {
            // Lưu session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Cập nhật last_login
            $conn->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
            
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'Mật khẩu không đúng'];
        }
    } else {
        return ['success' => false, 'message' => 'Email không tồn tại'];
    }
}

// Đăng ký
function register($email, $password, $full_name, $phone) {
    $conn = getDBConnection();
    $email = $conn->real_escape_string($email);
    $full_name = $conn->real_escape_string($full_name);
    $phone = $conn->real_escape_string($phone);
    
    // Kiểm tra email đã tồn tại
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        return ['success' => false, 'message' => 'Email đã được sử dụng'];
    }
    
    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (email, password, full_name, phone, role) VALUES ('$email', '$hashed', '$full_name', '$phone', 'customer')";
    
    if ($conn->query($sql)) {
        $user_id = $conn->insert_id;
        
        // Tự động đăng nhập
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_role'] = 'customer';
        
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Không thể tạo tài khoản'];
    }
}

// Đăng xuất
function logout() {
    session_destroy();
    redirect('index.php');
}

// Yêu cầu đăng nhập (dùng cho các trang protected)
function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect($redirect);
    }
}

// Yêu cầu admin
function requireAdmin() {
    requireLogin('admin_login.php');
    if (!isAdmin()) {
        die("<div style='padding: 50px; text-align: center;'><h1>⛔ Truy cập bị từ chối</h1><p>Bạn không có quyền truy cập trang này</p><a href='index.php'>Về trang chủ</a></div>");
    }
}
?>
