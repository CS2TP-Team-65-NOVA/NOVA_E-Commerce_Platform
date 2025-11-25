<?php
// basket/add.php
// Add an item to the current visitor's basket

require_once __DIR__ . '/basketService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method. Use POST.'
    ]);
    exit;
}

$sizeId = isset($_POST['size_id']) ? (int)$_POST['size_id'] : 0;
$qty    = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($sizeId <= 0 || $qty <= 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'size_id and quantity must be provided and greater than zero.'
    ]);
    exit;
}

try {
    addToBasket($pdo, $sizeId, $qty);

    // Return updated basket summary
    $basketData = getBasketDetails($pdo);

    echo json_encode([
        'success' => true,
        'basket'  => $basketData
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}

