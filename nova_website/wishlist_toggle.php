<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'not_logged_in']);
    exit;
}

$userId    = (int) $_SESSION['user_id'];
$productId = (int) ($_POST['product_id'] ?? 0);

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'invalid']);
    exit;
}

$check = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $userId, $productId);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if ($exists) {
    $del = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $userId, $productId);
    $del->execute();
    $del->close();
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    $ins = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $ins->bind_param("ii", $userId, $productId);
    $ins->execute();
    $ins->close();
    echo json_encode(['success' => true, 'action' => 'added']);
}