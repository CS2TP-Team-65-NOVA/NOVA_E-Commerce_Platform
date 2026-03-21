<?php
require_once __DIR__ . '/../../config/db.php';

$sql = "SELECT * FROM inventory_logs ORDER BY created_at DESC";
$result = $conn->query($sql);

$logs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($logs);
?>

