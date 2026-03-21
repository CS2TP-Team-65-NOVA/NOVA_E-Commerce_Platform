<?php
// basket/update.php
// Update the quantity of a basket item

require_once __DIR__ . '/basketService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method. Use POST.'
    ]);
    exit;
}

$basketItemId = isset($_POST['basket_item_id']) ? (int)$_POST['basket_item_id'] : 0;
$newQty       = isset($_POST['quantity']) ? (int)$_POST['quantity'] : -1;

if ($basketItemId <= 0 || $newQty < 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'basket_item_id must be > 0 and quantity must be >= 0.'
    ]);
    exit;
}

try {
    // If quantity = 0, this will delete the item
    updateBasketItemQty($pdo, $basketItemId, $newQty);

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
