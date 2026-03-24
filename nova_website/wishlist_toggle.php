<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Not logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'not_logged_in'
    ]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'invalid_product'
    ]);
    exit;
}

// Check if already exists
$stmt = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // REMOVE
    $stmt->close();

    $delete = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $delete->bind_param("ii", $userId, $productId);
    $delete->execute();
    $delete->close();

    echo json_encode([
        'success' => true,
        'action' => 'removed'
    ]);
    exit;
}

$stmt->close();

// ADD
$insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
$insert->bind_param("ii", $userId, $productId);
$insert->execute();
$insert->close();

echo json_encode([
    'success' => true,
    'action' => 'added'
]);