<?php
session_start();

// ---- LOG OUT USER ----

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Prevent cached pages from showing protected content after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<!-- Google Belleza Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

<!-- CSS stylesheet -->
<link rel="stylesheet" type="text/css" href="style.css">

<title>Logged Out</title>

<!-- NOVA favicon -->
<link rel="icon" type="image/x-icon" href="nova_favicon.png"/>

<!-- Auto-redirect to Home after 3 seconds -->
<script>
    setTimeout(function() {
        window.location.href = "index.php";
    }, 3000);
</script>

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

    <!-- Guest links after logout -->
    <a href="register.php" class="nav-link">Register</a>
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

<div class="logout-container">

    <div class="logout-header">
        <h1 class="logout-title">You’ve been logged out</h1>
        <p class="logout-subtitle">Thank you for visiting NOVA.</p>
    </div>

    <!-- Fallback button in case JS is disabled -->
    <a href="index.php" class="logout-btn">Return to Home</a>
    <p class="logout-redirect-info">You will be redirected to the home page in 3 seconds.</p>

</div>

</main>

</body>
</html>
