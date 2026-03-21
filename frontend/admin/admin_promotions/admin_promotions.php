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
        <a href="admin_reviews.php">Manage Reviews</a> 
    </div>
<main>
<main>
    <section class="promotions-section">
        <h1>Manage Promotions</h1>

        <!-- Add New Promotion Form -->
        <form class="promotions-form" action="add_promotion.php" method="POST">

            <label for="promo_title">Promotion Title</label>
            <input type="text" id="promo_title" name="promo_title" placeholder="e.g. Winter Sale 20% OFF" required>

            <label for="promo_code">Promo Code</label>
            <input type="text" id="promo_code" name="promo_code" placeholder="e.g. WINTER20" required>

            <label for="discount">Discount (%)</label>
            <input type="number" id="discount" name="discount" placeholder="e.g. 20" min="1" max="100" required>

            <button type="submit">Add Promotion</button>
        </form>

        <!-- Existing Promotions Table -->
        <div class="promotions-table-container">
            <h2>Existing Promotions</h2>

            <table class="product-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Sample row -->
                    <tr>
                        <td>Winter Sale</td>
                        <td>WINTER20</td>
                        <td>20%</td>
                        <td>
                            <a href="#" class="edit-link">Edit</a> |
                            <a href="#" class="delete-link">Delete</a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </section>


</main>

<footer>

</footer>
<!-- Edit Promotion Popup -->
<div id="editPopup" class="popup-overlay">
    <div class="popup-content">
        <span class="close-btn" id="closePopup">&times;</span>
        <h2>Edit Promotion</h2>
        <form id="editForm"  class="promotions-form" action="edit_promotion.php" method="POST">
            <input type="hidden" id="edit_id" name="promo_id">

            <label for="edit_title">Promotion Title</label>
            <input type="text" id="edit_title" name="promo_title" required>

            <label for="edit_code">Promo Code</label>
            <input type="text" id="edit_code" name="promo_code" required>

            <label for="edit_discount">Discount (%)</label>
            <input type="number" id="edit_discount" name="discount" min="1" max="100" required>

            <button type="submit">Update Promotion</button>
        </form>
    </div>
</div>

<script>
    // Open popup and pre-fill data
    document.querySelectorAll('.edit-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Replace with actual data from row
            const row = e.target.closest('tr');
            const title = row.children[0].innerText;
            const code = row.children[1].innerText;
            const discount = row.children[2].innerText.replace('%', '');

            // Fill form fields
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_code').value = code;
            document.getElementById('edit_discount').value = discount;

            // Set promo ID if available (e.g., data attribute)
            // document.getElementById('edit_id').value = row.dataset.promoId;

            // Show popup
            document.getElementById('editPopup').style.display = 'flex';
        });
    });

    // Close popup
    document.getElementById('closePopup').addEventListener('click', function () {
        document.getElementById('editPopup').style.display = 'none';
    });

    // Optional: Close popup on outside click
    window.addEventListener('click', function(e) {
        const popup = document.getElementById('editPopup');
        if (e.target === popup) {
            popup.style.display = 'none';
        }
    });
</script>

</body>
</html>

