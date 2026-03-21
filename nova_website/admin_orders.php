<?php
session_start();
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'customer') !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $new_status = trim($_POST['delivery_status'] ?? '');

    $allowed_statuses = ['processing', 'packed', 'shipped', 'delivered', 'cancelled'];

    if ($order_id <= 0) {
        $error = "Invalid order.";
    } elseif (!in_array($new_status, $allowed_statuses, true)) {
        $error = "Invalid order status.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE orders SET delivery_status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $new_status, $order_id);

            if ($stmt->execute()) {
                $success = "Order status updated successfully.";
            } else {
                $error = "Failed to update status.";
            }
        } catch (Exception $e) {
            $error = "Failed to update status.";
        }
    }
}

// AJAX: load order details into orders modal
if (isset($_GET['ajax']) && $_GET['ajax'] === 'order_details' && isset($_GET['order_id'])) {
    $orderId = (int) $_GET['order_id'];

    $orderSql = "
        SELECT 
            o.order_id,
            o.order_number,
            o.order_date,
            o.total_amount,
            o.payment_status,
            o.delivery_status,
            u.full_name,
            u.email
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = ?
    ";

    $stmt = $conn->prepare($orderSql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    $order = $orderResult->fetch_assoc();

    if (!$order) {
        echo '<div class="orders-modal-loading">Order not found.</div>';
        exit();
    }

    $itemsSql = "
        SELECT 
            oi.quantity,
            oi.price,
            pv.size_ml,
            p.name AS product_name
        FROM order_items oi
        LEFT JOIN product_versions pv ON oi.size_id = pv.size_id
        LEFT JOIN products p ON pv.product_id = p.product_id
        WHERE oi.order_id = ?
    ";

    $stmt = $conn->prepare($itemsSql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $itemsResult = $stmt->get_result();
    ?>

    <div class="orders-modal-grid">
        <div class="orders-modal-card">
            <h3>Order Information</h3>
            <div class="orders-modal-line"><strong>Order Number:</strong> #<?php echo htmlspecialchars($order['order_number']); ?></div>
            <div class="orders-modal-line"><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
            <div class="orders-modal-line">
                <strong>Delivery Status:</strong>
                <span class="orders-status-pill orders-status-<?php echo htmlspecialchars($order['delivery_status']); ?>">
                    <?php echo htmlspecialchars(ucfirst($order['delivery_status'])); ?>
                </span>
            </div>
            <div class="orders-modal-line"><strong>Payment Status:</strong> <?php echo htmlspecialchars(ucfirst($order['payment_status'])); ?></div>
        </div>

        <div class="orders-modal-card">
            <h3>Customer Information</h3>
            <div class="orders-modal-line"><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name'] ?? 'Unknown Customer'); ?></div>
            <div class="orders-modal-line"><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'No email'); ?></div>
        </div>

        <div class="orders-modal-card">
            <h3>Update Delivery Status</h3>

            <form method="post" action="admin_orders.php" class="orders-modal-actions">
                <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                <input type="hidden" name="update_status" value="1">

                <button type="submit" name="delivery_status" value="processing" class="orders-action-btn">
                    Processing
                </button>

                <button type="submit" name="delivery_status" value="packed" class="orders-action-btn">
                    Packed
                </button>

                <button type="submit" name="delivery_status" value="shipped" class="orders-action-btn">
                    Shipped
                </button>

                <button type="submit" name="delivery_status" value="delivered" class="orders-action-btn">
                    Delivered
                </button>

                <button type="submit" name="delivery_status" value="cancelled" class="orders-action-btn orders-action-btn-danger">
                    Cancel
                </button>
            </form>
        </div>
    </div>

    <div class="orders-modal-card">
        <h3>Order Items</h3>

        <table class="orders-modal-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $itemsResult->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="product-name">
                                <?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?>
                            </div>
                            <div class="product-desc">
                                <?php echo htmlspecialchars($item['size_ml'] ?? ''); ?>ml
                            </div>
                        </td>
                        <td><?php echo (int)$item['quantity']; ?></td>
                        <td>£<?php echo number_format((float)$item['price'], 2); ?></td>
                        <td>£<?php echo number_format((float)$item['price'] * (int)$item['quantity'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="orders-modal-total">
            Total: £<?php echo number_format((float)$order['total_amount'], 2); ?>
        </div>
    </div>

    <?php
    exit();
}

// Fetch orders
$orders = [];
try {
    $stmt = $conn->prepare("
        SELECT 
            o.order_id, 
            o.order_number, 
            o.order_date, 
            o.total_amount, 
            o.payment_status, 
            o.delivery_status,
            CONCAT(u.full_name, ' (', u.email, ')') AS customer_name,
            COUNT(oi.order_items_id) AS item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id, o.order_number, o.order_date, o.total_amount, o.payment_status, o.delivery_status, u.full_name, u.email
        ORDER BY o.order_date DESC
    ");
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Failed to load orders.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>Manage Orders</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="style.css?v=4">

    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<header id="main-header">
    <nav id="navbar">

        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>

        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
        </a>

        <div class="nav-right">

        <?php if (!isset($_SESSION['user_id'])): ?>

            <a href="register.php" class="nav-link">Register</a>
            <a href="login.php" class="nav-link">Log in</a>

            <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
            </a>

        <?php else: ?>
            <?php $role = $_SESSION['role'] ?? 'customer'; ?>

            <?php if ($role === 'admin'): ?>

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
            <h1>Orders Management</h1>
            <p class="welcome-text">Manage customer orders and delivery progress</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo count($orders); ?></div>
                <h3>Total Orders</h3>
            </div>
            
            <div class="stat-card">
                <div class="number">
                    <?php echo count(array_filter($orders, fn($o) => $o['delivery_status'] === 'processing')); ?>
                </div>
                <h3>Pending</h3>
            </div>
            
            <div class="stat-card">
                <div class="number">
                    <?php echo count(array_filter($orders, fn($o) => $o['delivery_status'] === 'shipped')); ?>
                </div>
                <h3>Shipped</h3>
            </div>
            
            <div class="stat-card">
                <div class="number">£<?php echo number_format(array_sum(array_column($orders, 'total_amount')), 2); ?></div>
                <h3>Revenue</h3>
            </div>
        </div>
        
        <div class="dashboard-panel">
            <div class="panel-header">
                <h2>All Orders</h2>
                <span style="color: #666; font-size: 14px;">Most recent first</span>
            </div>

            <?php if (!empty($orders)): ?>
                <div class="users-table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <div class="product-name">
                                            #<?php echo htmlspecialchars($order['order_number']); ?>
                                        </div>
                                        <div class="product-desc">
                                            <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                        </div>
                                        <div class="product-desc">
                                            <?php echo (int)$order['item_count']; ?> items
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="product-name">
                                            <?php echo htmlspecialchars($order['customer_name']); ?>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="category-tag">
                                            <?php echo htmlspecialchars(ucfirst($order['delivery_status'])); ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <span class="variants-badge">
                                            £<?php echo number_format($order['total_amount'], 2); ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="add-btn" onclick="openOrdersModal(<?php echo (int)$order['order_id']; ?>)">
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No orders found.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<div id="ordersModal" class="orders-modal">
    <div class="orders-modal-content">
        <button type="button" class="orders-modal-close" onclick="closeOrdersModal()">&times;</button>
        
        <div class="orders-modal-header">
            <h2>Order Details</h2>
            <p>View full order information</p>
        </div>

        <div id="ordersModalBody" class="orders-modal-body">
            <div class="orders-modal-loading">Loading order details...</div>
        </div>
    </div>
</div>

<script>
function openOrdersModal(orderId) {
    const modal = document.getElementById('ordersModal');
    const modalBody = document.getElementById('ordersModalBody');

    modal.style.display = 'flex';
    modalBody.innerHTML = '<div class="orders-modal-loading">Loading order details...</div>';

    fetch('admin_orders.php?ajax=order_details&order_id=' + orderId)
        .then(response => response.text())
        .then(data => {
            modalBody.innerHTML = data;
        })
        .catch(() => {
            modalBody.innerHTML = '<div class="orders-modal-loading">Failed to load order details.</div>';
        });
}

function closeOrdersModal() {
    document.getElementById('ordersModal').style.display = 'none';
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('ordersModal');
    if (e.target === modal) {
        closeOrdersModal();
    }
});
</script>

</body>
</html>


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
