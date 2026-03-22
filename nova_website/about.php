<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="style.css">

<title>About</title>
<link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>

<body>

<header id="main-header">
    <nav id="navbar">

        <!-- LEFT SIDE -->
        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link active">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>

        <!-- CENTER LOGO -->
        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
        </a>

        <!-- RIGHT SIDE -->
        <div class="nav-right">
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle theme">
               <span id="theme-icon">🌙</span>
            </button>
        <?php if (!isset($_SESSION['user_id'])): ?>

            <a href="register.php" class="nav-link">Register</a>
            <a href="login.php" class="nav-link">Log in</a>

            <a href="shopping_cart.php" class="basket-link">
                <img src="basket_icon.png" class="basket-icon basket-icon-default">
                <img src="active_basket_icon.png" class="basket-icon basket-icon-active">
            </a>

        <?php else: ?>
            <?php $role = $_SESSION['role'] ?? 'customer'; ?>

            <?php if ($role === 'admin'): ?>

                <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

                <a href="admin_profile.php" class="account-link">
                    <img src="account_icon.png" class="account-icon account-icon-default">
                    <img src="active_account_icon.png" class="account-icon account-icon-active">
                </a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active">
                </a>

            <?php else: ?>

                <a href="customer_profile.php" class="account-link">
                    <img src="account_icon.png" class="account-icon account-icon-default">
                    <img src="active_account_icon.png" class="account-icon account-icon-active">
                </a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active">
                </a>

            <?php endif; ?>
        <?php endif; ?>

        </div>

    </nav>
</header>



<!-- BANNER (updated to match home hero structure) -->
<div class="about-hero-wrapper">
    <section class="about-hero"></section>
</div>

<main>

    <!-- OUR STORY -->
    <section class="about-story-section">
        <div class="about-story-inner">

            <div class="about-story-main">
                <h2 class="about-heading">Our Story</h2>

                <p class="about-lead">
                    NOVA was born from a simple idea: fragrance should feel like a fresh start,
                    not just the final step before you leave the house. We wanted scents that feel
                    luminous, modern and effortless.
                </p>

                <p>
                    The name <strong>“NOVA”</strong> comes from a stellar phenomenon, a sudden,
                    brilliant burst of light in the night sky. For us, it represents
                    <em>new beginnings, confidence and that quiet spark of identity</em>
                    when you find a scent that truly feels like you.
                </p>

                <p>
                    Every NOVA fragrance is inspired by real moments: city nights, morning light,
                    quiet celebrations and everything in between. Clean lines, modern accords
                    and a warm touch, future-facing yet timeless.
                </p>
            </div>

            <aside class="about-story-image">
                <img src="nova_register_promo.png" alt="NOVA perfume bottle">
            </aside>
        </div>
    </section>

    <!-- VALUES -->
    <section class="about-values-section">
        <div class="about-values-inner">

            <h2 class="about-heading">Values</h2>
            <p class="about-values-lead">
                Nova blends creativity with intention. Every scent feels radiant, refined, and personal.
            </p>

            <div class="values-grid">

                <div class="value-card">
                    <h3>Premium Ingredients</h3>
                    <p>High-grade aroma molecules and naturals for purity and balance.</p>
                </div>

                <div class="value-card">
                    <h3>Long-Lasting Formulas</h3>
                    <p>Engineered for endurance without overwhelming intensity.</p>
                </div>

                <div class="value-card">
                    <h3>Cruelty-Free & Sustainable</h3>
                    <p>Animal-free testing, recyclable packaging, ethical sourcing.</p>
                </div>

                <div class="value-card">
                    <h3>Confidence in Every Bottle</h3>
                    <p>Fragrances crafted for identity, expression and self-belief.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- UNIQUE -->
    <section class="about-unique-section">
        <div class="about-unique-inner">

            <h2 class="about-heading">What Makes Nova Unique</h2>
            <p class="about-unique-lead">
                NOVA feels like a new constellation — familiar enough to love instantly, distinct enough to remember.
            </p>

            <div class="unique-grid">

                <div class="unique-point">
                    <h3>Celestial Inspiration</h3>
                    <p>Night skies, light bursts and constellations translated into perfume experiences.</p>
                </div>

                <div class="unique-point">
                    <h3>Luxury, Made Accessible</h3>
                    <p>Premium fragrance without the traditional luxury price barrier.</p>
                </div>

                <div class="unique-point">
                    <h3>Minimalist, Futuristic Design</h3>
                    <p>Precision, symmetry and modern silhouettes built for your shelf.</p>
                </div>

                <div class="unique-point">
                    <h3>Art Meets Science</h3>
                    <p>Creative vision blended with chemistry and meticulous formulation.</p>
                </div>

            </div>

        </div>
    </section>

    <!-- CRAFT -->
    <section class="about-craft-section">
        <div class="about-craft-inner">

            <h2 class="about-heading">Craftsmanship</h2>
            <p class="about-craft-lead">
                Every Nova fragrance is imagined thoughtfully and crafted with precision.
            </p>

            <div class="craft-grid">

                <div class="craft-card">
                    <h3>Scent Development</h3>
                    <p>From inspiration → formulation → refinement using high-grade aroma technologies.</p>
                </div>

                <div class="craft-card">
                    <h3>Ingredient Sourcing</h3>
                    <p>Partners selected for quality, ethical harvesting and traceability.</p>
                </div>

                <div class="craft-card">
                    <h3>Intentional Blending</h3>
                    <p>Small-batch production, tested for balance, longevity and performance.</p>
                </div>

                <div class="craft-card">
                    <h3>Design Experience</h3>
                    <p>Everything matters — bottle weight, silhouette, trigger resistance, unboxing emotion.</p>
                </div>

            </div>
        </div>
    </section>
    <!-- MEET OUR TEAM -->
    <section class="about-team-section">

        <div class="about-team-inner">

            <!-- LEFT SIDE SLIDESHOW -->
            <div class="team-slideshow">

                <div class="team-slide active">
                    <img src="alex.png" alt="Team Member">
                    <h4>Alex</h4>
                </div>

            <div class="team-slide">
                <img src="shreya.png" alt="Team Member">
                <h4>Shreya Kathiria</h4>
            </div>

            <div class="team-slide">
                <img src="malaika.jpg" alt="Team Member">
                <h4>Malika</h4>
            </div>

        </div>


        <!-- RIGHT SIDE TEXT -->
        <div class="about-team-text">

            <h2 class="about-heading">Meet Our Team</h2>

            <p class="about-lead">
                Behind NOVA is a passionate group of designers, developers and creators
                who believe fragrance should feel personal and meaningful.
            </p>

            <p>
                Our team combines creativity, science and craftsmanship to design
                scents that express identity and emotion. From fragrance formulation
                to bottle design, every detail is shaped by collaboration and
                innovation.
            </p>

            <p>
                Together we work to create modern fragrances that feel luminous,
                confident and unforgettable.
            </p>

        </div>
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
<script src="theme.js"></script>
<script>
const slides = document.querySelectorAll(".team-slide");
let index = 0;

setInterval(() => {
    slides[index].classList.remove("active");
    index++;
    if(index >= slides.length){
        index = 0;
    }
    slides[index].classList.add("active");
}, 3500);
</script>
<?php require_once __DIR__ . '/chatbot_include.php'; ?>
</body>
</html>

