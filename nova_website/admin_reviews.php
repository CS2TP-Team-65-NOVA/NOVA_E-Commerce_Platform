<?php
session_start();
require_once 'config.php';

// ---------- 1. PROTECT PAGE – ONLY ADMIN ----------
$role = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? '');
if (!isset($_SESSION['user_id']) || $role !== 'admin') {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION['full_name'] ?? ($_SESSION['user_name'] ?? 'Admin');

// ---------- 2. HANDLE DELETE ----------
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    if ($deleteId > 0) {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $deleteId);
            $stmt->execute();
            $stmt->close();
        }
    }
    // redirect to avoid resubmission on refresh
    header("Location: admin_reviews.php");
    exit;
}

// ---------- 3. STATS ----------
function get_value($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if ($res && $row = mysqli_fetch_row($res)) {
        return $row[0];
    }
    return 0;
}

$totalReviews    = (int) get_value($conn, "SELECT COUNT(*) FROM reviews");
$avgRatingRaw    = get_value($conn, "SELECT ROUND(AVG(rating),1) FROM reviews");
$avgRating       = $avgRatingRaw !== null ? $avgRatingRaw : 0;
$recentReviews   = get_value($conn, "SELECT COUNT(*) FROM reviews WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$positiveReviews = get_value($conn, "SELECT COUNT(*) FROM reviews WHERE rating >= 4");

// ---------- 4. FETCH REVIEWS ----------
$reviewsSql = "
    SELECT
        r.review_id,
        r.rating,
        r.comment,
        r.created_at,
        p.name      AS product_name,
        u.full_name AS user_name
    FROM reviews r
    JOIN products p ON r.product_id = p.product_id
    JOIN users    u ON r.user_id    = u.user_id
    ORDER BY r.created_at DESC
";
$reviewsResult = mysqli_query($conn, $reviewsSql);

// ---------- HELPER ----------
function safe($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

// Star rating helper
function renderStars($rating) {
    $stars      = '';
    $fullStars  = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;

    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            $stars .= '<span style="color: #f59e0b;">★</span>';
        } elseif ($i == $fullStars + 1 && $hasHalfStar) {
            $stars .= '<span style="color: #f59e0b;">★</span>';
        } else {
            $stars .= '<span style="color: #d1d5db;">☆</span>';
        }
    }
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reviews</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Belleza font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="style.css?v=2">

    <!-- Favicon -->
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

        <!-- RIGHT SIDE (role-based) -->
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

<!-- ADMIN LAYOUT -->
<div class="admin-layout">
    <div class="sidebar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_reviews.php" class="active">Manage Reviews</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Reviews Management</h1>
            <p class="welcome-text">Manage customer reviews for your perfumes</p>
        </div>
        
        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $totalReviews; ?></div>
                <h3>Total Reviews</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo number_format((float)$avgRating, 1); ?>/5</div>
                <div class="star-rating"><?php echo renderStars($avgRating); ?></div>
                <h3>Average Rating</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $positiveReviews; ?></div>
                <h3>Positive Reviews (4+ stars)</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $recentReviews; ?></div>
                <h3>This Week</h3>
            </div>
        </div>
            

        
       <!-- REVIEWS TABLE -->
<div class="dashboard-panel">
    <div class="panel-header">
        <h2>All Reviews</h2>
        <span style="color: #666; font-size: 14px;">Latest first</span>
    </div>
    
    <?php if ($reviewsResult && mysqli_num_rows($reviewsResult) > 0): ?>
        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Review</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($reviewsResult)): ?>
                        <tr>
                            <td>
                                <div class="product-name"><?php echo safe($row['product_name']); ?></div>
                                <div class="product-desc"><?php echo safe($row['comment']); ?></div>
                            </td>
                            
                            <td>
                                <div class="product-name"><?php echo safe($row['user_name']); ?></div>
                            </td>
                            
                            <td>
                                <div class="product-desc" style="margin-bottom: 4px;">
                                    <?php echo renderStars($row['rating']); ?>
                                </div>
                                <span class="variants-badge">
                                    <?php echo (int)$row['rating']; ?>/5
                                </span>
                            </td>
                            
                            <td>
                                <div class="product-desc">
                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </div>
                            </td>
                            
                            <td>
                                <div class="action-buttons">
                                    <button class="delete-btn" 
                                            onclick="if(confirm('Delete this review?')) window.location.href='admin_reviews.php?delete=<?php echo (int)$row['review_id']; ?>'">
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
            <p>No reviews have been submitted yet.</p>
        </div>
    <?php endif; ?>
</div>
    </main>
    </div>

<!-- GLOBAL NOVA FOOTER -->
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
