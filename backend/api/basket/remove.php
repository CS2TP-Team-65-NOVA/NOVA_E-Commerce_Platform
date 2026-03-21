<?php
// basket/remove.php
// Remove a basket item completely

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

if ($basketItemId <= 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'basket_item_id must be provided and > 0.'
    ]);
    exit;
}

try {
    removeBasketItem($pdo, $basketItemId);

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
