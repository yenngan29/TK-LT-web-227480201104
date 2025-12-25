<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

$conn = getDBConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['table_id']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$table_id = intval($data['table_id']);
$items = $data['items'];
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// Tính tổng tiền
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Tạo đơn hàng (có lưu user_id nếu đã đăng nhập)
$sql = "INSERT INTO orders (user_id, table_id, total_amount, status) VALUES (" . ($user_id ? $user_id : "NULL") . ", $table_id, $total, 'pending')";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    // Thêm chi tiết đơn hàng
    foreach ($items as $item) {
        $dish_id = intval($item['id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);
        
        $conn->query("INSERT INTO order_items (order_id, dish_id, quantity, price) 
                      VALUES ($order_id, $dish_id, $quantity, $price)");
    }
    
    // Cập nhật trạng thái bàn
    $conn->query("UPDATE tables SET status = 'occupied' WHERE id = $table_id");
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể tạo đơn hàng']);
}
?>

