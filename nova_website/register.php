<?php
session_start();
require_once 'config.php';   // uses $conn (mysqli)

// Handle form submission
if (isset($_POST['submitted'])) {

    // Get and trim inputs from the form
    // (field is called "username" in the form, but it maps to full_name in DB)
    $fullName      = trim($_POST['username'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $passwordPlain = trim($_POST['password'] ?? '');

    if ($fullName === '' || $email === '' || $passwordPlain === '') {
        $error_message = "All fields are required!";
    } else {
        // Hash password (fits into VARCHAR(64))
        $passwordHashed = password_hash($passwordPlain, PASSWORD_DEFAULT);

        // 1) Check if name OR email already exists
        //    Columns must match the DB: full_name + email
        $sqlCheck = "SELECT user_id FROM users WHERE full_name = ? OR email = ? LIMIT 1";

        if ($check = $conn->prepare($sqlCheck)) {
            $check->bind_param('ss', $fullName, $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error_message = "Username or email is already taken.";
            } else {
                // 2) Insert new user
                //    Map to full_name, email, password, role (customer by default)
                $sqlInsert = "
                    INSERT INTO users (full_name, email, password, role)
                    VALUES (?, ?, ?, 'customer')
                ";
                if ($stmt = $conn->prepare($sqlInsert)) {
                    $stmt->bind_param('sss', $fullName, $email, $passwordHashed);

                    if ($stmt->execute()) {
                        // Success – send to login
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "A database error occurred while creating your account.";
                    }

                    $stmt->close();
                } else {
                    $error_message = "Could not prepare insert statement.";
                }
            }

            $check->close();
        } else {
            $error_message = "Could not prepare check statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="style.css">

<title>Register</title>
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

            <a href="register.php" class="nav-link active">Register</a>
            <a href="login.php" class="nav-link">Log in</a>

            <a href="shopping_cart.php" class="basket-link">
                <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon">
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon">
            </a>

        <?php else: ?>
            <?php $role = $_SESSION['role'] ?? 'customer'; ?>

            <?php if ($role === 'admin'): ?>
                <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

                <a href="admin_profile.php" class="account-link">
                    <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon">
                    <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon">
                </a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon">
                </a>

            <?php else: ?>
                <a href="customer_profile.php" class="account-link">
                    <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon">
                    <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon">
                </a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon">
                </a>
            <?php endif; ?>
        <?php endif; ?>
        </div>

    </nav>
</header>

<main>

<div class="register-container">
    <form class="register-form" action="register.php" method="post">

        <div class="register-header">
            <h1 class="register-title-inside">Create your NOVA account</h1>
            <p class="register-subtitle">Register to create your NOVA account.</p>
        </div>

        <?php if (isset($error_message)): ?>
        <p class="register-error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" class="register-input" placeholder="Username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="register-input" placeholder="Email address" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" class="register-input" placeholder="Password" required>

        <input type="hidden" name="submitted" value="true" />

        <button type="submit" class="register-btn">Register</button>

        <p class="register-already-user">
            Already a user?
            <a href="login.php">Log in</a>
        </p>

    </form>
</div>

</main>


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
