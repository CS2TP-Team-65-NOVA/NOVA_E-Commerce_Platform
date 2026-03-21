<?php
// basket/view.php
// Return the current visitor's basket contents

require_once __DIR__ . '/basketService.php';

header('Content-Type: application/json');

try {
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
