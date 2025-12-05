<?php
session_start();
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'customer') !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch dashboard stats
$stats = [];
try {
    // Total users
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $stats['totalUsers'] = $stmt->get_result()->fetch_row()[0];

    // Total products
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    $stats['totalProducts'] = $stmt->get_result()->fetch_row()[0];

    // Total orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders");
    $stmt->execute();
    $stats['totalOrders'] = $stmt->get_result()->fetch_row()[0];

    // Total revenue
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'success'");
    $stmt->execute();
    $stats['totalRevenue'] = $stmt->get_result()->fetch_row()[0];

    // Recent orders
    $stmt = $conn->prepare("
        SELECT o.order_id, o.order_number, o.order_date, o.total_amount, 
               o.payment_status, o.delivery_status, u.full_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recentOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = "Failed to load dashboard data.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>Admin Dashboard</title>

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
        <a href="admin_dashboard.php" class="active">Dashboard</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_reviews.php">Manage Reviews</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <p class="welcome-text">
                Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>!
                Here's your store overview.
            </p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $stats['totalUsers'] ?? 0; ?></div>
                <h3>Total Users</h3>
            </div>

            <div class="stat-card">
                <div class="number"><?php echo $stats['totalProducts'] ?? 0; ?></div>
                <h3>Products</h3>
            </div>

            <div class="stat-card">
                <div class="number"><?php echo $stats['totalOrders'] ?? 0; ?></div>
                <h3>Total Orders</h3>
            </div>

            <div class="stat-card">
                <div class="number">£<?php echo number_format($stats['totalRevenue'] ?? 0, 2); ?></div>
                <h3>Total Revenue</h3>
            </div>
        </div>

        <!-- DASHBOARD CONTENT -->
        <div class="dashboard-content">
            <!-- Recent Orders -->
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h2>Recent Orders</h2>
                    <a href="admin_orders.php" class="panel-link">View All →</a>
                </div>

                <?php if (!empty($recentOrders)): ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['order_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                    <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['delivery_status']); ?>">
                                            <?php echo ucfirst($order['delivery_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">No recent orders found.</p>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h2>Quick Actions</h2>
                </div>

                <ul class="quick-actions">
                    <li>
                        <a href="admin_products.php">
                            <div class="action-icon"></div>
                            <div class="action-text">
                                Add New Product
                                <small>Create a new perfume listing</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="admin_orders.php">
                            <div class="action-icon"></div>
                            <div class="action-text">
                                Process Orders
                                <small>Review and update order status</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="admin_users.php">
                            <div class="action-icon"></div>
                            <div class="action-text">
                                Manage Users
                                <small>View and manage customer accounts</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="admin_promotions.php">
                            <div class="action-icon"></div>
                            <div class="action-text">
                                Create Promotion
                                <small>Set up discounts and special offers</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </main>
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

</body>
</html>
