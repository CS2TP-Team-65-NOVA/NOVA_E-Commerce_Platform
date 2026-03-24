<?php
session_start();
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'customer') !== 'admin') {
    header('Location: login.php');
    exit();
}

$addMessage = "";
$error = "";
$delUserError= "";

// Delete User
if (isset($_GET['delete']))  {
    $userDeleteID = (int)$_GET['delete'];

    // Check if user is trying to delete own account. 

    if($userDeleteID === (int)$_SESSION['user_id']) {
        $delUserError = "You cannot delete your account.";
    } else {

    // Check if user is trying to delete an admin account.

        $checkTarget =$conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $checkTarget->bind_param("i", $userDeleteID);
        $checkTarget->execute();
        $checkTargetResult = $checkTarget->get_result();

        if ($targetRole = $checkTargetResult->fetch_assoc()) {

            $role = 'admin';
            if ($targetRole['role'] === $role) {
                $delUserError = "You do not have permission to delete another admin.";

            } else {
                $checkUserOrders =$conn->prepare("SELECT COUNT(*) AS user_total FROM orders WHERE user_id = ?");
                $checkUserOrders->bind_param("i", $userDeleteID);
                $checkUserOrders->execute();
                $checkUserOrdersResult = $checkUserOrders->get_result();

                if ($numOfOrders = $checkUserOrdersResult->fetch_assoc()) {

                    if ((int)$numOfOrders['user_total'] >= 1) {
                        $delUserError = "Unable to delete a user that has an existing order.";

                    } else {
                        $deleteTarget =$conn->prepare("DELETE FROM users WHERE user_id = ?");
                        $deleteTarget->bind_param("i", $userDeleteID);

                    if (!$deleteTarget->execute()) {
                        $delUserError = "Error deleting user.";

                    } else {
                    $delUserError = "User deleted successfully.";
                    }

                    $deleteTarget->close();
            }
        } else {
            $delUserError = "Error checking user orders.";
        }

        $checkUserOrders->close();

    }

    } else {
        $delUserError = "Unable to find user.";
    }

    $checkTarget->close();
    }
}

$stats = [
    'totalUsers' => 0,
    'customers' => 0,
    'admins' => 0,
    'newThisMonth' => 0
];

$totalUsersResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
if ($totalUsersResult) {
    $stats['totalUsers'] = mysqli_fetch_assoc($totalUsersResult)['total'];
}

$customersResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
if ($customersResult) {
    $stats['customers'] = mysqli_fetch_assoc($customersResult)['total'];
}

$adminsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
if ($adminsResult) {
    $stats['admins'] = mysqli_fetch_assoc($adminsResult)['total'];
}

$usersResult = mysqli_query($conn, "
    SELECT 
        u.user_id,
        u.full_name,
        u.email,
        u.role,
        COUNT(o.order_id) AS orders_count
    FROM users u
    LEFT JOIN orders o ON u.user_id = o.user_id
    GROUP BY u.user_id, u.full_name, u.email, u.role
    ORDER BY u.user_id DESC
");


// Create Admin Account

if (isset($_POST['action']) && $_POST['action'] === 'add_admin' ) {

    // Get and trim inputs from the form
  
    $fullName      = trim($_POST['full_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $passwordPlain = trim($_POST['password'] ?? '');

    if ($fullName === '' || $email === '' || $passwordPlain === '') {
        $addMessage = "All fields are required!";
    } else {
        // Hash password (fits into VARCHAR(64))
        $passwordHashed = password_hash($passwordPlain, PASSWORD_DEFAULT);

        // 1) Check if email already exists
        //    Columns must match the DB: email
        $sqlCheck = "SELECT user_id FROM users WHERE email = ? LIMIT 1";

        if ($check = $conn->prepare($sqlCheck)) {
            $check->bind_param('s', $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $addMessage = "Email is already taken.";
            } else {
                // 2) Insert new user
                //    Map to full_name, email, password, role (admin)
                $sqlInsert = "
                    INSERT INTO users (full_name, email, password, role)
                    VALUES (?, ?, ?, 'admin')
                ";
                if ($stmt = $conn->prepare($sqlInsert)) {
                    $stmt->bind_param('sss', $fullName, $email, $passwordHashed);

                    if ($stmt->execute()) {
                        // Success
                        $addMessage = "Successfully created Admin Account."; 

                    } else {
                        $addMessage = "A database error occurred while creating admin account.";
                    }

                    $stmt->close();
                } else {
                    $addMessage = "Could not prepare insert statement.";
                }
            }

            $check->close();
        } else {
            $addMessage = "Could not prepare check statement.";
        }
    }
}

if (isset($_POST['generate_code'])) {
    $_SESSION['generated_admin_code'] = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    $_SESSION['generated_admin_code_time'] = time();
    $_SESSION['show_admin_code'] = true;
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
    <link rel="stylesheet" type="text/css" href="style.css?v=4">
   

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
        <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle theme">

            <!-- MOON (LIGHT MODE) -->
            <img src="dark_mode_icon.png" class="theme-icon theme-icon-moon theme-icon-default" alt="">
            <img src="active_dark_mode_icon.png" class="theme-icon theme-icon-moon theme-icon-active" alt="">

            <!-- SUN (DARK MODE) -->
            <img src="light_mode_icon_white.png" class="theme-icon theme-icon-sun theme-icon-default" alt="">
            <img src="active_light_mode_icon.png" class="theme-icon theme-icon-sun theme-icon-active" alt="">

        </button>
            
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
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_users.php" class="active">Manage Users</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_reviews.php">Manage Reviews</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Users Management</h1>
            <p class="welcome-text">Manage customer and admin accounts</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $stats['totalUsers'] ?? 0; ?></div>
                <h3>Total Users</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $stats['customers'] ?? 0; ?></div>
                <h3>Customers</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $stats['admins'] ?? 0; ?></div>
                <h3>Admins</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $stats['newThisMonth'] ?? 0; ?></div>
                <h3>New This Month</h3>
            </div>
        </div>
        
 <!-- DASHBOARD CONTENT -->
<div class="dashboard-panel">
    <div class="panel-header">
        <h2>All Users</h2>
        <span style="color: #666; font-size: 14px;">Newest first</span>
    </div>

    <?php if (!empty($delUserError)): ?>
        <div class="error-message"><?php echo htmlspecialchars($delUserError); ?></div>
    <?php endif; ?>

    <?php if ($usersResult && mysqli_num_rows($usersResult) > 0): ?>
        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Orders</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($usersResult)): ?>
                        <tr>
                            <td>
                                <div class="product-name"><?php echo htmlspecialchars($row['full_name'] ?? 'Unknown User'); ?></div>
                                <div class="product-desc">
                                    <?php echo htmlspecialchars($row['email'] ?? 'No email'); ?>
                                </div>
                                <div class="product-desc">
                                    <?php echo htmlspecialchars($row['phone_number'] ?? 'No phone number'); ?>
                                </div>
                            </td>
                            <td>
                                <span class="category-tag">
                                    <?php echo htmlspecialchars(ucfirst($row['role'] ?? 'customer')); ?>
                                </span>
                            </td>
                            <td>
                                <span class="variants-badge">
                                    <?php echo (int)($row['orders_count'] ?? 0); ?> orders
                                </span>
                            </td>
                            <td>
                                <button class="delete-btn"
                                    onclick="if(confirm('Delete this user account?')) window.location.href='admin_users.php?delete=<?php echo (int)$row['user_id']; ?>'">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>No users have been added yet.</p>
        </div>
    <?php endif; ?>
</div>

    <!-- Add Admin Form -->
<div class="dashboard-panel">
    <div class="panel-header">
        <h2>Add Admin</h2>
        <span style="color: #666; font-size: 14px;">Create a new admin account</span>
    </div>

    <?php if (!empty($addMessage)): ?>
        <div class="error-message"><?php echo htmlspecialchars($addMessage); ?></div>
    <?php endif; ?>

    <?php if (
        isset($_SESSION['generated_admin_code'], $_SESSION['generated_admin_code_time']) &&
        (time() - $_SESSION['generated_admin_code_time']) < 60
    ): ?>
        <?php
            $remaining = 60 - (time() - $_SESSION['generated_admin_code_time']);
        ?>

        <div class="code-card">

            <!-- helper text -->
            <div class="code-helper">
                Generate a temporary access code for admin registration.
            </div>

            <!-- code row -->
            <div class="code-row">
                <div class="code-label">ACCESS CODE</div>

                <div class="code-value-wrapper">
                    <span class="code-value" id="code">
                        <?php echo $_SESSION['generated_admin_code']; ?>
                    </span>

                    <!-- copy icon -->
                    <span class="copy-icon" onclick="copyCode()">
                        <img src="copy_icon.png" class="copy-default">
                        <img src="active_copy_icon.png" class="copy-active">
                    </span>
                </div>

                <div class="code-timer">
                    Expires in <span id="timer"><?php echo $remaining; ?></span>s
                </div>

                <!-- feedback -->
                <div id="copy-feedback" class="copy-feedback">Copied</div>
            </div>

        </div>

        <script>
        let t = <?php echo $remaining; ?>;
        let timer = document.getElementById("timer");

        let interval = setInterval(() => {
            t--;
            if (t <= 0) {
                clearInterval(interval);
                timer.innerText = "0";
                document.getElementById("code").innerText = "EXPIRED";
            } else {
                timer.innerText = t;
            }
        }, 1000);

        function copyCode() {
            const text = document.getElementById("code").innerText;
            navigator.clipboard.writeText(text);

            const feedback = document.getElementById("copy-feedback");
            feedback.classList.add("show");

            setTimeout(() => {
                feedback.classList.remove("show");
            }, 1200);
        }
        </script>

    <?php endif; ?>

    <!-- BUTTON MOVED TO BOTTOM -->
    <form method="post" style="margin-top: 25px; text-align: center;">
        <button type="submit" name="generate_code" class="generate-btn">
            Generate Access Code
        </button>
    </form>

</div>





    <!-- FOOTER (unchanged) -->
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
            <p>Copyright © 2026 NOVA Fragrance Ltd</p>
            <p>NOVA Fragrance Ltd is registered in England &amp; Wales. This website is for educational use as part of a university project.</p>
        </div>

    </div>
</footer>
<script src="theme.js"></script>
<script>
document.getElementById("generateForm").addEventListener("submit", function(e) {
    e.preventDefault(); // stop reload

    fetch("admin_users.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "generate_code=1"
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");

        const newPanel = doc.querySelectorAll(".dashboard-panel")[1];
        const currentPanel = document.querySelectorAll(".dashboard-panel")[1];

        if (newPanel && currentPanel) {
            currentPanel.innerHTML = newPanel.innerHTML;
        }
    });
});
</script>
</body>
</html>
