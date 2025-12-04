<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<!-- CSS stylesheet -->
<link rel="stylesheet" type="text/css" href="style.css">

<title>Home</title>

<!-- NOVA favicon -->
<link rel="icon" type="image/x-icon" href="nova_favicon.jpg"/>
</head>

<body>

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
<img src="nova_logo_black.jpg" id="logo" alt="NOVA Logo">
</a>

<!-- RIGHT SIDE: depends on user role -->
<div class="nav-right">

<?php if (!isset($_SESSION['user_id'])): ?>

<!-- GUEST: Register / Log in / Basket -->
<a href="register.php" class="nav-link">Register</a>
<a href="login.php" class="nav-link">Log in</a>

<a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">

<!-- default black icon -->
<img src="basket_icon.jpg"
class="basket-icon basket-icon-default"
alt="Basket icon" />

<!-- purple active icon -->
<img src="active_basket_icon.jpg"
class="basket-icon basket-icon-active"
alt="Active basket icon" />
</a>

<?php else: ?>
<?php $role = $_SESSION['role'] ?? 'customer'; ?>

<?php if ($role === 'admin'): ?>

<!-- ADMIN: Admin Dashboard / Account / Basket -->
<a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

<a href="admin_profile.php" class="account-link" aria-label="Admin account">
                        
<!-- default black icon -->
<img src="account_icon.jpg"
class="account-icon account-icon-default"
alt="Account icon" />

<!-- purple active icon -->
<img src="active_account_icon.jpg"
class="account-icon account-icon-active"
alt="Active account icon" />
</a>

<a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                        
<!-- default black icon -->
<img src="basket_icon.jpg"
class="basket-icon basket-icon-default"
alt="Basket icon" />
                        
<!-- purple active icon -->
<img src="active_basket_icon.jpg"
class="basket-icon basket-icon-active"
alt="Active basket icon" />
</a>

<?php else: ?>
<!-- CUSTOMER: Account / Basket -->
<a href="customer_profile.php" class="account-link" aria-label="My account">

<!-- default black icon -->
<img src="account_icon.jpg"
class="account-icon account-icon-default"
alt="Account icon" />
                        
<!-- purple active icon -->
<img src="active_account_icon.jpg"
class="account-icon account-icon-active"
alt="Active account icon" />
</a>

<a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                        
<!-- default black icon -->
<img src="basket_icon.jpg"
class="basket-icon basket-icon-default"
alt="Basket icon" />

<!-- purple active icon -->
<img src="active_basket_icon.jpg"
class="basket-icon basket-icon-active"
alt="Active basket icon" />
</a>

<?php endif; ?>
<?php endif; ?>

</div>

</nav>
</header>

<main>
    

<!-- SIDEBAR --> 
 <div class="sidebar"> 
    <a href="admin_profile.php">Profile</a> 
    <a href="admin_password.php">Change Password</a> 
    <a href="admin_promotions.php">Manage Promotions</a> 
    <a href="admin_products.php">Manage Products</a> 
    <a href="admin_orders.php">Manage Orders</a> 
</div>

    <!-- MAIN CONTENT -->
    <div class="content">

        <!-- PROFILE PAGE -->
       

        <!-- PASSWORD PAGE -->
        <div id="password" class="page">
            <h1>Change Password</h1>
            <p>This is the change password page.</p>
        </div>

        <!-- PROMOTIONS PAGE -->
        <div id="promotions" class="page">
            <h1>Manage Promotions</h1>
            <p>This is where promotions are managed.</p>
        </div>

        <!-- PRODUCTS PAGE -->
        <div id="products" class="page">
            <h1>Manage Products</h1>
            <p>This is where products are managed.</p>
        </div>

        <!-- ORDERS PAGE -->
        <div id="orders" class="page">
            <h1>Manage Orders</h1>
            <p>This is where orders are viewed.</p>
        </div>

    </div>



</main>
<footer>

</footer>

</body>
</html>