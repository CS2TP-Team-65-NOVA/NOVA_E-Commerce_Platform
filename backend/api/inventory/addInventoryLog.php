
<?php
require_once __DIR__ . '/../../config/db.php';

$size_id = $_POST['size_id'];
$change_type = $_POST['change_type'];
$quantity_changed = $_POST['quantity_changed'];
$quantity_before = $_POST['quantity_before'];
$quantity_after = $_POST['quantity_after'];
$reference_type = $_POST['reference_type'];
$reference_id = $_POST['reference_id'];

$sql = "INSERT INTO inventory_logs (size_id, change_type, quantity_changed, quantity_before, quantity_after, reference_type, reference_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isiiisi", $size_id, $change_type, $quantity_changed, $quantity_before, $quantity_after, $reference_type, $reference_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Log added']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Log not added']);
}
?>
