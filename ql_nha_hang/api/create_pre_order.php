<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // Không hiển thị error trực tiếp
ini_set('log_errors', 1);

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }

    $conn = getDBConnection();
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reservation_id']) || !isset($data['table_id']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$reservation_id = intval($data['reservation_id']);
$table_id = intval($data['table_id']);
$items = $data['items'];

// Kiểm tra reservation có thuộc về user không
$reservation = $conn->query("SELECT * FROM reservations WHERE id = $reservation_id AND user_id = $user_id")->fetch_assoc();
if (!$reservation) {
    echo json_encode(['success' => false, 'message' => 'Lượt đặt bàn không hợp lệ']);
    exit;
}

// Tính tổng tiền
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Kiểm tra đã có đơn hàng trước đó chưa
$existing_order = $conn->query("
    SELECT id FROM orders 
    WHERE user_id = $user_id 
    AND table_id = $table_id
    AND DATE(order_date) = '{$reservation['reservation_date']}'
    LIMIT 1
")->fetch_assoc();

if ($existing_order) {
    // Xóa đơn cũ và tạo mới
    $conn->query("DELETE FROM orders WHERE id = {$existing_order['id']}");
}

// Tạo đơn hàng mới (status = pending, sẽ được chuẩn bị khi khách đến)
$order_date = $reservation['reservation_date'] . ' ' . $reservation['reservation_time'];
$sql = "INSERT INTO orders (user_id, table_id, order_date, total_amount, status) 
        VALUES ($user_id, $table_id, '$order_date', $total, 'pending')";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    // Tính tổng số lượng món và số loại món
    $total_quantity = 0;
    $dish_count = count($items);
    foreach ($items as $item) {
        $total_quantity += intval($item['quantity']);
    }
    
    // Thêm chi tiết đơn hàng
    foreach ($items as $item) {
        $dish_id = intval($item['id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);
        
        $conn->query("INSERT INTO order_items (order_id, dish_id, quantity, price) 
                      VALUES ($order_id, $dish_id, $quantity, $price)");
    }
    
    // Cập nhật ghi chú cho reservation với số lượng món chính xác
    $note = "Đã đặt món trước: $dish_count loại món, tổng $total_quantity phần, " . formatCurrency($total);
    $note_escaped = $conn->real_escape_string($note);
    $conn->query("UPDATE reservations SET notes = CONCAT(COALESCE(notes, ''), '\n', '$note_escaped') WHERE id = $reservation_id");
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể tạo đơn hàng: ' . $conn->error]);
}

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}

// formatCurrency() đã có trong config.php, không cần khai báo lại
?>

