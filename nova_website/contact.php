<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <!-- Google Belleza Font (same as other pages) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Belleza&display=swap" rel="stylesheet">

    <!-- CSS stylesheet -->
    <link rel="stylesheet" type="text/css" href="style.css">

    <title>Contact</title>

    <!-- NOVA favicon -->
    <link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>

<body>

<!-- HEADER: same navbar as other pages -->
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

        <!-- RIGHT SIDE -->
        <div class="nav-right">

            <?php if (!isset($_SESSION['user_id'])): ?>

                <a href="register.php" class="nav-link">Register</a>
                <a href="login.php" class="nav-link">Log in</a>

                <a href="shopping_cart.php" class="basket-link">
                    <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon">
                    <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon">
                </a>

            <?php else: ?>
                <?php $role = $_SESSION['role'] ?? 'customer'; ?>

                <?php if ($role === 'admin'): ?>

                    <a href="admin_dashboard.php" class="nav-link">Admin Dashboard</a>

                    <a href="admin_profile.php" class="account-link">
                        <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon">
                        <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon">
                    </a>

                    <a href="shopping_cart.php" class="basket-link">
                        <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon">
                        <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon">
                    </a>

                <?php else: ?>

                    <a href="customer_profile.php" class="account-link">
                        <img src="account_icon.png" class="account-icon account-icon-default" alt="Account icon">
                        <img src="active_account_icon.png" class="account-icon account-icon-active" alt="Active account icon">
                    </a>

                    <a href="shopping_cart.php" class="basket-link">
                        <img src="basket_icon.png" class="basket-icon basket-icon-default" alt="Basket icon">
                        <img src="active_basket_icon.png" class="basket-icon basket-icon-active" alt="Active basket icon">
                    </a>

                <?php endif; ?>
            <?php endif; ?>

        </div>

    </nav>
</header>

<main>

    <!-- CONTACT CONTENT (Ashish) -->
    <section class="contact-section">
        <h1>Contact Us</h1>

        <p class="contact-intro">
            Have a question about finding your perfect NOVA scent or an existing order?
            We’d love to help.
        </p>

        <p class="contact-response-time">
            We usually reply within <strong>24–48 hours</strong> on business days.
        </p>

        <form id="contact-form" class="contact-form" novalidate>

            <!-- Full Name -->
            <div class="form-group">
                <label for="full_name">Full Name <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" required maxlength="80">
                <p class="error-message" data-for="full_name"></p>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
                <p class="error-message" data-for="email"></p>
            </div>

            <!-- Subject -->
            <div class="form-group">
                <label for="subject">Reason for contacting</label>
                <select id="subject" name="subject">
                    <option value="">Please select…</option>
                    <option value="order_issue">Order issue</option>
                    <option value="business">Business inquiry</option>
                    <option value="pr">PR / brand question</option>
                    <option value="other">Other</option>
                </select>
                <p class="error-message" data-for="subject"></p>
            </div>

            <!-- Message -->
            <div class="form-group">
                <label for="message">Message <span class="required">*</span></label>
                <textarea id="message" name="message" rows="5" required maxlength="1000"></textarea>
                <p class="error-message" data-for="message"></p>
                <p class="helper-text" id="message-counter">0 / 1000</p>
            </div>

            <!-- Status -->
            <div id="form-status" class="form-status"></div>

            <!-- Submit -->
            <button type="submit" class="contact-submit-btn">Send Message</button>

        </form>

        <div class="contact-info">
            <h2>Other ways to reach us</h2>

            <!-- Customer service email -->
            <div class="contact-info-item">
                <img src="mail_icon.png" alt="Email icon" class="contact-icon">
                <div>
                    <h3>Email</h3>
                    <p>
                        <a href="mailto:support@nova-perfumes.com">support@nova-perfumes.com</a>
                    </p>
                </div>
            </div>

            <!-- Phone / WhatsApp -->
            <div class="contact-info-item">
                <img src="whatsapp_icon.png" alt="Phone icon" class="contact-icon">
                <div>
                    <h3>Phone / WhatsApp</h3>
                    <p>
                        <a href="tel:+441234567890">+44 1234 567 890</a>
                    </p>
                </div>
            </div>

            <!-- Instagram -->
            <div class="contact-info-item">
                <img src="instagram_icon.png" alt="Instagram icon" class="contact-icon">
                <div>
                    <h3>Instagram</h3>
                    <p>
                        <a href="https://instagram.com/nova.perfumes" target="_blank">@nova.perfumes</a>
                    </p>
                </div>
            </div>

            <!-- Facebook -->
            <div class="contact-info-item">
                <img src="facebook_icon.png" alt="Facebook icon" class="contact-icon">
                <div>
                    <h3>Facebook</h3>
                    <p>
                        <a href="https://facebook.com/novaperfumes" target="_blank">/novaperfumes</a>
                    </p>
                </div>
            </div>

            <!-- FAQ -->
            <div class="contact-info-item faq-item">
                <span class="contact-icon contact-icon-empty" aria-hidden="true"></span>
                <div>
                    <h3>FAQ</h3>
                    <p>
                        <a href="faq.php">Find your instant answer here</a>
                    </p>
                </div>
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

<!-- Contact form JS (unchanged) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("contact-form");
        const statusBox = document.getElementById("form-status");
        const messageInput = document.getElementById("message");
        const messageCounter = document.getElementById("message-counter");

        // live character counter for message
        messageInput.addEventListener("input", function () {
            const max = messageInput.getAttribute("maxlength");
            messageCounter.textContent = messageInput.value.length + " / " + max;
        });

        form.addEventListener("submit", function (e) {
            e.preventDefault(); // prevent actual submit for now

            // clear old errors & status
            const errorElems = form.querySelectorAll(".error-message");
            errorElems.forEach(el => el.textContent = "");
            statusBox.textContent = "";
            statusBox.className = "form-status";

            let hasError = false;

            function setError(fieldId, message) {
                const errEl = form.querySelector('.error-message[data-for="' + fieldId + '"]');
                if (errEl) {
                    errEl.textContent = message;
                }
                hasError = true;
            }

            const nameVal = document.getElementById("full_name").value.trim();
            const emailVal = document.getElementById("email").value.trim();
            const subjectVal = document.getElementById("subject").value;
            const messageVal = messageInput.value.trim();

            if (!nameVal) {
                setError("full_name", "Please enter your name.");
            }

            if (!emailVal) {
                setError("email", "Please enter your email address.");
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailVal)) {
                    setError("email", "Please enter a valid email address.");
                }
            }

            if (!messageVal) {
                setError("message", "Please enter a message.");
            } else if (messageVal.length > 1000) {
                setError("message", "Your message is too long (max 1000 characters).");
            }

            if (hasError) {
                statusBox.textContent = "Please fix the highlighted fields and try again.";
                statusBox.classList.add("form-status-error");
                return;
            }

            statusBox.textContent = "Thank you! Your message has been submitted.";
            statusBox.classList.add("form-status-success");

            form.reset();
            messageCounter.textContent = "0 / 1000";
        });
    });
</script>

</body>
</html>
