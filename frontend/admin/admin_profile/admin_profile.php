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


<!-- SIDEBAR --> 
<div class="admin-layout">

    <div class="sidebar">
        <a href="admin_profile.php">Profile</a>
        <a href="admin_password.php">Change Password</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_reviews.php">Manage Reviews</a> 
    </div>
<main>

<main>
    <section class="profile-section">
        <h1>Admin Profile</h1>

        <form class="profile-form" action="update_profile.php" method="POST" enctype="multipart/form-data">
            <!-- Profile Picture -->
            <label for="profile_picture">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture">

            <!-- Full Name -->
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" placeholder="John Doe" required>

            <!-- Username -->
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="admin_user123" required>

            <!-- Email -->
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="admin@example.com" required>

            <!-- Phone -->
            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" placeholder="+1234567890">

            <!-- Role -->
            <label for="role">Role</label>
            <input type="text" name="role" id="role" value="Administrator" readonly>

            <button type="submit">Save Changes</button>
        </form>
    </section>
</main>



<footer>

</footer>

</body>
</html>