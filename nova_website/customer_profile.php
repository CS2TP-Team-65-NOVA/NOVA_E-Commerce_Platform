<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION['user_id'];


$currentData = [];
$stmt = $conn->prepare("SELECT user_id, full_name, email, created_at FROM users WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $currentData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}


$userName  = $currentData['full_name']
             ?? $_SESSION['full_name']
             ?? $_SESSION['username']
             ?? 'User';
$userEmail = $currentData['email'] ?? $_SESSION['email'] ?? '';
$userRole  = $_SESSION['role']     ?? 'customer';


$_SESSION['full_name'] = $userName;
$_SESSION['username']  = $userName;
$_SESSION['email']     = $userEmail;


$userOrders = [];
$stmt = $conn->prepare("
    SELECT
        o.order_id,
        o.order_number,
        o.created_at AS order_date,
        o.total_amount,
        o.payment_status,
        o.delivery_status,
        GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS product_names
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.order_id
    LEFT JOIN product_versions pv ON pv.size_id = oi.size_id
    LEFT JOIN products p ON p.product_id = pv.product_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
    LIMIT 10
");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $userOrders[] = $row;
    }
    $stmt->close();
}

// ---------- 4. FETCH RECENT REVIEWS ----------
$userReviews = [];
$stmt = $conn->prepare("
    SELECT r.review_id, r.rating, r.comment, r.created_at, p.name AS product_name
    FROM reviews r
    JOIN products p ON r.product_id = p.product_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $userReviews[] = $row;
    }
    $stmt->close();
}

// ---------- 5. COUNTS ----------
$totalOrders  = 0;
$totalReviews = 0;

$r = $conn->query("SELECT COUNT(*) AS c FROM orders  WHERE user_id = $userId");
if ($r) { $totalOrders  = (int) $r->fetch_assoc()['c']; }

$r = $conn->query("SELECT COUNT(*) AS c FROM reviews WHERE user_id = $userId");
if ($r) { $totalReviews = (int) $r->fetch_assoc()['c']; }

// ---------- 6. HANDLE UPDATE PROFILE ----------
$updateMessage = "";
$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email']     ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateMessage = "Please enter a valid email address.";
    } elseif (empty($full_name)) {
        $updateMessage = "Full name is required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $updateMessage = "This email is already registered by another user.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $full_name, $email, $userId);
            if ($stmt->execute()) {
                $_SESSION['full_name'] = $full_name;   
                $_SESSION['username']  = $full_name;   
                $_SESSION['email']     = $email;
                $userName              = $full_name;
                $updateMessage         = "Profile updated successfully!";
                $updateSuccess         = true;
                $currentData['full_name'] = $full_name;
                $currentData['email']     = $email;
            } else {
                $updateMessage = "Failed to update profile. Please try again.";
            }
        }
        $stmt->close();
    }
}

$passwordMessage = "";
$passwordSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password']     ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $passwordMessage = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $passwordMessage = "New password and confirmation do not match.";
    } elseif (strlen($new_password) < 6) {
        $passwordMessage = "New password must be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt   = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed, $userId);
            if ($stmt->execute()) {
                $passwordMessage = "Password changed successfully!";
                $passwordSuccess = true;
            } else {
                $passwordMessage = "Failed to change password. Please try again.";
            }
            $stmt->close();
        } else {
            $passwordMessage = "Current password is incorrect.";
        }
    }
}


function safe($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
function renderStars($rating) {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $rating ? '<span style="color:#f59e0b;">&#9733;</span>' : '<span style="color:#d1d5db;">&#9734;</span>';
    }
    return $out;
}
function formatStatus($status) {
    $map = [
        'pending'    => 'status-pending',
        'processing' => 'status-processing',
        'shipped'    => 'status-shipped',
        'delivered'  => 'status-delivered',
        'cancelled'  => 'status-cancelled',
        'paid'       => 'status-paid',
    ];
    $class = $map[strtolower($status ?? '')] ?? 'status-pending';
    return "<span class='status-badge $class'>" . ucfirst($status ?? 'Pending') . "</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – NOVA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="customer_profile.css">
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body class="cp-body">


<header id="main-header" class="cp-header">
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
            <a href="customer_profile.php" class="account-link active" aria-label="My account">
                <img src="account_icon.png"        class="account-icon account-icon-default" alt="Account icon">
                <img src="active_account_icon.png" class="account-icon account-icon-active"  alt="Active account icon">
            </a>
            <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                <img src="basket_icon.png"        class="basket-icon basket-icon-default" alt="Basket icon">
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active"  alt="Active basket icon">
            </a>
        </div>
    </nav>
</header>


<div class="cp-page-wrap">

    
    <aside class="cp-sidebar">

        <div class="cp-sidebar-top">
            <div class="cp-avatar-ring">
                <div class="cp-avatar-inner">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
            </div>
            <p class="cp-name"><?php echo safe($userName); ?></p>
            <p class="cp-role"><?php echo ucfirst($userRole); ?></p>
            <div class="cp-sb-stats">
                <div class="cp-sb-stat">
                    <span class="cp-sb-stat-value"><?php echo $totalOrders; ?></span>
                    <span class="cp-sb-stat-label">Orders</span>
                </div>
                <div class="cp-sb-stat">
                    <span class="cp-sb-stat-value"><?php echo $totalReviews; ?></span>
                    <span class="cp-sb-stat-label">Reviews</span>
                </div>
                <div class="cp-sb-stat">
                    <span class="cp-sb-stat-value active">&#10003;</span>
                    <span class="cp-sb-stat-label">Active</span>
                </div>
            </div>
        </div>

        <nav class="cp-nav">
            <a href="#section-profile" class="cp-nav-link cp-nav-active">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                My Profile
            </a>
            <a href="#section-orders" class="cp-nav-link">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                My Orders
            </a>
            <a href="#section-password" class="cp-nav-link">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                Change Password
            </a>
            <?php if (!empty($userReviews)): ?>
            <a href="#section-reviews" class="cp-nav-link">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                My Reviews
            </a>
            <?php endif; ?>
            <div class="cp-nav-divider"></div>
            <a href="shopping_cart.php" class="cp-nav-link">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM7.5 13h9.3l1.7-8H5.2L4.7 3H1v2h2l3.6 7.6L5.2 15C4.5 15 4 15.5 4 16.2s.5 1.3 1.2 1.3H19v-2H6.4l.7-2.5z"/></svg>
                Shopping Cart
            </a>
            <a href="perfumes.php" class="cp-nav-link">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                Browse Perfumes
            </a>
            <div class="cp-nav-divider"></div>
            <a href="logout.php" class="cp-nav-link cp-nav-logout">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                Log Out
            </a>
        </nav>

        <div class="cp-sb-bottom">
            <div class="cp-sb-bottom-dot"></div>
            <span class="cp-sb-bottom-text">NOVA FRAGRANCE &middot; 2025</span>
        </div>
    </aside>

    <!-- ---- MAIN CONTENT ---- -->
    <main class="cp-main">

        <div class="cp-page-header">
            <h1>My Account</h1>
            <p>Welcome back, <?php echo safe($userName); ?>! Manage your profile and orders here.</p>
        </div>

        <!-- SNAPSHOT BAR -->
        <!-- UPDATE PROFILE -->
        <section class="cp-card" id="section-profile">
            <div class="cp-card-header">
                <h2>Personal Information</h2>
                <span class="cp-badge">Account Details</span>
            </div>
            <?php if ($updateMessage !== ""): ?>
                <div class="cp-alert <?php echo $updateSuccess ? 'cp-alert-success' : 'cp-alert-error'; ?>">
                    <?php echo safe($updateMessage); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="customer_profile.php">
                <input type="hidden" name="action" value="update_profile">
                <div class="cp-form-grid">
                    <div class="cp-field">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                               value="<?php echo safe($currentData['full_name'] ?? $userName); ?>"
                               placeholder="Your full name" required>
                    </div>
                    <div class="cp-field">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo safe($currentData['email'] ?? $userEmail); ?>"
                               placeholder="your@email.com" required>
                    </div>
                </div>
                <div class="cp-form-actions">
                    <button type="submit" class="cp-btn-primary">Save Changes</button>
                </div>
            </form>
        </section>

       
        <section class="cp-card" id="section-orders">
            <div class="cp-card-header">
                <h2>Recent Orders</h2>
                <?php if (!empty($userOrders)): ?>
                    <a href="customer_orders.php" class="cp-card-link">View All &rarr;</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($userOrders)): ?>
                <div class="cp-table-wrap">
                    <table class="cp-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Products</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Delivery</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo safe($order['order_number'] ?? $order['order_id']); ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo safe($order['product_names'] ?? '—'); ?></td>
                                    <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo formatStatus($order['payment_status']); ?></td>
                                    <td><?php echo formatStatus($order['delivery_status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="cp-empty">
                    <p>You haven't placed any orders yet.</p>
                    <a href="perfumes.php" class="cp-empty-link">Start Shopping &rarr;</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- CHANGE PASSWORD -->
        <section class="cp-card" id="section-password">
            <div class="cp-card-header">
                <h2>Change Password</h2>
                <span class="cp-badge cp-badge-amber">Security</span>
            </div>
            <?php if ($passwordMessage !== ""): ?>
                <div class="cp-alert <?php echo $passwordSuccess ? 'cp-alert-success' : 'cp-alert-error'; ?>">
                    <?php echo safe($passwordMessage); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="customer_profile.php">
                <input type="hidden" name="action" value="change_password">
                <div class="cp-form-grid">
                    <div class="cp-field cp-field-full">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="cp-field">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password"
                               required minlength="6" placeholder="At least 6 characters">
                        <div id="password-strength" class="cp-strength-text"></div>
                    </div>
                    <div class="cp-field">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="cp-form-actions">
                    <button type="submit" class="cp-btn-primary">Change Password</button>
                </div>
            </form>
        </section>

        <!-- MY REVIEWS -->
        <?php if (!empty($userReviews)): ?>
        <section class="cp-card" id="section-reviews">
            <div class="cp-card-header">
                <h2>My Reviews</h2>
                <span class="cp-badge"><?php echo count($userReviews); ?> review<?php echo count($userReviews) !== 1 ? 's' : ''; ?></span>
            </div>
            <div class="cp-reviews-list">
                <?php foreach ($userReviews as $review): ?>
                    <div class="cp-review-item">
                        <div class="cp-review-top">
                            <strong class="cp-review-product"><?php echo safe($review['product_name']); ?></strong>
                            <span class="cp-review-stars"><?php echo renderStars($review['rating']); ?></span>
                        </div>
                        <p class="cp-review-comment"><?php echo safe($review['comment']); ?></p>
                        <small class="cp-review-date"><?php echo date('d M Y', strtotime($review['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- DANGER ZONE -->
        <section class="cp-card cp-danger-card">
            <div class="cp-card-header">
                <h2>Account Actions</h2>
            </div>
            <p class="cp-danger-desc">These actions are permanent and cannot be undone.</p>
            <div class="cp-danger-actions">
                <a href="logout.php" class="cp-btn-danger-outline">Log Out</a>
                <a href="delete_account.php"
                   onclick="return confirm('Permanently delete your account? This cannot be undone.');"
                   class="cp-btn-danger">Delete Account</a>
            </div>
        </section>

    </main>
</div><!-- /cp-page-wrap -->

<!-- FOOTER -->
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

<script>
document.getElementById('new_password').addEventListener('input', function () {
    const pw = this.value;
    const el = document.getElementById('password-strength');
    let msg = '', color = '#888';
    if (!pw.length) { el.textContent = ''; return; }
    if (pw.length < 6)                              { msg = 'Too short'; color = '#dc3545'; }
    else if (pw.length < 8)                         { msg = 'Fair';      color = '#ffc107'; }
    else if (!/[A-Z]/.test(pw)||!/[0-9]/.test(pw)) { msg = 'Good';      color = '#28a745'; }
    else                                             { msg = 'Strong \u2713'; color = '#20c997'; }
    el.textContent = msg;
    el.style.color = color;
});

document.querySelectorAll('.cp-nav-link[href^="#"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        const t = document.querySelector(this.getAttribute('href'));
        if (t) {
            e.preventDefault();
            t.scrollIntoView({ behavior: 'smooth', block: 'start' });
            document.querySelectorAll('.cp-nav-link').forEach(l => l.classList.remove('cp-nav-active'));
            this.classList.add('cp-nav-active');
        }
    });
});
</script>
</body>
</html>