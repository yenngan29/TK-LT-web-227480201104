<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

$conn = getDBConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['item_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$item_id = intval($data['item_id']);
$status = $conn->real_escape_string($data['status']);

// Cập nhật trạng thái món
$sql = "UPDATE order_items SET status = '$status' WHERE id = $item_id";

if ($conn->query($sql)) {
    // Kiểm tra tất cả món trong đơn hàng
    $item_info = $conn->query("SELECT order_id FROM order_items WHERE id = $item_id")->fetch_assoc();
    $order_id = $item_info['order_id'];
    
    // Kiểm tra tất cả món đã xong chưa
    $all_items = $conn->query("SELECT status FROM order_items WHERE order_id = $order_id");
    $all_ready = true;
    
    while ($it = $all_items->fetch_assoc()) {
        if (!in_array($it['status'], ['ready', 'served'])) {
            $all_ready = false;
            break;
        }
    }
    
    // Nếu tất cả món đã xong → Cập nhật trạng thái đơn hàng
    if ($all_ready) {
        $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id");
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật']);
}
?>



