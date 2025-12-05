<?php
session_start();
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'customer') !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch orders
$orders = [];
try {
    $stmt = $conn->prepare("
        SELECT o.order_id, o.order_number, o.order_date, o.total_amount, 
               o.payment_status, o.delivery_status,
               CONCAT(u.full_name, ' (', u.email, ')') as customer_name,
               COUNT(oi.order_items_id) as item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ");
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Failed to load orders.";
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id   = intval($_POST['order_id']);
    $new_status = $_POST['delivery_status'];

    try {
        $stmt = $conn->prepare("UPDATE orders SET delivery_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $success = "Order status updated successfully.";
    } catch (Exception $e) {
        $error = "Failed to update status.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>Manage Orders</title>

    <!-- Google Belleza Font (same as other pages) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <!-- Global + admin styles -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="admin_style.css">

    <!-- NOVA favicon -->
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<!-- HEADER: same navbar pattern as other pages -->
<header id="main-header">
    <nav id="navbar">

        <!-- LEFT SIDE -->
        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>

        <!-- CENTER LOGO -->
        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
        </a>

        <!-- RIGHT SIDE (role-based, same structure as other pages) -->
        <div class="nav-right">

        <?php if (!isset($_SESSION['user_id'])): ?>

            <!-- Guest -->
            <a href="register.php" class="nav-link">Register</a>
            <a href="login.php" class="nav-link">Log in</a>

            <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
            </a>

        <?php else: ?>
            <?php $role = $_SESSION['role'] ?? 'customer'; ?>

            <?php if ($role === 'admin'): ?>

                <!-- ADMIN: show Admin Dashboard link + admin account icon + basket -->
                <a href="admin_dashboard.php" class="nav-link active">Admin Dashboard</a>

                <a href="admin_profile.php" class="account-link" aria-label="Admin account">
                    <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon" />
                    <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon" />
                </a>

                <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
                </a>

            <?php else: ?>

                <!-- CUSTOMER: profile + basket -->
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


<!-- ADMIN LAYOUT -->
<div class="admin-layout">
    <div class="sidebar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_orders.php" class="active">Manage Orders</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_reviews.php">Manage Reviews</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Orders</h1>
            <p class="welcome-text">
                Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>!
                Here’s an overview of recent orders.
            </p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- STATS -->
        <div class="stats-cards">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="number"><?php echo count($orders); ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="number">
                    <?php echo count(array_filter($orders, fn($o) => $o['delivery_status'] === 'processing')); ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Shipped</h3>
                <div class="number">
                    <?php echo count(array_filter($orders, fn($o) => $o['delivery_status'] === 'shipped')); ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Revenue</h3>
                <div class="number">£<?php echo number_format(array_sum(array_column($orders, 'total_amount')), 2); ?></div>
            </div>
        </div>
        
        <!-- ORDERS TABLE -->
        <div class="orders-table-container">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 40px;">No orders found</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td><?php echo (int)$order['item_count']; ?> items</td>
                                <td><strong>£<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($order['delivery_status']); ?>">
                                        <?php echo ucfirst($order['delivery_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-view" onclick="viewOrder(<?php echo (int)$order['order_id']; ?>)">View</button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                                            <input type="hidden" name="delivery_status" value="shipped">
                                            <button type="submit" name="update_status" class="btn-ship">Ship</button>
                                        </form>
                                        <button class="btn-cancel" onclick="cancelOrder(<?php echo (int)$order['order_id']; ?>)">Cancel</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- MODAL -->
<div id="orderModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Order Details</h2>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="orderDetailsContent">
            <!-- Content loaded via JavaScript -->
        </div>
    </div>
</div>

<!-- GLOBAL NOVA FOOTER (same as other pages) -->
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

<script>
function viewOrder(orderId) {
    // In a real app, you'd fetch via AJAX
    document.getElementById('orderModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function cancelOrder(orderId) {
    if (confirm('Cancel order #' + orderId + '?')) {
        // AJAX request to cancel order
        alert('Order cancelled.');
    }
}

window.onclick = function(e) {
    if (e.target === document.getElementById('orderModal')) {
        closeModal();
    }
}
</script>

</body>
</html>
