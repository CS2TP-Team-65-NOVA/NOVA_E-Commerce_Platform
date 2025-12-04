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
    <section class="products-section">
        <h1>Manage Products</h1>

        <!-- Add New Product Form -->
        <form class="product-form" action="add_product.php" method="POST" enctype="multipart/form-data">
            
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" placeholder="e.g. Floral Essence" required>

            <label for="description">Description</label>
            <input type="text" id="description" name="description" placeholder="Short product description" required>

            <label for="price">Price ($)</label>
            <input type="number" step="0.01" id="price" name="price" placeholder="e.g. 49.99" required>

            <label for="stock">Stock Quantity</label>
            <input type="number" id="stock" name="stock" placeholder="e.g. 100" required>

            <label for="product_image">Product Image</label>
            <input type="file" id="product_image" name="product_image" accept="image/*">

            <button type="submit">Add Product</button>
        </form>

        <!-- Existing Products Table -->
        <div class="product-table-container">
            <h2>Product List</h2>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Sample row -->
                    <tr>
                        <td>
                            <img src="product_sample.jpg" alt="Product" class="product-image">
                        </td>
                        <td>Floral Essence</td>
                        <td>$49.99</td>
                        <td>100</td>
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

<!-- Edit Product Popup -->
<div id="editProductPopup" class="popup-overlay">
    <div class="popup-content">
        <span class="close-btn" id="closeProductPopup">&times;</span>
        <h2>Edit Product</h2>
        <form id="editProductForm" class="product-form" action="edit_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="edit_product_id" name="product_id">

            <label for="edit_product_name">Product Name</label>
            <input type="text" id="edit_product_name" name="product_name" required>

            <label for="edit_description">Description</label>
            <input type="text" id="edit_description" name="description" required>

            <label for="edit_price">Price ($)</label>
            <input type="number" step="0.01" id="edit_price" name="price" required>

            <label for="edit_stock">Stock Quantity</label>
            <input type="number" id="edit_stock" name="stock" required>

            <label for="edit_product_image">Product Image</label>
            <input type="file" id="edit_product_image" name="product_image" accept="image/*">

            <button type="submit">Update Product</button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productPopup = document.getElementById('editProductPopup');
    const closeProductBtn = document.getElementById('closeProductPopup');

    // Attach event to each "Edit" link
    document.querySelectorAll('.products-section .edit-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const row = e.target.closest('tr');
            const image = row.querySelector('img').getAttribute('src'); // if needed
            const name = row.children[1].innerText;
            const price = row.children[2].innerText.replace('$', '');
            const stock = row.children[3].innerText;

            // Populate form fields
            document.getElementById('edit_product_name').value = name;
            document.getElementById('edit_description').value = ''; // no description in table
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;

            // Show popup
            productPopup.style.display = 'flex';
        });
    });

    // Close popup on click
    closeProductBtn.addEventListener('click', () => {
        productPopup.style.display = 'none';
    });

    window.addEventListener('click', function (e) {
        if (e.target === productPopup) {
            productPopup.style.display = 'none';
        }
    });
});
</script>


</body>
</html>