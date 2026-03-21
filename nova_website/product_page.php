<?php
session_start();
require_once 'config.php'; // gives $conn

// ------------------------------------------------------------------
// 1. Read and validate product id (?id=...)
// ------------------------------------------------------------------
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: perfumes.php');
    exit;
}
$productId = (int)$_GET['id'];

// ------------------------------------------------------------------
// 2. Handle new review submission
// ------------------------------------------------------------------
$reviewError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $reviewError = 'You must be logged in to leave a review.';
    } else {
        $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        if ($rating < 1 || $rating > 5) {
            $reviewError = 'Please select a rating between 1 and 5 stars.';
        } elseif ($comment === '') {
            $reviewError = 'Please enter a comment.';
        } else {
            $userId = (int)$_SESSION['user_id'];

            $stmt = $conn->prepare("
                INSERT INTO reviews (product_id, user_id, rating, comment)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    rating = VALUES(rating),
                    comment = VALUES(comment),
                    updated_at = CURRENT_TIMESTAMP
            ");
            if ($stmt) {
                $stmt->bind_param('iiis', $productId, $userId, $rating, $comment);
                if ($stmt->execute()) {
                    header('Location: product_page.php?id=' . $productId . '#reviews');
                    exit;
                } else {
                    $reviewError = 'Could not save your review. Please try again.';
                }
                $stmt->close();
            } else {
                $reviewError = 'Could not prepare review statement.';
            }
        }
    }
}

// ------------------------------------------------------------------
// 3. Load product details
// ------------------------------------------------------------------
$productSql = "
    SELECT 
        p.*,
        c.category
    FROM products p
    LEFT JOIN categories c
        ON p.category_id = c.category_id
    WHERE p.product_id = ?
    LIMIT 1
";
$product = null;
if ($stmt = $conn->prepare($productSql)) {
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $product = $res->fetch_assoc();
    }
    $stmt->close();
}
if (!$product) {
    header('Location: perfumes.php');
    exit;
}

// ------------------------------------------------------------------
// 4. Load size options (product_versions + inventory)
// ------------------------------------------------------------------
$sizes = [];
$sizeSql = "
    SELECT
        v.size_id,
        v.size_ml,
        v.price,
        i.stock_qty,
        i.status
    FROM product_versions v
    LEFT JOIN inventory i
        ON v.size_id = i.size_id
    WHERE v.product_id = ?
    ORDER BY v.size_ml ASC
";
if ($stmt = $conn->prepare($sizeSql)) {
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $sizes[] = $row;
    }
    $stmt->close();
}

$defaultPrice  = $product['price'];
$defaultSizeId = null;
if (!empty($sizes)) {
    // Prefer 100ml if available, otherwise first size
    $defaultSize = $sizes[0];
    foreach ($sizes as $s) {
        if ((int)$s['size_ml'] === 100) {
            $defaultSize = $s;
            break;
        }
    }
    $defaultPrice  = $defaultSize['price'];
    $defaultSizeId = $defaultSize['size_id'];
}

// ------------------------------------------------------------------
// 5. Load reviews for this product
// ------------------------------------------------------------------
$reviews     = [];
$avgRating   = 0;
$reviewCount = 0;

$reviewSql = "
    SELECT 
        r.rating,
        r.comment,
        r.created_at,
        u.full_name
    FROM reviews r
    INNER JOIN users u
        ON r.user_id = u.user_id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
";
if ($stmt = $conn->prepare($reviewSql)) {
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
}

if (!empty($reviews)) {
    $reviewCount = count($reviews);
    $sum = 0;
    foreach ($reviews as $rv) {
        $sum += (int)$rv['rating'];
    }
    $avgRating = $sum / $reviewCount;
}

// ------------------------------------------------------------------
// 6. "You may also like" – other products from same category
// ------------------------------------------------------------------
$related = [];
$relatedSql = "
    SELECT product_id, name, price, image
    FROM products
    WHERE category_id = ? AND product_id <> ?
    ORDER BY created_at DESC
    LIMIT 3
";
if ($stmt = $conn->prepare($relatedSql)) {
    $catId = (int)$product['category_id'];
    $stmt->bind_param('ii', $catId, $productId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $related[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">


    <!-- Global styles -->
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

<main class="product-page-container">

    <!-- MAIN PRODUCT SECTION (two-column) -->
    <section class="product-main">
        <!-- LEFT: gallery -->
        <div class="product-gallery">
            <div class="product-main-image">
                <?php if (!empty($product['image'])): ?>
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <span class="placeholder-text">Image coming soon</span>
                <?php endif; ?>
            </div>

            <div class="product-thumbs">
                <?php
                $images = [];
                if (!empty($product['image']))   $images[] = $product['image'];
                if (!empty($product['image_2'])) $images[] = $product['image_2'];
                if (!empty($product['image_3'])) $images[] = $product['image_3'];
                if (!empty($product['image_4'])) $images[] = $product['image_4'];
                if (!empty($product['image_5'])) $images[] = $product['image_5'];

                foreach ($images as $img):
                ?>
                    <button type="button" class="thumb-btn" data-img="images/<?php echo htmlspecialchars($img); ?>">
                        <img src="images/<?php echo htmlspecialchars($img); ?>"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RIGHT: details + add to bag -->
        <div class="product-detail-panel">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

            <p class="product-breadcrumb">
                <?php echo htmlspecialchars($product['category'] ?: 'Exclusive Perfumes'); ?>
            </p>

            <?php if (!empty($product['fragrance_family'])): ?>
                <p class="product-fragrance-family">
                    <?php echo htmlspecialchars($product['fragrance_family']); ?>
                </p>
            <?php endif; ?>

            <div class="product-rating-summary">
                <?php if ($reviewCount > 0): ?>
                    ★ <?php echo number_format($avgRating, 1); ?>
                    (<?php echo $reviewCount; ?> reviews)
                <?php else: ?>
                    No reviews yet
                <?php endif; ?>
            </div>

            <p class="product-price-large">
                £<span id="js-price"><?php echo number_format((float)$defaultPrice, 2); ?></span>
            </p>

            <!-- ADD TO BAG FORM -->
            <form method="get" action="shopping_cart.php" class="product-purchase-form">
                <!-- For cart logic -->
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                <!-- This hidden field is what cart will use for size -->
                <input type="hidden" name="size_id" id="js-size-id" value="<?php echo (int)$defaultSizeId; ?>">

                <?php if (!empty($sizes)): ?>
                    <label for="js-size-select">Size</label>
                    <select id="js-size-select" name="size_display">
                        <?php foreach ($sizes as $s): ?>
                            <?php
                                $status = $s['status'] ?? 'in_stock';
                                $disabled = ($status === 'out_of_stock') ? 'disabled' : '';
                                $selected = ($s['size_id'] == $defaultSizeId) ? 'selected' : '';
                            ?>
                            <option
                                value="<?php echo (int)$s['size_id']; ?>"
                                data-price="<?php echo number_format((float)$s['price'], 2, '.', ''); ?>"
                                <?php echo $selected . ' ' . $disabled; ?>
                            >
                                <?php
                                echo (int)$s['size_ml'] . ' ml';
                                if ($status === 'out_of_stock') {
                                    echo ' – Out of stock';
                                } elseif ($status === 'low_stock') {
                                    echo ' – Low stock (' . (int)$s['stock_qty'] . ' left)';
                                }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <label for="qty">Quantity</label>
                <input type="number" id="qty" name="quantity" min="1" value="1">

                <div class="product-purchase-actions">
                    <button type="submit" class="btn-primary">Add to bag</button>

                    <!-- main favourite button -->
                    <button
                        type="button"
                        class="fav-toggle card-icon"
                        data-product-id="<?php echo $productId; ?>"
                        title="Add to favourites"
                    >
                        <span class="heart">&hearts;</span>
                    </button>
                </div>
            </form>

        </div>
    </section>

    <!-- FULL-WIDTH PRODUCT INFORMATION SECTION -->
    <section class="product-info-section">
        <h2 class="info-title">Product Information</h2>

        <?php if (!empty($product['short_description'])): ?>
            <p class="info-paragraph">
                <?php echo nl2br(htmlspecialchars($product['short_description'])); ?>
            </p>
        <?php endif; ?>

        <div class="info-block">
            <?php if (!empty($product['top_notes'])): ?>
                <p><strong>Top notes:</strong> <?php echo htmlspecialchars($product['top_notes']); ?></p>
            <?php endif; ?>

            <?php if (!empty($product['heart_notes'])): ?>
                <p><strong>Heart notes:</strong> <?php echo htmlspecialchars($product['heart_notes']); ?></p>
            <?php endif; ?>

            <?php if (!empty($product['base_notes'])): ?>
                <p><strong>Base notes:</strong> <?php echo htmlspecialchars($product['base_notes']); ?></p>
            <?php endif; ?>

            <?php if (!empty($product['launch_info'])): ?>
                <p><strong>Launch:</strong> <?php echo htmlspecialchars($product['launch_info']); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($product['scent_story'])): ?>
            <h3 class="info-subtitle">Scent story</h3>
            <p class="info-paragraph">
                <?php echo nl2br(htmlspecialchars($product['scent_story'])); ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($product['design_story'])): ?>
            <h3 class="info-subtitle">Design story</h3>
            <p class="info-paragraph">
                <?php echo nl2br(htmlspecialchars($product['design_story'])); ?>
            </p>
        <?php endif; ?>
    </section>

    <!-- YOU MAY ALSO LIKE – using same product-card grid as perfumes.php -->
    <?php if (!empty($related)): ?>
        <section class="related-products">
            <h2 class="related-title">You may also like</h2>

            <div class="products-grid">
                <?php foreach ($related as $rp): ?>
                    <?php
                        $relProductId = (int)$rp['product_id'];
                        $relDefaultSizeId = null;

                        // same default size logic as perfumes.php (for quick add to cart)
                        if ($stmtSize = $conn->prepare("
                            SELECT v.size_id
                            FROM product_versions v
                            LEFT JOIN inventory i ON v.size_id = i.size_id
                            WHERE v.product_id = ?
                              AND (i.status IS NULL OR i.status <> 'out_of_stock')
                            ORDER BY v.price ASC
                            LIMIT 1
                        ")) {
                            $stmtSize->bind_param('i', $relProductId);
                            $stmtSize->execute();
                            $sizeRes = $stmtSize->get_result();
                            if ($sizeRow = $sizeRes->fetch_assoc()) {
                                $relDefaultSizeId = (int)$sizeRow['size_id'];
                            }
                            $stmtSize->close();
                        }
                    ?>
                    <article class="product-card">
                        <div class="product-img-wrapper">
                            <div class="product-actions">
                                <!-- favourite -->
                                <button
                                    type="button"
                                    class="card-icon fav-toggle"
                                    data-product-id="<?php echo $relProductId; ?>"
                                    title="Add to favourites"
                                >
                                    <span class="heart">&hearts;</span>
                                </button>

                                <!-- quick add to cart -->
                                <form method="get" action="shopping_cart.php" class="cart-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $relProductId; ?>">
                                    <?php if ($relDefaultSizeId): ?>
                                        <input type="hidden" name="size_id" value="<?php echo $relDefaultSizeId; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="card-icon cart-btn" title="Add to basket">
                                            &#128722;
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="card-icon cart-btn" title="Out of stock" disabled>
                                            &#128722;
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <?php if (!empty($rp['image'])): ?>
                                <img src="images/<?php echo htmlspecialchars($rp['image']); ?>"
                                     alt="<?php echo htmlspecialchars($rp['name']); ?>">
                            <?php else: ?>
                                <span class="placeholder-text">Image coming soon</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($rp['name']); ?></h3>

                            <p class="product-category">
                                From £<?php echo number_format($rp['price'], 2); ?>
                            </p>

                            <div class="card-footer-line">
                                <a href="product_page.php?id=<?php echo $relProductId; ?>" class="view-btn">
                                    View
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- REVIEWS -->
    <section id="reviews" class="product-reviews">
        <h2>Customer reviews</h2>

        <?php if ($reviewError): ?>
            <p class="form-error"><?php echo htmlspecialchars($reviewError); ?></p>
        <?php endif; ?>

        <?php if (!empty($reviews)): ?>
            <ul class="review-list">
                <?php foreach ($reviews as $rv): ?>
                    <li class="review-item">
                        <div class="review-header">
                            <strong><?php echo htmlspecialchars($rv['full_name']); ?></strong>
                            <span class="review-rating">★ <?php echo (int)$rv['rating']; ?>/5</span>
                        </div>
                        <p class="review-comment">
                            <?php echo nl2br(htmlspecialchars($rv['comment'])); ?>
                        </p>
                        <p class="review-date">
                            <?php echo htmlspecialchars($rv['created_at']); ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this perfume.</p>
        <?php endif; ?>

        <div class="review-form-wrapper">
            <h3>Write a review</h3>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <p>You must be logged in to leave a review.</p>
            <?php else: ?>
                <form method="post" class="review-form">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select…</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" rows="4" required></textarea>

                    <button type="submit" name="add_review" class="add-review-btn">Submit review</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

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
