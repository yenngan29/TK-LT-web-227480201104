<?php
require_once '../config.php';
header('Content-Type: application/json');

$conn = getDBConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$order_id = intval($data['order_id']);
$status = $conn->real_escape_string($data['status']);

$sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";

if ($conn->query($sql)) {
    // Cập nhật trạng thái các món ăn
    if ($status == 'preparing') {
        $conn->query("UPDATE order_items SET status = 'preparing' WHERE order_id = $order_id");
    } elseif ($status == 'completed') {
        $conn->query("UPDATE order_items SET status = 'ready' WHERE order_id = $order_id");
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật']);
}
?>





