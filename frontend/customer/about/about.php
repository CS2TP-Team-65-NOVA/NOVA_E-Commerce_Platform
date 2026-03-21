<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" type="text/css" href="style.css">

<title>About</title>
<link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>

<body>

<header id="main-header">
<nav id="navbar">
<div class="nav-left">
    <a href="index.php" class="nav-link">Home</a>
    <a href="about.php" class="nav-link active">About</a>
    <a href="perfumes.php" class="nav-link">Perfumes</a>
</div>
<a href="index.php" class="logo-link">
    <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
</a>

<div class="nav-right">
<?php if (!isset($_SESSION['user_id'])): ?>
    <a href="register.php" class="nav-link">Register</a>
    <a href="login.php" class="nav-link">Log in</a>
    <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
        <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon"/>
        <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket"/>
    </a>
<?php else: $role = $_SESSION['role'] ?? 'customer'; ?>
    <?php if ($role === 'admin'): ?>
        <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>
        <a href="admin_profile.php" class="account-link" aria-label="Admin">
            <img src="account_icon.png" class="account-icon account-icon-default" alt="Account"/>
            <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active"/>
        </a>
        <a href="shopping_cart.php" class="basket-link">
            <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket"/>
            <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket"/>
        </a>
    <?php else: ?>
        <a href="customer_profile.php" class="account-link" aria-label="Account">
            <img src="account_icon.png" class="account-icon account-icon-default" alt="Account"/>
            <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active"/>
        </a>
        <a href="shopping_cart.php" class="basket-link">
            <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket"/>
            <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active"/>
        </a>
    <?php endif; ?>
<?php endif; ?>
</div>

</nav>
</header>

<!-- PERFUME BANNER -->
<section class="nova-banner">
    <img src="about_banner.png" class="nova-banner-img" alt="Nova perfume banner"/>
</section>

<main>
    <main>

    <!-- OUR STORY SECTION -->
    <section class="about-story-section">
        <div class="about-story-inner">

            <!-- Left column – main story text -->
            <div class="about-story-main">
                <h2 class="about-heading">Our Story</h2>

                <p class="about-lead">
                    Nova was born from a simple idea: fragrance should feel like a fresh start,
                    not just the final step before you leave the house. We wanted scents that
                    feel luminous, modern and effortless – the kind you remember long after the
                    moment has passed.
                </p>

                <p>
                    The name <strong>“Nova”</strong> comes from the stellar phenomenon – a sudden,
                    brilliant burst of light in the night sky. For us, it represents
                    <em>new beginnings, confidence and that quiet explosion of character</em>
                    when you find a scent that truly feels like you.
                </p>

                <p>
                    Each Nova fragrance is inspired by real moments: late-night city walks,
                    soft morning light, stolen celebrations and everything in between.
                    We blend clean lines and modern accords with a touch of warmth, so every
                    bottle feels both future-facing and timeless.
                </p>
            </div>

            <!-- Right column – key points -->
            <aside class="about-story-highlights">

                <div class="about-highlight">
                    <h3>Cruelty-free by design</h3>
                    <p>
                        Nova fragrances are <strong>never tested on animals</strong>. We partner only
                        with suppliers who share our commitment to ethical, cruelty-free practices.
                    </p>
                </div>

                <div class="about-highlight">
                    <h3>Sustainability in focus</h3>
                    <p>
                        From recyclable glass bottles to minimal outer packaging,
                        we’re continually refining Nova to reduce waste and lighten our footprint.
                    </p>
                </div>

                <div class="about-highlight">
                    <h3>Thoughtful ingredients</h3>
                    <p>
                        We balance high-quality aroma molecules with carefully selected naturals,
                        avoiding unnecessary additives so the scent – not the filler – takes centre stage.
                    </p>
                </div>

                <div class="about-highlight founder-note">
                    <h3>From our founder</h3>
                    <p>
                        “Nova was created for people who want their fragrance to feel intentional.
                        Not loud, not overpowering – just a quiet kind of brilliance that stays with you.”
                    </p>
                    <p class="founder-signoff">
                        — Nova Founder
                    </p>
                </div>

            </aside>
        </div>
        <!-- VALUES / WHAT WE BELIEVE -->
<section class="about-values-section">
    <div class="about-values-inner">

        <h2 class="about-heading">Values</h2>
        <p class="about-values-lead">
            Nova is built on the belief that fragrance should pair imagination with intention. 
            Every scent is crafted to feel radiant, refined, and deeply personal.
        </p>

        <div class="values-grid">

            <div class="value-card">
                <h3>Premium Ingredients</h3>
                <p>
                    We source high-grade aroma molecules and naturals, ensuring each note feels pure, balanced and luxurious.
                </p>
            </div>

            <div class="value-card">
                <h3>Long-Lasting Formulas</h3>
                <p>
                    Our blends are engineered for endurance – scents designed to linger without overwhelming.
                </p>
            </div>

            <div class="value-card">
                <h3>Cruelty-Free & Sustainable</h3>
                <p>
                    Ethical production is non-negotiable. We create consciously, embracing recyclable materials and animal-free testing.
                </p>
            </div>

            <div class="value-card">
                <h3>Confidence in Every Bottle</h3>
                <p>
                    Crafted for individuality, Nova fragrances empower self-expression, identity, and a quiet, magnetic confidence.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- WHAT MAKES NOVA UNIQUE -->
<section class="about-unique-section">
    <div class="about-unique-inner">

        <h2 class="about-heading">What Makes Nova Unique</h2>
        <p class="about-unique-lead">
            In a world of copy-paste fragrances, Nova is designed to feel like a new constellation — 
            familiar enough to love instantly, distinct enough to remember.
        </p>

        <div class="unique-grid">

            <div class="unique-point">
                <h3>Celestial-Inspired Fragrances</h3>
                <p>
                    Our scents draw from constellations, night skies and luminous bursts of light,
                    translating cosmic moments into modern perfume experiences.
                </p>
            </div>

            <div class="unique-point">
                <h3>Luxury, Made Accessible</h3>
                <p>
                    We prioritise quality over heavy markups, delivering a luxury impression without 
                    the traditional luxury price tag.
                </p>
            </div>

            <div class="unique-point">
                <h3>Minimalist, Futuristic Design</h3>
                <p>
                    Clean silhouettes, precise lines and a focus on the bottle as an object of art —
                    Nova is made to live beautifully on your shelf.
                </p>
            </div>

            <div class="unique-point">
                <h3>Art Meets Science</h3>
                <p>
                    Each collection is built at the intersection of creativity and chemistry,
                    blending artistic direction with meticulous formulation.
                </p>
            </div>

        </div>

    </div>
</section>
<!-- CRAFTSMANSHIP / HOW WE CREATE -->
<section class="about-craft-section">
    <div class="about-craft-inner">
        <h2 class="about-heading">Craftsmanship</h2>
        <p class="about-craft-lead">
            Every Nova fragrance is carefully imagined and precisely made. We treat perfumery as a craft –
            slow, intentional, refined.
        </p>

        <div class="craft-grid">

            <div class="craft-card">
                <h3>Scent Development</h3>
                <p>
                    Our process begins with inspiration, evolves through formulation, and is refined using responsible,
                    high-grade aroma technologies.
                </p>
            </div>

            <div class="craft-card">
                <h3>Ingredient Sourcing</h3>
                <p>
                    We partner with suppliers who prioritise quality, traceability, and ethical harvesting.
                </p>
            </div>

            <div class="craft-card">
                <h3>Intentional Blending</h3>
                <p>
                    Each fragrance is blended in small controlled batches, tested for balance, longevity and skin experience,
                    and refined until it feels effortless.
                </p>
            </div>

            <div class="craft-card">
                <h3>Design Experience</h3>
                <p>
                    We obsess over more than scent. From first spray to last impression – the bottle weight,
                    trigger resistance, silhouette and unboxing emotion all matter.
                </p>
            </div>

        </div>
    </div>
</section>








</main>

<footer></footer>

</body>
</html>
