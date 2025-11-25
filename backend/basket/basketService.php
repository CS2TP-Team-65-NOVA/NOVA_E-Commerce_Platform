<?php
// basket/basketService.php
// Core basket logic for NOVA (Person 2: Malaika)

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/session.php';

/**
 * Get or create the active basket for the current visitor.
 * Works for both logged-in users and guests.
 */
function getActiveBasket(PDO $pdo): array
{
    $userId    = currentUserId();
    $sessionId = $_SESSION['guest_session_id'];

    if ($userId) {
        // Try find active basket for this user
        $stmt = $pdo->prepare("
            SELECT * FROM basket 
            WHERE user_id = ? AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $basket = $stmt->fetch();
    } else {
        // Guest basket (based on session_id)
        $stmt = $pdo->prepare("
            SELECT * FROM basket 
            WHERE session_id = ? AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$sessionId]);
        $basket = $stmt->fetch();
    }

    if ($basket) {
        return $basket;
    }

    // No basket found → create new one
    $stmt = $pdo->prepare("
        INSERT INTO basket (user_id, session_id, status, created_at, updated_at)
        VALUES (?, ?, 'active', NOW(), NOW())
    ");
    $stmt->execute([$userId, $sessionId]);

    $basketId = (int)$pdo->lastInsertId();

    return [
        'basket_id' => $basketId,
        'user_id'   => $userId,
        'session_id'=> $sessionId,
        'status'    => 'active',
    ];
}

/**
 * Add an item to the basket (or increase quantity if it already exists).
 *
 * @throws Exception if size_id is invalid or quantity <= 0
 */
function addToBasket(PDO $pdo, int $sizeId, int $qty): void
{
    if ($qty <= 0) {
        throw new Exception("Quantity must be greater than zero.");
    }

    $basket = getActiveBasket($pdo);
    $basketId = (int)$basket['basket_id'];

    // Get current price for this size from product_versions (Fuad's table)
    $stmt = $pdo->prepare("SELECT price FROM product_versions WHERE size_id = ?");
    $stmt->execute([$sizeId]);
    $pv = $stmt->fetch();

    if (!$pv) {
        throw new Exception("Invalid size_id: product variant not found.");
    }

    $unitPrice = (float)$pv['price'];

    // Check if item already exists in this basket
    $stmt = $pdo->prepare("
        SELECT basket_item_id, quantity 
        FROM basket_items
        WHERE basket_id = ? AND size_id = ?
        LIMIT 1
    ");
    $stmt->execute([$basketId, $sizeId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $newQty    = (int)$existing['quantity'] + $qty;
        $lineTotal = $unitPrice * $newQty;

        $stmt = $pdo->prepare("
            UPDATE basket_items
            SET quantity = ?, line_total = ?, updated_at = NOW()
            WHERE basket_item_id = ?
        ");
        $stmt->execute([$newQty, $lineTotal, $existing['basket_item_id']]);
    } else {
        $lineTotal = $unitPrice * $qty;

        $stmt = $pdo->prepare("
            INSERT INTO basket_items (basket_id, size_id, quantity, unit_price, line_total, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$basketId, $sizeId, $qty, $unitPrice, $lineTotal]);
    }
}

/**
 * Update the quantity for a specific basket item.
 * If new quantity is 0, the item is removed.
 */
function updateBasketItemQty(PDO $pdo, int $basketItemId, int $newQty): void
{
    if ($newQty < 0) {
        throw new Exception("Quantity cannot be negative.");
    }

    // Fetch current item (need unit_price & size for recalculation)
    $stmt = $pdo->prepare("
        SELECT unit_price 
        FROM basket_items 
        WHERE basket_item_id = ?
        LIMIT 1
    ");
    $stmt->execute([$basketItemId]);
    $item = $stmt->fetch();

    if (!$item) {
        throw new Exception("Basket item not found.");
    }

    if ($newQty === 0) {
        // Remove item entirely
        $stmt = $pdo->prepare("DELETE FROM basket_items WHERE basket_item_id = ?");
        $stmt->execute([$basketItemId]);
        return;
    }

    $unitPrice = (float)$item['unit_price'];
    $lineTotal = $unitPrice * $newQty;

    $stmt = $pdo->prepare("
        UPDATE basket_items
        SET quantity = ?, line_total = ?, updated_at = NOW()
        WHERE basket_item_id = ?
    ");
    $stmt->execute([$newQty, $lineTotal, $basketItemId]);
}

/**
 * Remove a basket item completely.
 */
function removeBasketItem(PDO $pdo, int $basketItemId): void
{
    $stmt = $pdo->prepare("DELETE FROM basket_items WHERE basket_item_id = ?");
    $stmt->execute([$basketItemId]);
}

/**
 * Get full basket details for current visitor:
 * - basket info
 * - items (with product + size info)
 * - totals
 */
function getBasketDetails(PDO $pdo): array
{
    $basket   = getActiveBasket($pdo);
    $basketId = (int)$basket['basket_id'];

    // Join with product_versions + products so frontend can display nicely
    $stmt = $pdo->prepare("
        SELECT 
            bi.basket_item_id,
            bi.quantity,
            bi.unit_price,
            bi.line_total,
            pv.size_ml,
            p.product_id,
            p.name AS product_name
        FROM basket_items bi
        JOIN product_versions pv ON bi.size_id = pv.size_id
        JOIN products p ON pv.product_id = p.product_id
        WHERE bi.basket_id = ?
        ORDER BY bi.created_at ASC
    ");
    $stmt->execute([$basketId]);
    $items = $stmt->fetchAll();

    $total      = 0.0;
    $itemCount  = 0;

    foreach ($items as $item) {
        $total     += (float)$item['line_total'];
        $itemCount += (int)$item['quantity'];
    }

    return [
        'basket'     => $basket,
        'items'      => $items,
        'total'      => $total,
        'item_count' => $itemCount,
    ];
}
