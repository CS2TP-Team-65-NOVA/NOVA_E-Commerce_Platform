<?php
session_start();
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

<title>Home</title>

<!-- NOVA favicon -->
<link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>

<body>

<!-- FULLSCREEN INTRO VIDEO OVERLAY -->
<div id="intro-overlay">
    <video id="intro-video" autoplay muted playsinline>
        <source src="nova_intro.mp4" type="video/mp4">
    </video>
</div>

<!-- PAGE CONTENT (HIDDEN UNTIL VIDEO ENDS) -->
<div id="page-content" class="page-content">

    <!-- HEADER: logo + dynamic navbar -->
    <header id="main-header">
        <nav id="navbar">

            <!-- LEFT SIDE: Home, About, Perfumes -->
            <div class="nav-left">
                <a href="index.php" class="nav-link active">Home</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="perfumes.php" class="nav-link">Perfumes</a>
            </div>

            <!-- CENTER: NOVA Logo -->
            <a href="index.php" class="logo-link">
                <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
            </a>

            <!-- RIGHT SIDE BASED ON USER SESSION -->
            <div class="nav-right">

            <?php if (!isset($_SESSION['user_id'])): ?>

                <!-- GUEST -->
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

    <!-- HERO SECTION -->
    <main>

        <div class="hero-wrapper">
            <section class="hero">
                <div class="hero-inner">
                    <h1 class="hero-title">Discover Your Scent</h1>
                    <p class="hero-subtitle">Inspired by the art of expression.</p>
                    <a href="register.php" class="hero-btn">Join Now</a>
                </div>
            </section>
        </div>

        <!-- Add more homepage sections here -->

    </main>

    <footer>
        <!-- Your footer content here -->
    </footer>

</div> <!-- END OF #page-content -->

<!-- JAVASCRIPT FOR INTRO VIDEO -->
<script src="intro.js"></script>

</body>
</html>
