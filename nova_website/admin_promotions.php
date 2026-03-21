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
$addMessage = '';

// Delete promotion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $promotionId = (int) $_GET['delete'];

    $deleteSql = "DELETE FROM promotions WHERE promotion_id = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);

    if ($deleteStmt) {
        mysqli_stmt_bind_param($deleteStmt, "i", $promotionId);

        if (mysqli_stmt_execute($deleteStmt)) {
            $success = "Promotion deleted successfully.";
        } else {
            $error = "Failed to delete promotion.";
        }

        mysqli_stmt_close($deleteStmt);
    } else {
        $error = "Failed to prepare delete query.";
    }
}

// Add promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_promotion') {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $discountType = trim($_POST['discount_type'] ?? '');
    $discountValue = (float) ($_POST['discount_value'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if (
        $name === '' ||
        $code === '' ||
        $discountType === '' ||
        $discountValue <= 0 ||
        $startDate === '' ||
        $endDate === ''
    ) {
        $addMessage = "Please fill in all fields correctly.";
    } elseif (!in_array($discountType, ['percentage', 'fixed'])) {
        $addMessage = "Invalid discount type.";
    } elseif ($endDate < $startDate) {
        $addMessage = "End date cannot be before start date.";
    } else {
        $today = date('Y-m-d');

        if ($endDate < $today) {
            $status = 'expired';
        } elseif ($startDate > $today) {
            $status = 'scheduled';
        } else {
            $status = 'active';
        }

        $insertSql = "INSERT INTO promotions 
            (promotion_name, promo_code, discount_type, discount_value, start_date, end_date, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = mysqli_prepare($conn, $insertSql);

        if ($insertStmt) {
            mysqli_stmt_bind_param(
                $insertStmt,
                "sssdsss",
                $name,
                $code,
                $discountType,
                $discountValue,
                $startDate,
                $endDate,
                $status
            );

            if (mysqli_stmt_execute($insertStmt)) {
                $success = "Promotion added successfully.";
            } else {
                $addMessage = "Failed to add promotion.";
            }

            mysqli_stmt_close($insertStmt);
        } else {
            $addMessage = "Failed to prepare insert query.";
        }
    }
}

// Auto-update statuses
$today = date('Y-m-d');
mysqli_query($conn, "UPDATE promotions SET status = 'expired' WHERE end_date < '$today'");
mysqli_query($conn, "UPDATE promotions SET status = 'scheduled' WHERE start_date > '$today'");
mysqli_query($conn, "UPDATE promotions SET status = 'active' WHERE start_date <= '$today' AND end_date >= '$today'");

// Stats
$stats = [
    'totalPromotions' => 0,
    'activePromotions' => 0,
    'expiredPromotions' => 0,
    'scheduledPromotions' => 0
];

$statsSql = "
    SELECT
        COUNT(*) AS totalPromotions,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS activePromotions,
        SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) AS expiredPromotions,
        SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) AS scheduledPromotions
    FROM promotions
";

$statsResult = mysqli_query($conn, $statsSql);
if ($statsResult && mysqli_num_rows($statsResult) > 0) {
    $stats = mysqli_fetch_assoc($statsResult);
}

// Load promotions
$promotionsSql = "SELECT * FROM promotions ORDER BY promotion_id DESC";
$promotionsResult = mysqli_query($conn, $promotionsSql);

if (!$promotionsResult) {
    $error = "Failed to load promotions.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>Manage Promotions</title>

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
        <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle theme">
            <span id="theme-icon">🌙</span>
        </button>
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

<!-- ADMIN LAYOUT -->
<div class="admin-layout">
    <div class="sidebar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_promotions.php" class="active">Manage Promotions</a>
        <a href="admin_reviews.php">Manage Reviews</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Promotion Management</h1>
            <p class="welcome-text">Create and manage store promotions and discount codes</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo (int)($stats['totalPromotions'] ?? 0); ?></div>
                <h3>Total Promotions</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo (int)($stats['activePromotions'] ?? 0); ?></div>
                <h3>Active</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo (int)($stats['expiredPromotions'] ?? 0); ?></div>
                <h3>Expired</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo (int)($stats['scheduledPromotions'] ?? 0); ?></div>
                <h3>Scheduled</h3>
            </div>
        </div>
        
        <!-- PROMOTIONS TABLE -->
        <div class="dashboard-panel">
            <div class="panel-header">
                <h2>All Promotions</h2>
                <span style="color: #666; font-size: 14px;">Newest first</span>
            </div>

            <?php if ($promotionsResult && mysqli_num_rows($promotionsResult) > 0): ?>
                <div class="users-table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Promotion</th>
                                <th>Type</th>
                                <th>Discount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($promotionsResult)): ?>
                                <tr>
                                    <td>
                                        <div class="product-name">
                                            <?php echo htmlspecialchars($row['promotion_name']); ?>
                                        </div>
                                        <div class="product-desc">
                                            Code: <?php echo htmlspecialchars($row['promo_code']); ?>
                                        </div>
                                        <div class="product-desc">
                                            <?php echo date('M d, Y', strtotime($row['start_date'])); ?>
                                            -
                                            <?php echo date('M d, Y', strtotime($row['end_date'])); ?>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="category-tag">
                                            <?php echo htmlspecialchars(ucfirst($row['discount_type'])); ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <span class="variants-badge">
                                            <?php
                                            if ($row['discount_type'] === 'fixed') {
                                                echo '£' . number_format((float)$row['discount_value'], 2);
                                            } else {
                                                echo number_format((float)$row['discount_value'], 0) . '%';
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <span class="category-tag">
                                            <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div class="action-buttons">
                                            <button class="delete-btn"
                                                onclick="if(confirm('Delete this promotion?')) window.location.href='admin_promotions.php?delete=<?php echo (int)$row['promotion_id']; ?>'">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No promotions found.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ADD PROMOTION FORM BELOW TABLE -->
        <div class="dashboard-panel">
            <div class="panel-header">
                <h2>Add Promotion</h2>
                <span style="color: #666; font-size: 14px;">Create new promotion</span>
            </div>

            <?php if (!empty($addMessage)): ?>
                <div class="error-message"><?php echo htmlspecialchars($addMessage); ?></div>
            <?php endif; ?>

            <form method="post" action="admin_promotions.php">
                <input type="hidden" name="action" value="add_promotion">

                <div class="form-group">
                    <label for="name">Promotion Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Enter promotion name">
                </div>

                <div class="form-group">
                    <label for="code">Promo Code *</label>
                    <input type="text" id="code" name="code" required placeholder="Enter promo code">
                </div>

                <div class="form-group">
                    <label for="discount_type">Discount Type *</label>
                    <select id="discount_type" name="discount_type" required>
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (£)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="discount_value">Discount Value *</label>
                    <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date *</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>

                <button type="submit" class="add-btn">Add Promotion</button>

                <div class="form-note">
                    Create discount codes and promotional campaigns for your store.
                </div>
            </form>
        </div>
    </main>
</div>

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
<script src="theme.js"></script>
</body>
</html>
