<?php
session_start();
require_once 'config.php';

/* 1. Rebuild cart */

$cartItems = [];
$subtotal  = 0.0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $productIds = [];
    $sizeIds    = [];

    foreach ($_SESSION['cart'] as $item) {
        $productIds[] = (int)$item['product_id'];
        if (!empty($item['size_id'])) {
            $sizeIds[] = (int)$item['size_id'];
        }
    }

    $productIds = array_values(array_unique($productIds));
    $sizeIds    = array_values(array_unique($sizeIds));

    $productMap = [];
    if (!empty($productIds)) {
        $idList = implode(',', array_map('intval', $productIds));
        $sql = "SELECT product_id, name, image, price FROM products WHERE product_id IN ($idList)";
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $productMap[(int)$row['product_id']] = $row;
            }
        }
    }

    $sizeMap = [];
    if (!empty($sizeIds)) {
        $idList = implode(',', array_map('intval', $sizeIds));
        $sql = "SELECT size_id, product_id, size_ml, price FROM product_versions WHERE size_id IN ($idList)";
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $sizeMap[(int)$row['size_id']] = $row;
            }
        }
    }

    foreach ($_SESSION['cart'] as $key => $item) {
        $p = $productMap[$item['product_id']] ?? null;
        if (!$p) continue;

        $variant = null;
        if (!empty($item['size_id']) && isset($sizeMap[$item['size_id']])) {
            $variant = $sizeMap[$item['size_id']];
        }

        $name       = $p['name'];
        $image      = !empty($p['image']) ? $p['image'] : 'placeholder.jpg';
        $size_label = $variant ? ((int)$variant['size_ml'] . ' ml') : 'Standard size';
        $unitPrice  = $variant ? (float)$variant['price'] : (float)$p['price'];
        $qty        = (int)$item['qty'];
        $lineTotal  = $unitPrice * $qty;

        $subtotal += $lineTotal;

        $cartItems[] = [
            'key'        => $key,
            'product_id' => $item['product_id'],
            'size_id'    => $item['size_id'],
            'name'       => $name,
            'image'      => $image,
            'size_label' => $size_label,
            'unit_price' => $unitPrice,
            'qty'        => $qty,
            'line_total' => $lineTotal,
        ];
    }
}

$order_success = false;
$error_message = '';

/* 2. Handle order submission */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email']     ?? '');
    $address   = trim($_POST['address']   ?? '');
    $city      = trim($_POST['city']      ?? '');
    $postcode  = trim($_POST['postcode']  ?? '');
    $payment   = $_POST['payment_method'] ?? '';

    if (empty($cartItems)) {
        $error_message = 'Your basket is empty.';
    } elseif ($full_name === '' || $email === '' || $address === '' || $city === '' || $postcode === '' || $payment === '') {
        $error_message = 'Please fill in all required fields.';
    } else {

        // ---- INSERT INTO orders ----
        $userId          = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $orderNumber     = 'ORD-' . strtoupper(substr(uniqid(), -6));
        $shippingAddress = $address . ', ' . $city . ', ' . $postcode;

        $stmt = $conn->prepare("
            INSERT INTO orders
                (user_id, order_number, total_amount, shipping_address,
                 payment_status, delivery_status, currency)
            VALUES (?, ?, ?, ?, 'success', 'processing', 'GBP')
        ");

        if ($stmt) {
            $stmt->bind_param("isds", $userId, $orderNumber, $subtotal, $shippingAddress);

            if ($stmt->execute()) {
                $orderId = $conn->insert_id;
                $stmt->close();

                // ---- INSERT INTO order_items ----
                $itemStmt = $conn->prepare("
                    INSERT INTO order_items
                        (order_id, size_id, quantity, price, line_total)
                    VALUES (?, ?, ?, ?, ?)
                ");

                if ($itemStmt) {
                    foreach ($cartItems as $item) {
                        $sid       = !empty($item['size_id']) ? (int)$item['size_id'] : null;
                        $qty       = (int)$item['qty'];
                        $unitPrice = (float)$item['unit_price'];
                        $lineTotal = (float)$item['line_total'];

                        $itemStmt->bind_param("iiddd", $orderId, $sid, $qty, $unitPrice, $lineTotal);
                        $itemStmt->execute();
                    }
                    $itemStmt->close();
                }

                // ---- SUCCESS: clear cart ----
                $order_success    = true;
                $_SESSION['cart'] = [];
                $cartItems        = [];
                $subtotal         = 0.0;

            } else {
                $error_message = 'Could not place order. Please try again.';
                $stmt->close();
            }
        } else {
            $error_message = 'Database error. Please try again.';
        }
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
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<header id="main-header">
    <nav id="navbar">
        <div class="nav-left">
            <a href="index.php"    class="nav-link">Home</a>
            <a href="about.php"    class="nav-link">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>
        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
        </a>
        <div class="nav-right">
        <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle theme">
            <span id="theme-icon">🌙</span>
        </button>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="nav-link">Register</a>
                <a href="login.php"    class="nav-link">Log in</a>
                <a href="shopping_cart.php" class="basket-link active" aria-label="Shopping basket">
                    <img src="basket_icon.png"        class="basket-icon basket-icon-default" alt="Basket icon"/>
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active"  alt="Active basket icon"/>
                </a>
            <?php else: ?>
                <?php $role = $_SESSION['role'] ?? 'customer'; ?>
                <?php if ($role === 'admin'): ?>
                    <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>
                    <a href="admin_profile.php" class="account-link" aria-label="Admin account">
                        <img src="account_icon.png"        class="account-icon account-icon-default" alt="Account icon"/>
                        <img src="active_account_icon.png" class="account-icon account-icon-active"  alt="Active account icon"/>
                    </a>
                    <a href="shopping_cart.php" class="basket-link active" aria-label="Shopping basket">
                        <img src="basket_icon.png"        class="basket-icon basket-icon-default" alt="Basket icon"/>
                        <img src="active_basket_icon.png" class="basket-icon basket-icon-active"  alt="Active basket icon"/>
                    </a>
                <?php else: ?>
                    <a href="customer_profile.php" class="account-link" aria-label="My account">
                        <img src="account_icon.png"        class="account-icon account-icon-default" alt="Account icon"/>
                        <img src="active_account_icon.png" class="account-icon account-icon-active"  alt="Active account icon"/>
                    </a>
                    <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                        <img src="basket_icon.png"        class="basket-icon basket-icon-default" alt="Basket icon"/>
                        <img src="active_basket_icon.png" class="basket-icon basket-icon-active"  alt="Active basket icon"/>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="checkout-page">

    <h1 class="checkout-title">Checkout</h1>

    <?php if ($order_success): ?>
        <div class="success-message">
            Thank you! Your order has been placed successfully.
            <a href="customer_profile.php" style="color:#155724;font-weight:700;margin-left:8px;">View your orders &rarr;</a>
        </div>
    <?php elseif ($error_message !== ''): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="checkout-layout">

        <!-- LEFT: form -->
        <div class="checkout-box">
            <h2 class="section-heading">Delivery details</h2>

            <form method="post">
                <div class="form-group">
                    <label for="full_name">Full name *</label>
                    <input type="text" name="full_name" id="full_name"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ($_SESSION['full_name'] ?? $_SESSION['username'] ?? '')); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email address *</label>
                    <input type="email" name="email" id="email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ($_SESSION['email'] ?? '')); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea name="address" id="address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" name="city" id="city"
                           value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="postcode">Postcode *</label>
                    <input type="text" name="postcode" id="postcode"
                           value="<?php echo htmlspecialchars($_POST['postcode'] ?? ''); ?>">
                </div>

                <!-- PAYMENT METHOD -->
                <section class="checkout-section">
                    <h2 class="checkout-subtitle">Payment method</h2>
                    <div class="payment-options">

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="card" checked>
                            <div class="payment-option-inner">
                                <img src="master_card.png" class="payment-icon">
                                <div class="payment-text">
                                    <span class="payment-name">Credit / Debit Card</span>
                                    <span class="payment-desc">No extra fees</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="paypal">
                            <div class="payment-option-inner">
                                <img src="Pay_pal.png" class="payment-icon">
                                <div class="payment-text">
                                    <span class="payment-name">PayPal</span>
                                    <span class="payment-desc">Secure online payment</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="apple_pay">
                            <div class="payment-option-inner">
                                <img src="apple_pay.png" class="payment-icon">
                                <div class="payment-text">
                                    <span class="payment-name">Apple Pay</span>
                                    <span class="payment-desc">Pay with Apple Wallet</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="klarna">
                            <div class="payment-option-inner">
                                <img src="Klarna.png" class="payment-icon">
                                <div class="payment-text">
                                    <span class="payment-name">Klarna</span>
                                    <span class="payment-desc">Pay in 3 instalments</span>
                                </div>
                            </div>
                        </label>

                    </div>
                </section>

                <button type="submit" class="btn-primary" <?php echo empty($cartItems) ? 'disabled' : ''; ?>>
                    Place order
                </button>
            </form>
        </div>

        <!-- RIGHT: summary -->
        <div class="checkout-box">
            <h2 class="section-heading">Order summary</h2>

            <?php if (empty($cartItems)): ?>
                <div class="summary-list">Your basket is empty.</div>
            <?php else: ?>
                <div class="summary-list">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="summary-item-row">
                            <div class="summary-item-name">
                                <?php echo htmlspecialchars($item['name']); ?>
                                <small><?php echo htmlspecialchars($item['size_label']); ?> &times; <?php echo $item['qty']; ?></small>
                            </div>
                            <div>£<?php echo number_format($item['line_total'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-footer-row">
                    <span>Subtotal</span>
                    <span>£<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-footer-row">
                    <span>Delivery</span>
                    <span>Free</span>
                </div>
                <div class="summary-footer-row summary-footer-total">
                    <span>Total</span>
                    <span>£<?php echo number_format($subtotal, 2); ?></span>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<footer class="nova-footer">
    <div class="nova-footer-inner">
        <div class="footer-top-row">
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
            <div class="footer-col">
                <h4>About Us</h4>
                <a href="about.php">Our Story</a>
                <a href="#">Our Social Purpose</a>
                <a href="#">Careers</a>
                <a href="#">Student Discount</a>
                <a href="#">VIP Rewards</a>
                <a href="#">Charity Partners</a>
            </div>
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
            <div class="footer-col footer-col-right">
                <div class="footer-payments">
                    <img src="master_card.png" alt="Mastercard">
                    <img src="Pay_pal.png"     alt="PayPal">
                    <img src="apple_pay.png"   alt="Apple Pay">
                    <img src="Klarna.png"      alt="Klarna">
                </div>
                <div class="footer-rating-card">
                    <div class="rating-logo">TrustScore</div>
                    <div class="rating-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <div class="rating-text">4.7 | 154,224 reviews</div>
                </div>
                <div class="footer-membership-logo"><span>Member of NOVA Group</span></div>
                <div class="footer-app-badges">
                    <img src="app_store.png"  alt="Download on App Store">
                    <img src="play_store.png" alt="Download on Google Play">
                </div>
            </div>
        </div>
        <div class="footer-middle-row">
            <div class="footer-social">
                <a href="#" class="social-circle">f</a>
                <a href="#" class="social-circle">x</a>
                <a href="#" class="social-circle">&#9658;</a>
                <a href="#" class="social-circle">in</a>
                <a href="#" class="social-circle">P</a>
            </div>
        </div>
        <div class="footer-bottom-row">
            <p>Copyright &copy; 2025 NOVA Fragrance Ltd</p>
            <p>NOVA Fragrance Ltd is registered in England &amp; Wales. This website is for educational use as part of a university project.</p>
        </div>
    </div>
</footer>
<script src="theme.js"></script>
</body>
</html>
