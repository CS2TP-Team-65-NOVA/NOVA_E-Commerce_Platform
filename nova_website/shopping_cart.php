<?php
session_start();
require_once 'config.php';

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

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <title>Basket</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<header id="main-header">
        <nav id="navbar">

            <!-- LEFT SIDE: Home, About, Perfumes -->
            <div class="nav-left">
                <a href="index.php" class="nav-link">Home</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="perfumes.php" class="nav-link">Perfumes</a>
            </div>

            <!-- CENTER: NOVA Logo -->
            <a href="index.php" class="logo-link">
                <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
            </a>

            <!-- RIGHT SIDE BASED ON USER SESSION -->
            <div class="nav-right">

            <?php if (!isset($_SESSION['user_id'])): ?>

                <!-- GUEST -->
                <a href="register.php" class="nav-link">Register</a>
                <a href="login.php" class="nav-link">Log in</a>

                <a href="shopping_cart.php" class="basket-link active" aria-label="Shopping basket">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
                </a>

            <?php else: ?>
                <?php $role = $_SESSION['role'] ?? 'customer'; ?>

                <?php if ($role === 'admin'): ?>

                    <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

                    <a href="admin_profile.php" class="account-link" aria-label="Admin account">
                        <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon" />
                        <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon" />
                    </a>

                    <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                        <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                        <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
                    </a>

                <?php else: ?>

                    <a href="customer_profile.php" class="account-link" aria-label="My account">
                        <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon" />
                        <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon" />
                    </a>

                    <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                        <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                        <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
                    </a>

                <?php endif; ?>
            <?php endif; ?>

            </div>

        </nav>
    </header>

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

<footer class="nova-footer">
    <div class="nova-footer-inner">

        <!-- TOP: 3 columns + payment / rating column -->
        <div class="footer-top-row">
            <!-- Help -->
            <div class="footer-col">
                <h4>Help</h4>
                <a href="contact.php">Contact Us</a>
                <a href="#" class="footer-link-highlight">Accessibility Statement</a>
                <a href="#">Delivery Information</a>
                <a href="#">Customer Service</a>
                <a href="#">Returns Policy</a>
                <a href="#">FAQs</a>
                <a href="#">Store Finder</a>
                <a href="#">The App</a>
                <a href="#">Complaints Policy</a>
            </div>

            <!-- About Us -->
            <div class="footer-col">
                <h4>About Us</h4>
                <a href="about.php">Our Story</a>
                <a href="#">Our Social Purpose</a>
                <a href="#">Careers</a>
                <a href="#">Student Discount</a>
                <a href="#">VIP Rewards</a>
                <a href="#">Charity Partners</a>
            </div>

            <!-- Legal -->
            <div class="footer-col">
                <h4>Legal</h4>
                <a href="#">Terms &amp; Conditions</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Customer Reviews Policy</a>
                <a href="#">Cookie Preferences</a>
                <a href="#">CNF or Portal Enquiries</a>
                <a href="#">Tax Strategy</a>
                <a href="#">Gender Pay Gap</a>
                <a href="#">Modern Slavery Statement</a>
                <a href="#">Corporate Governance</a>
            </div>

            <!-- Right side: payments + rating + app badges -->
            <div class="footer-col footer-col-right">
                <div class="footer-payments">
                    <!-- payment logos (swap src to your images) -->
                    <img src="master_card.png" alt="Mastercard">
                    <img src="Pay_pal.png" alt="PayPal">
                    <img src="apple_pay.png" alt="Apple Pay">
                    <img src="Klarna.png" alt="Klarna">
                </div>

                <div class="footer-rating-card">
                    <div class="rating-logo">TrustScore</div>
                    <div class="rating-stars">★★★★★</div>
                    <div class="rating-text">4.7 | 154,224 reviews</div>
                </div>

                <div class="footer-membership-logo">
                    <!-- membership / group logo -->
                    <span>Member of NOVA Group</span>
                </div>

                <div class="footer-app-badges">
                    <img src="app_store.png" alt="Download on App Store">
                    <img src="play_store.png" alt="Download on Google Play">
                </div>
            </div>
        </div>

        <!-- MIDDLE: social icons -->
        <div class="footer-middle-row">
            <div class="footer-social">
                <a href="" class="social-circle">f</a>
                <a href="#" class="social-circle">x</a>
                <a href="#" class="social-circle">▶</a>
                <a href="#" class="social-circle">in</a>
                <a href="#" class="social-circle">P</a>
            </div>
        </div>

        <!-- BOTTOM: small print -->
        <div class="footer-bottom-row">
            <p>Copyright © 2025 NOVA Fragrance Ltd</p>
            <p>NOVA Fragrance Ltd is registered in England &amp; Wales. This website is for educational use as part of a university project.</p>
        </div>

    </div>
</footer>
