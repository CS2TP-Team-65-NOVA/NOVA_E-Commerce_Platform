<?php
session_start();
require_once 'config.php';

// ---------- 1. PROTECT PAGE – USER MUST BE LOGGED IN ----------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId    = $_SESSION['user_id'];
$userName  = $_SESSION['full_name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? '';
$userRole  = $_SESSION['role'] ?? 'customer';

// ---------- 2. FETCH CURRENT USER DATA ----------
$currentData = [];
$stmt = $conn->prepare("
    SELECT user_id, full_name, email, created_at 
    FROM users 
    WHERE user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result      = $stmt->get_result();
    $currentData = $result->fetch_assoc();
    $stmt->close();
}

// ---------- 3. FETCH USER ORDERS ----------
$userOrders = [];
$stmt = $conn->prepare("
    SELECT 
        order_id,
        order_number,
        order_date,
        total_amount,
        payment_status,
        delivery_status
    FROM orders 
    WHERE user_id = ?
    ORDER BY order_date DESC
    LIMIT 10
");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['item_count'] = 1; // simple default
        $userOrders[] = $row;
    }
    $stmt->close();
}

// ---------- 4. FETCH USER REVIEWS ----------
$userReviews = [];
$stmt = $conn->prepare("
    SELECT 
        r.review_id,
        r.rating,
        r.comment,
        r.created_at,
        p.name AS product_name
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

$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders WHERE user_id = $userId");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalOrders = $row['count'];
}

$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM reviews WHERE user_id = $userId");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalReviews = $row['count'];
}

// ---------- 6. HANDLE UPDATE PROFILE ----------
$updateMessage = "";
$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'update_profile') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateMessage = "Please enter a valid email address.";
    } elseif (empty($full_name)) {
        $updateMessage = "Full name is required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $updateMessage = "This email is already registered by another user.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $full_name, $email, $userId);

            if ($stmt->execute()) {
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email']     = $email;
                $userName              = $full_name;

                $updateMessage = "Profile updated successfully!";
                $updateSuccess = true;

                $currentData['full_name'] = $full_name;
                $currentData['email']     = $email;
            } else {
                $updateMessage = "Failed to update profile. Please try again.";
            }
            $stmt->close();
        }
    }
}

// ---------- 7. HANDLE CHANGE PASSWORD ----------
$passwordMessage = "";
$passwordSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'change_password') {

    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
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
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed_password, $userId);

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

// ---------- HELPERS ----------
function safe($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

function renderStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating)
            ? '<span style="color:#f59e0b;">★</span>'
            : '<span style="color:#d1d5db;">☆</span>';
    }
    return $stars;
}

function formatStatus($status) {
    $statusClasses = [
        'pending'    => 'status-pending',
        'processing' => 'status-processing',
        'shipped'    => 'status-shipped',
        'delivered'  => 'status-delivered',
        'cancelled'  => 'status-cancelled',
        'paid'       => 'status-paid',
    ];
    $class   = $statusClasses[strtolower($status)] ?? 'status-pending';
    $display = ucfirst($status);
    return "<span class='status-badge $class'>$display</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Belleza font + global styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- NOVA favicon -->
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<!-- HEADER (keep as you currently have it) -->
<header id="main-header">
    <nav id="navbar">

        <!-- LEFT -->
        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>

        <!-- CENTER LOGO -->
        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
        </a>

        <!-- RIGHT -->
        <div class="nav-right">
            <a href="customer_profile.php" class="account-link active" aria-label="My account">
                <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon" />
                <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon" />
            </a>

            <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon" />
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon" />
            </a>
        </div>

    </nav>
</header>

<!-- PROFILE CONTENT -->
<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <h1>MY ACCOUNT</h1>
        <p>Welcome back, <?php echo safe($userName); ?>! Manage your profile and orders here.</p>
    </div>

    <!-- Profile Grid -->
    <div class="profile-grid">
        <!-- LEFT COLUMN -->
        <div>
            <!-- User Info Card -->
            <div class="profile-card">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($userName, 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo safe($userName); ?></h3>
                        <p><?php echo safe($userEmail); ?></p>
                        <p style="color:#5e17eb;font-weight:600;margin-top:5px;">
                            <?php echo ucfirst($userRole); ?> Account
                        </p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Member Since</span>
                        <span class="info-value">
                            <?php echo date('F d, Y', strtotime($currentData['created_at'] ?? date('Y-m-d'))); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Orders</span>
                        <span class="info-value"><?php echo $totalOrders; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Reviews Posted</span>
                        <span class="info-value"><?php echo $totalReviews; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Status</span>
                        <span class="info-value" style="color:#28a745;">Active</span>
                    </div>
                </div>
            </div>

            <!-- Update Profile Form -->
            <div class="profile-card" style="margin-top:30px;">
                <div class="card-header">
                    <h2>Update Profile</h2>
                </div>

                <?php if ($updateMessage !== ""): ?>
                    <div class="message <?php echo $updateSuccess ? 'success' : 'error'; ?>">
                        <?php echo safe($updateMessage); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="customer_profile.php">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                               value="<?php echo safe($currentData['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo safe($currentData['email'] ?? ''); ?>" required>
                    </div>

                    <button type="submit" class="btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <!-- Recent Orders -->
            <div class="profile-card">
                <div class="card-header">
                    <h2>Recent Orders</h2>
                    <?php if (!empty($userOrders)): ?>
                        <a href="customer_orders.php"
                           style="color:#5e17eb;text-decoration:none;font-size:14px;">View All →</a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($userOrders)): ?>
                    <table class="user-table">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($userOrders as $order): ?>
                            <tr>
                                <td>#<?php echo safe($order['order_number'] ?? $order['order_id']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td><?php echo safe($order['item_count']); ?></td>
                                <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo formatStatus($order['delivery_status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>You haven't placed any orders yet.</p>
                        <a href="perfumes.php"
                           style="color:#5e17eb;text-decoration:none;margin-top:10px;display:inline-block;">
                            Start Shopping →
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Change Password -->
            <div class="profile-card" style="margin-top:30px;">
                <div class="card-header">
                    <h2>Change Password</h2>
                </div>

                <?php if ($passwordMessage !== ""): ?>
                    <div class="message <?php echo $passwordSuccess ? 'success' : 'error'; ?>">
                        <?php echo safe($passwordMessage); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="customer_profile.php">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required
                               minlength="6" placeholder="At least 6 characters">
                        <div id="password-strength" style="font-size:12px;margin-top:5px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn-primary">Change Password</button>
                </form>
            </div>

            <!-- ✅ QUICK LINKS MOVED UP, NEXT TO RIGHT COLUMN CONTENT -->
            <div class="quick-links">
                <a href="shopping_cart.php" class="quick-link">
                    <div class="quick-link-text">
                        <h4>Shopping Cart</h4>
                        <p>View your cart items</p>
                    </div>
                </a>

                <a href="customer_orders.php" class="quick-link">
                    <div class="quick-link-text">
                        <h4>Order History</h4>
                        <p>View all your orders</p>
                    </div>
                </a>

                <a href="perfumes.php" class="quick-link">
                    <div class="quick-link-text">
                        <h4>Continue Shopping</h4>
                        <p>Browse more perfumes</p>
                    </div>
                </a>

                <!-- ✅ LOGOUT TILE NOW HAS extra CLASS quick-link-logout -->
                <a href="logout.php" class="quick-link quick-link-logout">
                    <div class="quick-link-text">
                        <h4>Logout</h4>
                        <p>Sign out of your account</p>
                    </div>
                </a>
            </div>

            <!-- Recent Reviews -->
            <?php if (!empty($userReviews)): ?>
                <div class="profile-card" style="margin-top:30px;">
                    <div class="card-header">
                        <h2>My Reviews</h2>
                    </div>

                    <div style="max-height:300px;overflow-y:auto;">
                        <?php foreach ($userReviews as $review): ?>
                            <div style="padding:15px;border-bottom:1px solid #f0f0f0;">
                                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                                    <strong><?php echo safe($review['product_name']); ?></strong>
                                    <span class="star-rating">
                                        <?php echo renderStars($review['rating']); ?>
                                    </span>
                                </div>
                                <p style="color:#666;font-size:14px;margin:5px 0;">
                                    <?php echo safe($review['comment']); ?>
                                </p>
                                <small style="color:#999;">
                                    <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div> <!-- /RIGHT COLUMN -->
    </div> <!-- /profile-grid -->
</div> <!-- /profile-container -->

<!-- NOVA FOOTER (unchanged) -->
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

        <div class="footer-middle-row">
            <div class="footer-social">
                <a href="" class="social-circle">f</a>
                <a href="#" class="social-circle">x</a>
                <a href="#" class="social-circle">▶</a>
                <a href="#" class="social-circle">in</a>
                <a href="#" class="social-circle">P</a>
            </div>
        </div>

        <div class="footer-bottom-row">
            <p>Copyright © 2025 NOVA Fragrance Ltd</p>
            <p>NOVA Fragrance Ltd is registered in England &amp; Wales. This website is for educational use as part of a university project.</p>
        </div>
    </div>
</footer>

<script>
// Password strength indicator (unchanged)
document.getElementById('new_password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = document.getElementById('password-strength');

    let message = '';
    let color   = '#666';

    if (password.length === 0) {
        message = '';
    } else if (password.length < 6) {
        message = 'Too short (minimum 6 characters)';
        color   = '#dc3545';
    } else if (password.length < 8) {
        message = 'Fair';
        color   = '#ffc107';
    } else if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
        message = 'Good';
        color   = '#28a745';
    } else {
        message = 'Strong';
        color   = '#20c997';
    }

    strength.textContent = message;
    strength.style.color = color;
});
</script>

</body>
</html>

