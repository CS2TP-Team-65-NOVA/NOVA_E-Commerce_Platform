<?php
session_start();
require_once 'config.php';   // must create $conn (mysqli)

// ---------- 1. PROTECT PAGE – ONLY ADMIN ----------
$role = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? '');
if (!isset($_SESSION['user_id']) || $role !== 'admin') {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION['full_name'] ?? ($_SESSION['user_name'] ?? 'Admin');

// ---------- 2. HANDLE DELETE ----------
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    if ($deleteId > 0) {
        // product_versions and reviews will be deleted automatically (ON DELETE CASCADE)
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $deleteId);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: admin_products.php");
    exit;
}

// ---------- 3. HANDLE ADD PRODUCT ----------
$addMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $category_id = $_POST['category_id'] ?? '';

    if ($name === '' || $price === '') {
        $addMessage = "Please fill in at least product name and price.";
    } else {
        $priceValue = floatval($price);
        if ($priceValue < 0) $priceValue = 0;

        // convert empty category to NULL
        $catVal = ($category_id === '' || $category_id === '0') ? null : (int)$category_id;

        // basic placeholder image name (you can change later)
        $image = 'nova_default.jpg';

        $stmt = $conn->prepare("
            INSERT INTO products (category_id, description, name, price, image)
            VALUES (?, ?, ?, ?, ?)
        ");
        if ($stmt) {
            // category_id can be null
            $stmt->bind_param("issds", $catVal, $description, $name, $priceValue, $image);
            $stmt->execute();
            $stmt->close();

            header("Location: admin_products.php");
            exit;
        } else {
            $addMessage = "Database error while adding product.";
        }
    }
}

// ---------- 4. FETCH CATEGORIES FOR SELECT ----------
$categories = [];
$catResult = mysqli_query($conn, "SELECT category_id, category FROM categories ORDER BY category ASC");
if ($catResult) {
    while ($row = mysqli_fetch_assoc($catResult)) {
        $categories[] = $row;
    }
}

// ---------- 5. FETCH PRODUCTS LIST ----------
$productsSql = "
    SELECT 
        p.product_id,
        p.name,
        p.description,
        p.price AS base_price,
        p.created_at,
        c.category,
        COUNT(DISTINCT v.size_id) AS variants_count,
        MIN(v.price) AS min_version_price,
        MAX(v.price) AS max_version_price
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN product_versions v ON p.product_id = v.product_id
    GROUP BY p.product_id
    ORDER BY p.created_at DESC
";
$productsResult = mysqli_query($conn, $productsSql);

// Dashboard stats
$totalProducts   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$totalVariants   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM product_versions"))['count'];
$avgPrice        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(price) as avg FROM products"))['avg'];
$totalCategories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM categories"))['count'];

// ---------- HELPER ----------
function safe($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Belleza Font (same as other pages) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <!-- Global + admin styles -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="admin_style.css">

    <!-- NOVA favicon -->
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

<!-- HEADER: same navbar as the rest of the site -->
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

        <!-- RIGHT SIDE (role-based, same pattern as other pages) -->
        <div class="nav-right">

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

                <!-- ADMIN: Admin Dashboard link + admin profile + basket -->
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
        <a href="admin_products.php" class="active">Manage Products</a>
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_promotions.php">Manage Promotions</a>
        <a href="admin_reviews.php">Manage Reviews</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Products Management</h1>
            <p class="welcome-text">Manage all perfumes in your store</p>
        </div>
        
        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $totalProducts; ?></div>
                <h3>Total Products</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $totalVariants; ?></div>
                <h3>Variants</h3>
            </div>
            
            <div class="stat-card">
                <div class="number">£<?php echo number_format($avgPrice, 2); ?></div>
                <h3>Avg. Price</h3>
            </div>
            
            <div class="stat-card">
                <div class="number"><?php echo $totalCategories; ?></div>
                <h3>Categories</h3>
            </div>
        </div>
        
        <!-- DASHBOARD CONTENT -->
        <div class="dashboard-content">
            <!-- Products Table -->
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h2>All Products</h2>
                    <span style="color: #666; font-size: 14px;">Newest first</span>
                </div>
                
                <?php if ($productsResult && mysqli_num_rows($productsResult) > 0): ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Variants</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($productsResult)): ?>
                                <tr>
                                    <td>
                                        <div class="product-name"><?php echo safe($row['name']); ?></div>
                                        <div class="product-desc"><?php echo safe($row['description']); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($row['category']): ?>
                                            <span class="category-tag"><?php echo safe($row['category']); ?></span>
                                        <?php else: ?>
                                            <span style="color: #666; font-size: 13px;">Uncategorized</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="price-amount">£<?php echo number_format((float)$row['base_price'], 2); ?></div>
                                        <?php if ($row['min_version_price'] !== null): ?>
                                            <div class="price-range">
                                                Sizes: £<?php echo number_format((float)$row['min_version_price'], 2); ?> – 
                                                £<?php echo number_format((float)$row['max_version_price'], 2); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="variants-badge"><?php echo (int)$row['variants_count']; ?> variants</span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td>
                                        <button class="delete-btn" 
                                            onclick="if(confirm('Delete this product? This will also remove its versions and reviews.')) 
                                            window.location.href='admin_products.php?delete=<?php echo (int)$row['product_id']; ?>'">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No products have been added yet.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Add Product Form -->
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h2>Add Product</h2>
                    <span style="color: #666; font-size: 14px;">Create a new perfume</span>
                </div>
                
                <?php if ($addMessage !== ""): ?>
                    <div class="error-message"><?php echo safe($addMessage); ?></div>
                <?php endif; ?>
                
                <form method="post" action="admin_products.php">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required placeholder="Enter product name">
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Base Price (£) *</label>
                        <input type="number" step="0.01" min="0" id="price" name="price" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">– Select category –</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo (int)$cat['category_id']; ?>">
                                    <?php echo safe($cat['category']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Enter product description"></textarea>
                    </div>
                    
                    <button type="submit" class="add-btn">Add Product</button>
                    
                    <div class="form-note">
                        You can manage sizes, stock and prices for each product
                        in the <strong>product_versions</strong> table or a future
                        "Product Versions" admin page.
                    </div>
                </form>
            </div>
        </div>  
    </main>
</div>

<!-- GLOBAL NOVA FOOTER -->
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
