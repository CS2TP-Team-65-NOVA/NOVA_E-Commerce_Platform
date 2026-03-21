<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. Handle "add to cart" via GET parameters

if (isset($_GET['product_id'])) {
    $productId = (int) $_GET['product_id'];
    $sizeId    = isset($_GET['size_id']) ? (int) $_GET['size_id'] : 0;

    if ($productId > 0) {
        if ($sizeId <= 0) {
            $stmt = $conn->prepare(
                'SELECT size_id 
                 FROM product_versions 
                 WHERE product_id = ? 
                 ORDER BY size_ml ASC 
                 LIMIT 1'
            );
            if ($stmt) {
                $stmt->bind_param('i', $productId);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $sizeId = (int) $row['size_id'];
                    }
                }
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare(
                'SELECT size_id 
                 FROM product_versions 
                 WHERE product_id = ? AND size_id = ? 
                 LIMIT 1'
            );
            if ($stmt) {
                $stmt->bind_param('ii', $productId, $sizeId);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if (!$result->fetch_assoc()) {
                        $sizeId = 0;
                    }
                }
                $stmt->close();
            }
        }

        $itemKey = $productId . ':' . $sizeId;

        if (!isset($_SESSION['cart'][$itemKey])) {
            $_SESSION['cart'][$itemKey] = [
                'product_id' => $productId,
                'size_id'    => $sizeId,
                'qty'        => 1,
            ];
        } else {
            $_SESSION['cart'][$itemKey]['qty']++;
        }
    }

    header('Location: shopping_cart.php');
    exit;
}

// 2. Update and Removal of Product

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['remove_key'])) {
        $removeKey = $_POST['remove_key'];
        if (isset($_SESSION['cart'][$removeKey])) {
            unset($_SESSION['cart'][$removeKey]);
        }
    }

    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $key => $qty) {
            if (!isset($_SESSION['cart'][$key])) {
                continue;
            }

            $qty = (int) $qty;

            if ($qty <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['qty'] = $qty;
            }
        }
    }

    header('Location: shopping_cart.php');
    exit;
}

// 3. Data Diplaying in the Shopping cart

$basketItems = [];
$subtotal    = 0.0;

if (!empty($_SESSION['cart'])) {
    $productIds = [];
    $sizeIds    = [];

    foreach ($_SESSION['cart'] as $line) {
        $productIds[] = (int) $line['product_id'];
        if (!empty($line['size_id'])) {
            $sizeIds[] = (int) $line['size_id'];
        }
    }

    $productIds = array_values(array_unique($productIds));
    $sizeIds    = array_values(array_unique($sizeIds));

    $productsById = [];
    if (!empty($productIds)) {
        $idList = implode(',', array_map('intval', $productIds));
        $sql    = "SELECT product_id, name, image, price 
                   FROM products 
                   WHERE product_id IN ($idList)";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $productsById[(int) $row['product_id']] = $row;
            }
        }
    }

    $sizesById = [];
    if (!empty($sizeIds)) {
        $idList = implode(',', array_map('intval', $sizeIds));
        $sql    = "SELECT size_id, product_id, size_ml, price 
                   FROM product_versions 
                   WHERE size_id IN ($idList)";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $sizesById[(int) $row['size_id']] = $row;
            }
        }
    }

    foreach ($_SESSION['cart'] as $key => $line) {
        $productId = (int) $line['product_id'];
        $sizeId    = (int) $line['size_id'];
        $qty       = max(1, (int) $line['qty']); 

        if (!isset($productsById[$productId])) {
            continue;
        }

        $productRow = $productsById[$productId];
        $variantRow = (!empty($sizeId) && isset($sizesById[$sizeId]))
            ? $sizesById[$sizeId]
            : null;

        $name       = $productRow['name'];
        $image      = !empty($productRow['image']) ? $productRow['image'] : 'placeholder.jpg';
        $sizeLabel  = $variantRow
            ? ((int) $variantRow['size_ml'] . ' ml')
            : 'Standard size';
        $unitPrice  = $variantRow
            ? (float) $variantRow['price']
            : (float) $productRow['price'];

        $lineTotal  = $unitPrice * $qty;
        $subtotal  += $lineTotal;

        $basketItems[] = [
            'key'        => $key,
            'product_id' => $productId,
            'size_id'    => $sizeId,
            'name'       => $name,
            'image'      => $image,
            'size_label' => $sizeLabel,
            'qty'        => $qty,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVA – Basket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="basket-page">
    <h1 class="basket-title">Your basket</h1>

    <div class="basket-layout">
        
         <!--Summary-Left Side -->
        <div class="basket-products-box">
            <?php if (empty($basketItems)): ?>
                <p class="basket-empty-message">
                    Your basket is currently empty.
                </p>
                <div class="basket-empty-actions">
                    <a href="perfumes.php" class="btn-primary">Browse perfumes</a>
                </div>
            <?php else: ?>
                <form method="post">
                    <?php foreach ($basketItems as $item): ?>
                        <div class="basket-item">
                            <div>
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div>
                                <div class="basket-item-title">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </div>
                                <div class="basket-item-size">
                                    <?php echo htmlspecialchars($item['size_label']); ?>
                                </div>
                                <div class="basket-item-price-row">
                                    <span>
                                        Unit price:
                                        <strong>
                                            £<?php echo number_format($item['unit_price'], 2); ?>
                                        </strong>
                                    </span>
                                </div>
                                <div class="basket-item-qty-row">
                                    <span>Qty:</span>
                                    <input
                                        type="number"
                                        name="qty[<?php echo htmlspecialchars($item['key']); ?>]"
                                        value="<?php echo (int) $item['qty']; ?>"
                                        min="0"
                                    >
                                    <button
                                        type="submit"
                                        name="remove_key"
                                        value="<?php echo htmlspecialchars($item['key']); ?>"
                                        class="remove-btn"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <div class="basket-item-total">
                                £<?php echo number_format($item['line_total'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="update-row">
                        <button type="submit" class="btn-secondary">
                            Update basket
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Summary-Right Side -->
        <div class="basket-summary-box">
            <h2>Summary</h2>

            <?php if (empty($basketItems)): ?>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>£0.00</span>
                </div>
                <a href="checkout.php"
                   class="btn-primary"
                   aria-disabled="true"
                   style="pointer-events: none; opacity: 0.6;">
                    Checkout
                </a>
            <?php else: ?>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>£<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery</span>
                    <span>£0.00</span>
                </div>
                <div class="summary-row summary-row-total">
                    <strong>Total</strong>
                    <strong>£<?php echo number_format($subtotal, 2); ?></strong>
                </div>

                <a href="checkout.php" class="btn-primary">
                    Checkout
                </a>
                <a href="perfumes.php" class="btn-primary">
                    Continue Shopping
                </a>
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>
