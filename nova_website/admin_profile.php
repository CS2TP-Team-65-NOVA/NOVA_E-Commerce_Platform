<?php
session_start();
require_once 'config.php';

// ---------- 1. PROTECT PAGE – ONLY ADMIN ----------
$role = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? '');
if (!isset($_SESSION['user_id']) || $role !== 'admin') {
    header("Location: login.php");
    exit;
}

$adminId    = $_SESSION['user_id'];
$adminName  = $_SESSION['full_name'] ?? ($_SESSION['user_name'] ?? 'Admin');
$adminEmail = $_SESSION['email'] ?? '';

// ---------- 2. FETCH CURRENT ADMIN DATA ----------
$currentData = [];
$stmt = $conn->prepare("SELECT user_id, full_name, email, created_at FROM users WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result      = $stmt->get_result();
    $currentData = $result->fetch_assoc();
    $stmt->close();
}

// ---------- 3. HANDLE UPDATE PROFILE ----------
$updateMessage = "";
$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateMessage = "Please enter a valid email address.";
    } elseif (empty($full_name)) {
        $updateMessage = "Full name is required.";
    } else {
        // Check if email is already taken by another user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $adminId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $updateMessage = "This email is already registered by another user.";
        } else {
            // Update name + email
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $full_name, $email, $adminId);

            if ($stmt->execute()) {
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email']     = $email;
                $adminName             = $full_name;

                $updateMessage         = "Profile updated successfully!";
                $updateSuccess         = true;

                $currentData['full_name'] = $full_name;
                $currentData['email']     = $email;
            } else {
                $updateMessage = "Failed to update profile. Please try again.";
            }
            $stmt->close();
        }
    }
}

// ---------- 4. HANDLE CHANGE PASSWORD ----------
$passwordMessage = "";
$passwordSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
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
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed_password, $adminId);

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

// ---------- HELPER ----------
function safe($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Belleza font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="admin_style.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<!-- HEADER: same navbar as the rest of the site -->
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

        <!-- RIGHT SIDE (role-based, consistent pattern) -->
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
                <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

                <a href="admin_profile.php" class="account-link active" aria-label="Admin account">
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
        <a href="admin_reviews.php">Manage Reviews</a>
        <a href="admin_profile.php" class="active">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>My Profile</h1>
            <p class="welcome-text">Manage your account information and security settings</p>
        </div>
        
        <div class="profile-container">
            <!-- Left Column: Profile Information & Update -->
            <div>
                <!-- Profile Information Card -->
                <div class="profile-info-card">
                    <h3 style="margin: 0 0 20px 0; color: #2d1b69; font-family: 'Belleza', Arial, sans-serif;">
                        Account Information
                    </h3>
                    
                    <div class="profile-info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value"><?php echo safe($currentData['full_name'] ?? ''); ?></span>
                    </div>
                    
                    <div class="profile-info-item">
                        <span class="info-label">Email Address</span>
                        <span class="info-value"><?php echo safe($currentData['email'] ?? ''); ?></span>
                    </div>
                    
                    <div class="profile-info-item">
                        <span class="info-label">User ID</span>
                        <span class="info-value">#<?php echo safe($currentData['user_id'] ?? ''); ?></span>
                    </div>
                    
                    <div class="profile-info-item">
                        <span class="info-label">Member Since</span>
                        <span class="info-value">
                            <?php echo date('F d, Y', strtotime($currentData['created_at'] ?? date('Y-m-d'))); ?>
                        </span>
                    </div>
                    
                    <div class="profile-info-item">
                        <span class="info-label">Account Type</span>
                        <span class="info-value">Administrator</span>
                    </div>
                </div>
                
                <!-- Update Profile Form -->
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h2>Update Profile</h2>
                    </div>
                    
                    <?php if ($updateMessage !== ""): ?>
                        <div class="message <?php echo $updateSuccess ? 'success' : 'error'; ?>">
                            <?php echo safe($updateMessage); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="admin_profile.php">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" 
                                   value="<?php echo safe($currentData['full_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo safe($currentData['email'] ?? ''); ?>" required>
                        </div>
                        
                        <button type="submit" class="submit-btn">Update Profile</button>
                    </form>
                </div>
            </div>
            
            <!-- Right Column: Change Password -->
            <div>
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h2>Change Password</h2>
                    </div>
                    
                    <?php if ($passwordMessage !== ""): ?>
                        <div class="message <?php echo $passwordSuccess ? 'success' : 'error'; ?>">
                            <?php echo safe($passwordMessage); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="admin_profile.php">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password *</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password *</label>
                            <input type="password" id="new_password" name="new_password" required 
                                   minlength="6"
                                   placeholder="At least 6 characters">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="submit-btn">Change Password</button>
                    </form>
                </div>
            </div>
        </div> <!-- end .profile-container -->
    </main>
</div> <!-- end .admin-layout -->

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
                <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" class="social-icon-link">
                    <img src="facebook_icon_white.png" class="social-icon social-icon-default" alt="Facebook">
                    <img src="active_facebook_icon.png" class="social-icon social-icon-active" alt="Facebook active">
                </a>
                <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" class="social-icon-link">
                    <img src="instagram_icon_white.png" class="social-icon social-icon-default" alt="Instagram">
                    <img src="active_instagram_icon.png" class="social-icon social-icon-active" alt="Instagram active">
                </a>
                <a href="https://www.x.com" target="_blank" rel="noopener noreferrer" class="social-icon-link">
                    <img src="twitter_icon_white.png" class="social-icon social-icon-default" alt="X">
                    <img src="active_twitter_icon.png" class="social-icon social-icon-active" alt="X active">
                </a>
                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="social-icon-link">
                    <img src="youtube_icon_white.png" class="social-icon social-icon-default" alt="YouTube">
                    <img src="active_youtube_icon.png" class="social-icon social-icon-active" alt="YouTube active">
                </a>
            </div>
        </div>

        <!-- BOTTOM: small print -->
        <div class="footer-bottom-row">
            <p>Copyright © 2025 NOVA Fragrance Ltd</p>
            <p>NOVA Fragrance Ltd is registered in England &amp; Wales. This website is for educational use as part of a university project.</p>
        </div>

    </div>
</footer>

<!-- Password strength JS -->
<script>
document.getElementById('new_password').addEventListener('input', function(e) {
    const password = e.target.value;
    let strength   = document.getElementById('password-strength');

    if (!strength) {
        strength = document.createElement('div');
        strength.id = 'password-strength';
        strength.style.fontSize = '12px';
        strength.style.marginTop = '5px';
        e.target.parentNode.appendChild(strength);
    }

    let message = '';
    let color   = '#666';

    if (password.length === 0) {
        message = '';
    } else if (password.length < 6) {
        message = 'Too short (min 6 characters)';
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
<script src="theme.js"></script>
<?php require_once __DIR__ . '/chatbot_include.php'; ?>
</body>
</html>
