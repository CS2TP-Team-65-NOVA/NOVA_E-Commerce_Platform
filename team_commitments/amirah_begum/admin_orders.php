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

<div class="admin-layout">

    <div class="sidebar">
        <a href="admin_profile.php">Profile</a>
        <a href="admin_password.php">Change Password</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_products.php">Manage Products</a>
        <a href="admin_orders.php">Manage Orders</a>
    </div>
<main>
    <section class="orders-section">
        <h1>Manage Orders</h1>

        <!-- Orders Table -->
        <div class="orders-table-container">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total ($)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Sample Row -->
                    <tr>
                        <td>#1001</td>
                        <td>Jane Doe</td>
                        <td>2025-12-02</td>
                        <td>$89.99</td>
                        <td>Pending</td>
                        <td>
                            <a href="#" class="edit-link">View</a> |
                            <a href="#" class="ship-link">Mark Shipped</a> |
                            <a href="#" class="delete-link">Cancel</a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </section>
</main>


<footer>

</footer>
<!-- View Order Popup -->
<div id="viewOrderPopup" class="popup-overlay">
    <div class="popup-content">
        <span class="close-btn" id="closeOrderPopup">&times;</span>
        <h2>Order Details</h2>
        <div class="order-details">
            <p><strong>Order ID:</strong> <span id="order_id_view"></span></p>
            <p><strong>Customer:</strong> <span id="customer_view"></span></p>
            <p><strong>Date:</strong> <span id="date_view"></span></p>
            <p><strong>Total:</strong> $<span id="total_view"></span></p>
            <p><strong>Status:</strong> <span id="status_view"></span></p>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewPopup = document.getElementById('viewOrderPopup');
    const closeViewBtn = document.getElementById('closeOrderPopup');

    // View order popup trigger
    document.querySelectorAll('.orders-section .edit-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const row = e.target.closest('tr');
            const orderId = row.children[0].innerText;
            const customer = row.children[1].innerText;
            const date = row.children[2].innerText;
            const total = row.children[3].innerText.replace('$', '');
            const status = row.children[4].innerText;

            // Fill popup
            document.getElementById('order_id_view').innerText = orderId;
            document.getElementById('customer_view').innerText = customer;
            document.getElementById('date_view').innerText = date;
            document.getElementById('total_view').innerText = total;
            document.getElementById('status_view').innerText = status;

            // Show popup
            viewPopup.style.display = 'flex';
        });
    });

    // Close popup
    closeViewBtn.addEventListener('click', () => {
        viewPopup.style.display = 'none';
    });

    window.addEventListener('click', function (e) {
        if (e.target === viewPopup) {
            viewPopup.style.display = 'none';
        }
    });
});
</script>

</body>
</html>