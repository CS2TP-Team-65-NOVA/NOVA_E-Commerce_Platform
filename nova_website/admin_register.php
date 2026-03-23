<?php
session_start();
require_once 'config.php';



$access_granted = false;
$error_message = "";

// Reset access if coming fresh (no POST and not submitting access)
if (!isset($_POST['access_submitted']) && !isset($_POST['submitted'])) {
    unset($_SESSION['admin_access']);
}

// Handle access code submission
if (isset($_POST['access_submitted'])) {
    $entered_code = trim($_POST['access_code'] ?? '');

    if (
    isset($_SESSION['generated_admin_code'], $_SESSION['generated_admin_code_time']) &&
    (time() - $_SESSION['generated_admin_code_time']) < 60 &&
    $entered_code === $_SESSION['generated_admin_code']
) {
    $_SESSION['admin_access'] = true;
    $access_granted = true;

    // invalidate code after use
    unset($_SESSION['generated_admin_code']);
    unset($_SESSION['generated_admin_code_time']);
} else {
    $error_message = "Invalid or expired access code.";
}
}

// Check if already granted
if (isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === true) {
    $access_granted = true;
}

// Handle admin registration ONLY if access granted
if ($access_granted && isset($_POST['submitted'])) {

    $fullName      = trim($_POST['username'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $passwordPlain = trim($_POST['password'] ?? '');

    if ($fullName === '' || $email === '' || $passwordPlain === '') {
        $error_message = "All fields are required!";
    } else {

        $passwordHashed = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $sqlCheck = "SELECT user_id FROM users WHERE full_name = ? OR email = ? LIMIT 1";

        if ($check = $conn->prepare($sqlCheck)) {
            $check->bind_param('ss', $fullName, $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error_message = "Username or email is already taken.";
            } else {

                $sqlInsert = "
                    INSERT INTO users (full_name, email, password, role)
                    VALUES (?, ?, ?, 'admin')
                ";

                if ($stmt = $conn->prepare($sqlInsert)) {
                    $stmt->bind_param('sss', $fullName, $email, $passwordHashed);

                    if ($stmt->execute()) {
                        unset($_SESSION['admin_access']); // reset access after use
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "A database error occurred while creating the admin account.";
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

<title>Admin Register</title>
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

        <!-- MOON (LIGHT MODE) -->
        <img src="dark_mode_icon.png" class="theme-icon theme-icon-moon theme-icon-default" alt="">
        <img src="active_dark_mode_icon.png" class="theme-icon theme-icon-moon theme-icon-active" alt="">

        <!-- SUN (DARK MODE) -->
        <img src="light_mode_icon_white.png" class="theme-icon theme-icon-sun theme-icon-default" alt="">
        <img src="active_light_mode_icon.png" class="theme-icon theme-icon-sun theme-icon-active" alt="">

        </button>
        <?php if (!isset($_SESSION['user_id'])): ?>

            <a href="register.php" class="nav-link active">Register</a>
            <a href="login.php" class="nav-link">Log in</a>

            <a href="shopping_cart.php" class="basket-link">
                <img src="basket_icon.png" class="basket-icon basket-icon-default">
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active">
            </a>

        <?php else: ?>
            <?php $role = $_SESSION['role'] ?? 'customer'; ?>

            <?php if ($role === 'admin'): ?>
                <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

                <a href="admin_profile.php" class="account-link">
                    <img src="account_icon.png" class="account-icon account-icon-default">
                    <img src="active_account_icon.png" class="account-icon account-icon-active">
                </a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active">
                </a>

            <?php else: ?>
                <a href="customer_profile.php" class="account-link">
                    <img src="account_icon.png" class="account-icon account-icon-default">
                    <img src="active_account_icon.png" class="account-icon account-icon-active">
                </a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active">
                </a>
            <?php endif; ?>
        <?php endif; ?>
        </div>

    </nav>
</header>

<main>

<div class="register-container">

<?php if (!$access_granted): ?>

    <!-- ACCESS CODE SCREEN -->
    <form class="register-form" method="post">

        <div class="register-header">
            <h1 class="register-title-inside">Enter Admin Access Code</h1>
            <p class="register-subtitle">Enter your admin access code to continue.</p>
        </div>

        <?php if ($error_message): ?>
        <p class="register-error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <label for="access_code">Access Code:</label>
        <input type="password" id="access_code" name="access_code" class="register-input" placeholder="Access code" required>

        <input type="hidden" name="access_submitted" value="true" />

        <button type="submit" class="register-btn">Continue</button>

        <p class="register-already-user admin-back-link">
            Not an admin?
            <a href="register.php">Go back</a>
        </p>

    </form>

<?php else: ?>

    <!-- ADMIN REGISTER FORM -->
    <form class="register-form" action="admin_register.php" method="post">

        <div class="register-header">
            <h1 class="register-title-inside">Create your Admin account</h1>
            <p class="register-subtitle">Register to create your NOVA Admin account.</p>
        </div>

        <?php if ($error_message): ?>
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

        <p class="register-already-user admin-back-link">
            Not an admin?
            <a href="register.php">Go back</a>
        </p>

    </form>

<?php endif; ?>

</div>

</main>

<footer class="nova-footer">
    <div class="nova-footer-inner">
        <div class="footer-top-row">
            <div class="footer-col">
                <h4>Help</h4>
                <a href="contact.php">Contact Us</a>
                <a href="#">Accessibility Statement</a>
            </div>
        </div>
    </div>
</footer>
<script src="theme.js"></script>
<?php require_once _DIR_ . '/chatbot_include.php'; ?>
</body>
</html>