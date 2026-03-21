<?php
require_once __DIR__ . '/../../config/db.php';

$size_id = $_POST['size_id'];
$new_qty = $_POST['stock_qty'];

$sql = "UPDATE inventory SET stock_qty = ?, updated_at = NOW() WHERE size_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $new_qty, $size_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Stock updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No rows updated']);
}
?>


