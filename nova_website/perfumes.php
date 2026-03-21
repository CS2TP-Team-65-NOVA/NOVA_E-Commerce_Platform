<?php
session_start();
require_once 'config.php'; // provides $conn

// -------------------------------------------------
// 1. Read filter + sort + pagination + search
// -------------------------------------------------
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

$allowedSorts = [
    'relevant',
    'price_low',
    'price_high',
    'name_az',
    'name_za',
    'best_seller',
    'top_rated',
    'newest'
];

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevant';
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'relevant';
}

$page = (isset($_GET['page']) && ctype_digit($_GET['page']) && (int)$_GET['page'] > 0)
    ? (int)$_GET['page']
    : 1;

$perPage = 9;
$offset  = ($page - 1) * $perPage;

// search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// -------------------------------------------------
// 2. Get all categories for dropdown
// -------------------------------------------------
$categories = [];
$catSql = "SELECT category_id, category FROM categories ORDER BY category";
if ($catRes = $conn->query($catSql)) {
    while ($row = $catRes->fetch_assoc()) {
        $categories[] = $row;
    }
}

// -------------------------------------------------
// 3. Build WHERE clause for category + search
// -------------------------------------------------
$conditions = [];

if ($selectedCategory !== 'all') {
    $catId = (int)$selectedCategory;
    $conditions[] = "p.category_id = {$catId}";
}

if ($search !== '') {
    $searchEscaped = $conn->real_escape_string($search);
    $conditions[] = "p.name LIKE '%{$searchEscaped}%'";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

// -------------------------------------------------
// 4. Total count (for pagination)
// -------------------------------------------------
$totalProducts = 0;
$countSql = "SELECT COUNT(*) AS cnt FROM products p {$whereClause}";
if ($countRes = $conn->query($countSql)) {
    if ($countRow = $countRes->fetch_assoc()) {
        $totalProducts = (int)$countRow['cnt'];
    }
}
$totalPages = max(1, (int)ceil($totalProducts / $perPage));

// -------------------------------------------------
// 5. Sorting: build ORDER BY clause
// -------------------------------------------------
$ratingsSubquery = "
    SELECT
        product_id,
        AVG(rating) AS avg_rating,
        COUNT(*)    AS review_count
    FROM reviews
    GROUP BY product_id
";

switch ($sort) {
    case 'price_low':
        $orderBy = "p.price ASC";
        break;
    case 'price_high':
        $orderBy = "p.price DESC";
        break;
    case 'name_az':
        $orderBy = "p.name ASC";
        break;
    case 'name_za':
        $orderBy = "p.name DESC";
        break;
    case 'best_seller':
        $orderBy = "COALESCE(r.review_count,0) DESC, p.name ASC";
        break;
    case 'top_rated':
        $orderBy = "COALESCE(r.avg_rating,0) DESC, COALESCE(r.review_count,0) DESC, p.name ASC";
        break;
    case 'newest':
        $orderBy = "p.created_at DESC";
        break;
    case 'relevant':
    default:
        $orderBy = "p.product_id ASC";
        break;
}

// -------------------------------------------------
// 6. Product list with category + ratings info
// -------------------------------------------------
$productSql = "
    SELECT 
        p.*,
        c.category,
        COALESCE(r.avg_rating, 0)   AS avg_rating,
        COALESCE(r.review_count, 0) AS review_count
    FROM products p
    LEFT JOIN categories c
        ON p.category_id = c.category_id
    LEFT JOIN ({$ratingsSubquery}) r
        ON p.product_id = r.product_id
    {$whereClause}
    ORDER BY {$orderBy}
    LIMIT {$perPage} OFFSET {$offset}
";
$productRes = $conn->query($productSql);

// -------------------------------------------------
// 7. Helper for page URLs (keep category + sort + search)
// -------------------------------------------------
function build_page_url($page, $category, $sort, $search)
{
    $params = ['page' => $page];

    if ($category !== 'all') {
        $params['category'] = $category;
    }

    if ($sort && $sort !== 'relevant') {
        $params['sort'] = $sort;
    }

    if ($search !== '') {
        $params['search'] = $search;
    }

    return 'perfumes.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Perfumes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <!-- Global site styles -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>
<body>

    <header id="main-header">
        <nav id="navbar">

            <!-- LEFT SIDE: Home, About, Perfumes -->
            <div class="nav-left">
                <a href="index.php" class="nav-link">Home</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="perfumes.php" class="nav-link active">Perfumes</a>
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

<main class="shop-container">

    <h1 class="shop-title">Shop Perfumes</h1>

    <!-- Filter / sort / search bar -->
    <form method="get" action="perfumes.php" class="filter-bar">

        <div class="filter-group">
            <label for="category">Filter Products:</label>
            <select name="category" id="category">
                <option value="all" <?php echo ($selectedCategory === 'all') ? 'selected' : ''; ?>>
                    All categories
                </option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo (int)$cat['category_id']; ?>"
                        <?php echo ($selectedCategory == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="sort">Sort perfumes:</label>
            <select name="sort" id="sort">
                <option value="relevant"   <?php echo $sort === 'relevant'   ? 'selected' : ''; ?>>Most relevant</option>
                <option value="price_low"  <?php echo $sort === 'price_low'  ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="name_az"    <?php echo $sort === 'name_az'    ? 'selected' : ''; ?>>Name: A–Z</option>
                <option value="name_za"    <?php echo $sort === 'name_za'    ? 'selected' : ''; ?>>Name: Z–A</option>
                <option value="best_seller"<?php echo $sort === 'best_seller'? 'selected' : ''; ?>>Best seller</option>
                <option value="top_rated"  <?php echo $sort === 'top_rated'  ? 'selected' : ''; ?>>Top rated</option>
                <option value="newest"     <?php echo $sort === 'newest'     ? 'selected' : ''; ?>>Newest</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="search">Search:</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search perfumes..."
                value="<?php echo htmlspecialchars($search); ?>"
            >
        </div>

        <div class="filter-actions">
            <button type="submit" class="apply-btn">Apply</button>
            <a href="perfumes.php" class="clear-btn">Clear</a>
        </div>

    </form>

    <!-- Product grid -->
    <section class="products-grid">
        <?php if ($productRes && $productRes->num_rows > 0): ?>
            <?php while ($p = $productRes->fetch_assoc()): ?>
                <?php
                    // Find a default in-stock size for quick add-to-cart
                    $productId      = (int)$p['product_id'];
                    $defaultSizeId  = null;

                    if ($stmtSize = $conn->prepare("
                        SELECT v.size_id
                        FROM product_versions v
                        LEFT JOIN inventory i ON v.size_id = i.size_id
                        WHERE v.product_id = ?
                          AND (i.status IS NULL OR i.status <> 'out_of_stock')
                        ORDER BY v.price ASC
                        LIMIT 1
                    ")) {
                        $stmtSize->bind_param('i', $productId);
                        $stmtSize->execute();
                        $sizeRes = $stmtSize->get_result();
                        if ($sizeRow = $sizeRes->fetch_assoc()) {
                            $defaultSizeId = (int)$sizeRow['size_id'];
                        }
                        $stmtSize->close();
                    }
                ?>
                <article class="product-card">
                    <div class="product-img-wrapper">
                        <div class="product-actions">
                            <!-- Favourite (localStorage-based) -->
                            <button
                                type="button"
                                class="card-icon fav-toggle"
                                data-product-id="<?php echo $productId; ?>"
                                title="Add to favourites"
                            >
                                <span class="heart">&hearts;</span>
                            </button>

                            <!-- Quick add to cart -->
                            <form method="get" action="shopping_cart.php" class="cart-form">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <?php if ($defaultSizeId): ?>
                                    <input type="hidden" name="size_id" value="<?php echo $defaultSizeId; ?>">
                                    <button type="submit" class="card-icon cart-btn" title="Add to basket">
                                        &#128722;
                                    </button>
                                <?php else: ?>
                                    <!-- No in-stock sizes – disable cart icon -->
                                    <button type="button" class="card-icon cart-btn" title="Out of stock" disabled>
                                        &#128722;
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>

                        <?php if (!empty($p['image'])): ?>
                            <img src="images/<?php echo htmlspecialchars($p['image']); ?>"
                                 alt="<?php echo htmlspecialchars($p['name']); ?>">
                        <?php else: ?>
                            <span class="placeholder-text">Image coming soon</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($p['name']); ?></h3>

                        <p class="product-category">
                            <?php echo htmlspecialchars($p['category'] ?: 'Exclusive Perfumes'); ?>
                        </p>

                        <p class="product-price">
                            £<?php echo number_format((float)$p['price'], 2); ?>
                        </p>

                        <div class="card-footer-line">
                            <div class="product-rating">
                                <?php if ($p['review_count'] > 0): ?>
                                    ★ <?php echo number_format($p['avg_rating'], 1); ?>
                                    (<?php echo (int)$p['review_count']; ?>)
                                <?php else: ?>
                                    No reviews yet
                                <?php endif; ?>
                            </div>

                            <!-- FIXED: use ?id= so it matches product_page.php -->
                            <a href="product_page.php?id=<?php echo $productId; ?>" class="view-btn">
                                View
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No perfumes found for your filters/search.</p>
        <?php endif; ?>
    </section>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">

            <?php if ($page > 1): ?>
                <a href="<?php echo htmlspecialchars(build_page_url($page - 1, $selectedCategory, $sort, $search)); ?>">
                    &laquo;
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars(build_page_url($i, $selectedCategory, $sort, $search)); ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?php echo htmlspecialchars(build_page_url($page + 1, $selectedCategory, $sort, $search)); ?>">
                    &raquo;
                </a>
            <?php endif; ?>

        </div>
    <?php endif; ?>

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
