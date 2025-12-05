<?php
session_start();
require_once 'config.php';

// ------------------------------------------
// 1. Get 3 random featured products
// ------------------------------------------
$ratingsSubquery = "
    SELECT
        product_id,
        AVG(rating) AS avg_rating,
        COUNT(*)    AS review_count
    FROM reviews
    GROUP BY product_id
";

$featuredSql = "
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
    ORDER BY RAND()
    LIMIT 3
";

$featuredRes = $conn->query($featuredSql);
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

    <!-- MAIN CONTENT -->
    <main>

        <!-- HERO SECTION -->
        <div class="hero-wrapper">
            <section class="hero">
                <div class="hero-inner">
                    <h1 class="hero-title">Discover Your Scent</h1>
                    <p class="hero-subtitle">Inspired by the art of expression.</p>
                    <a href="register.php" class="hero-btn">Join Now</a>
                </div>
            </section>
        </div>

             <!-- WHY NOVA SECTION (no image, button under text) -->
        <section class="why-nova">
            <div class="why-nova-inner no-image">

                <div class="why-nova-text">
                    <h2>Why NOVA?</h2>
                    <p>
                        NOVA is more than a perfume – it’s a quiet burst of confidence.
                        Every fragrance is inspired by light, movement and modern rituals.
                        Clean lines, luminous notes and long-lasting wear, crafted to feel
                        like <em>you</em> on your best day.
                    </p>
                    <p>
                        Whether you’re stepping into a lecture, a meeting or a night out,
                        NOVA adds a subtle signature that people remember.
                    </p>
                    <ul class="why-nova-list">
                        <li>Long-lasting yet never overpowering.</li>
                        <li>Cruelty-free and thoughtfully sourced.</li>
                        <li>Modern, minimalist bottle design.</li>
                    </ul>

                    <!-- Contact button styled like hero-btn -->
                    <a href="contact.php" class="hero-btn why-nova-contact-btn">
                        Contact Us
                    </a>
                </div>

            </div>
        </section>


        <!-- FEATURED PRODUCTS SECTION -->
        <section class="home-featured">
            <div class="home-section-header">
                <h2>Featured Perfumes</h2>
                <a href="perfumes.php" class="view-all-link">View all perfumes</a>
            </div>

            <div class="products-grid home-products-grid">
                <?php if ($featuredRes && $featuredRes->num_rows > 0): ?>
                    <?php while ($p = $featuredRes->fetch_assoc()): ?>
                        <?php
                            $productId     = (int)$p['product_id'];
                            $defaultSizeId = null;

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

                                    <a href="product_page.php?id=<?php echo $productId; ?>" class="view-btn">
                                        View
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No perfumes available yet. Please check back soon.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- CUSTOMER REVIEWS SECTION (STATIC) -->
        <section class="home-reviews">
            <div class="home-section-header">
                <h2>Customer Reviews</h2>
                <p class="reviews-subtitle">
                    Real impressions from NOVA wearers.
                </p>
            </div>

            <div class="reviews-grid">
                <article class="review-card">
                    <p class="review-text">
                        “Smells like a luxury fragrance counter but feels lighter and more wearable day-to-day.
                        I’ve had so many compliments.”
                    </p>
                    <p class="review-author">— Aisha, Birmingham</p>
                </article>

                <article class="review-card">
                    <p class="review-text">
                        “Lasts through lectures and evening plans. It’s become my signature scent.”
                    </p>
                    <p class="review-author">— Daniel, London</p>
                </article>

                <article class="review-card">
                    <p class="review-text">
                        “Love the bottle, love the smell, love that it’s cruelty-free. NOVA just feels put-together.”
                    </p>
                    <p class="review-author">— Sofia, Manchester</p>
                </article>
            </div>
        </section>

    </main>

    <!-- FOOTER (unchanged) -->
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

</div> <!-- END OF #page-content -->

<!-- JAVASCRIPT FOR INTRO VIDEO -->
<script src="intro.js"></script>

<!-- FAVOURITES HEARTS JS (same as perfumes.php) -->
<script>
(function () {
    const STORAGE_KEY = 'nova_favourites';

    function loadFavourites() {
        try {
            const raw = window.localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveFavourites(list) {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
        } catch (e) {}
    }

    function updateButtonUI(btn, isActive) {
        if (isActive) {
            btn.classList.add('is-active');
        } else {
            btn.classList.remove('is-active');
        }
    }

    const favourites = loadFavourites();
    const buttons = document.querySelectorAll('.fav-toggle');

    buttons.forEach(btn => {
        const productId = parseInt(btn.dataset.productId, 10);
        const isFav = favourites.includes(productId);
        updateButtonUI(btn, isFav);

        btn.addEventListener('click', () => {
            const idx = favourites.indexOf(productId);
            let nowFav;
            if (idx === -1) {
                favourites.push(productId);
                nowFav = true;
            } else {
                favourites.splice(idx, 1);
                nowFav = false;
            }
            saveFavourites(favourites);
            updateButtonUI(btn, nowFav);
        });
    });
})();
</script>

</body>
</html>
