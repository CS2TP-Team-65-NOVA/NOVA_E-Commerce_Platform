<?php
session_start();

// Redirect if not logged in or not a customer
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') === 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="customer_profile.css">
<title>My Profile – NOVA</title>
<link rel="icon" type="image/x-icon" href="nova_favicon.jpg"/>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<!-- HEADER: logo + dynamic navbar -->
<header id="main-header">
    <nav id="navbar">

        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>

        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.jpg" id="logo" alt="NOVA Logo">
        </a>

        <div class="nav-right">
            <a href="customer_profile.php" class="account-link active" aria-label="My account">
                <img src="account_icon.jpg" class="account-icon account-icon-default" alt="Account icon"/>
                <img src="active_account_icon.jpg" class="account-icon account-icon-active" alt="Active account icon"/>
            </a>
            <a href="shopping_cart.php" class="basket-link" aria-label="Shopping basket">
                <img src="basket_icon.jpg" class="basket-icon basket-icon-default" alt="Basket icon"/>
                <img src="active_basket_icon.jpg" class="basket-icon basket-icon-active" alt="Active basket icon"/>
            </a>
        </div>

    </nav>
</header>


<!-- CUSTOMER PROFILE LAYOUT -->
<div class="customer-layout">

    <!-- SIDEBAR -->
    <aside class="customer-sidebar">

        <!-- Avatar area -->
        <div class="sidebar-avatar">
            <div class="avatar-ring">
                <div class="avatar-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
            </div>
            <p class="sidebar-greeting">Welcome back,</p>
            <p class="sidebar-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Customer'); ?></p>
        </div>

        <nav class="sidebar-nav">
            <a href="customer_profile.php" class="active-link">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                </span>
                My Profile
            </a>
            <a href="customer_password.php">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                </span>
                Change Password
            </a>
            <a href="customer_orders.php">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                </span>
                My Orders
            </a>
            <a href="customer_wishlist.php">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </span>
                Wishlist
            </a>
            <a href="logout.php" class="logout-link">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                </span>
                Log Out
            </a>
        </nav>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="customer-main">

        <!-- Page title -->
        <div class="page-header">
            <h1>My Profile</h1>
            <p class="page-subtitle">Manage your personal information and preferences</p>
        </div>

        <!-- SUCCESS / ERROR MESSAGE (PHP would populate this) -->
        <?php if (!empty($_SESSION['profile_success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['profile_success']); unset($_SESSION['profile_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['profile_error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['profile_error']); unset($_SESSION['profile_error']); ?>
            </div>
        <?php endif; ?>


        <!-- PROFILE DETAILS CARD -->
        <section class="content-card customer-card">
            <div class="card-header">
                <h2>Personal Information</h2>
                <span class="card-badge">Account Details</span>
            </div>

            <form class="profile-form customer-profile-form" action="update_profile.php" method="POST" enctype="multipart/form-data">

                <!-- Profile Picture Upload -->
                <div class="avatar-upload-row">
                    <div class="upload-preview" id="avatarPreview">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="preview-icon">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                    <div class="upload-info">
                        <label for="profile_picture" class="upload-btn">Upload Photo</label>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display:none;">
                        <p class="upload-hint">JPG, PNG or GIF · Max 2MB</p>
                    </div>
                </div>

                <div class="form-grid">

                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" id="full_name"
                               placeholder="Jane Smith"
                               value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>"
                               required>
                    </div>

                    <!-- Username -->
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username"
                               placeholder="jane_smith"
                               value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email"
                               placeholder="jane@example.com"
                               value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>"
                               required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" name="phone" id="phone"
                               placeholder="+44 7700 900000"
                               value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" id="dob"
                               value="<?php echo htmlspecialchars($_SESSION['dob'] ?? ''); ?>">
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="" disabled selected>Select gender</option>
                            <option value="female" <?php echo ($_SESSION['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="male"   <?php echo ($_SESSION['gender'] ?? '') === 'male'   ? 'selected' : ''; ?>>Male</option>
                            <option value="other"  <?php echo ($_SESSION['gender'] ?? '') === 'other'  ? 'selected' : ''; ?>>Prefer not to say</option>
                        </select>
                    </div>

                </div><!-- /form-grid -->

                <!-- Delivery Address -->
                <div class="form-section-title">Delivery Address</div>
                <div class="form-grid">

                    <div class="form-group form-group--full">
                        <label for="address_line1">Address Line 1</label>
                        <input type="text" name="address_line1" id="address_line1"
                               placeholder="12 Rose Street"
                               value="<?php echo htmlspecialchars($_SESSION['address_line1'] ?? ''); ?>">
                    </div>

                    <div class="form-group form-group--full">
                        <label for="address_line2">Address Line 2 <span class="optional">(optional)</span></label>
                        <input type="text" name="address_line2" id="address_line2"
                               placeholder="Flat 3"
                               value="<?php echo htmlspecialchars($_SESSION['address_line2'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" id="city"
                               placeholder="London"
                               value="<?php echo htmlspecialchars($_SESSION['city'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" name="postcode" id="postcode"
                               placeholder="SW1A 1AA"
                               value="<?php echo htmlspecialchars($_SESSION['postcode'] ?? ''); ?>">
                    </div>

                </div><!-- /form-grid -->

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="reset"  class="btn-secondary">Discard</button>
                </div>

            </form>
        </section>


        <!-- FRAGRANCE PREFERENCES CARD -->
        <section class="content-card customer-card">
            <div class="card-header">
                <h2>Fragrance Preferences</h2>
                <span class="card-badge card-badge--accent">Personalise</span>
            </div>
            <p class="card-desc">Help us recommend the perfect scents for you.</p>

            <form class="profile-form customer-profile-form" action="update_preferences.php" method="POST">

                <div class="form-grid">

                    <div class="form-group">
                        <label for="fav_family">Favourite Fragrance Family</label>
                        <select name="fav_family" id="fav_family" class="form-select">
                            <option value="" disabled selected>Choose a family</option>
                            <option value="floral">Floral</option>
                            <option value="woody">Woody</option>
                            <option value="oriental">Oriental / Amber</option>
                            <option value="fresh">Fresh / Citrus</option>
                            <option value="aquatic">Aquatic</option>
                            <option value="chypre">Chypre</option>
                            <option value="gourmand">Gourmand</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fav_season">Preferred Season to Wear</label>
                        <select name="fav_season" id="fav_season" class="form-select">
                            <option value="" disabled selected>Choose a season</option>
                            <option value="spring">Spring</option>
                            <option value="summer">Summer</option>
                            <option value="autumn">Autumn</option>
                            <option value="winter">Winter</option>
                            <option value="all">All year round</option>
                        </select>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Preferences</button>
                </div>

            </form>
        </section>

        <!-- DANGER ZONE -->
        <section class="content-card customer-card danger-card">
            <div class="card-header">
                <h2>Account Actions</h2>
            </div>
            <p class="card-desc">These actions are permanent and cannot be undone.</p>
            <div class="danger-actions">
                <a href="logout.php" class="btn-danger-outline">Log Out</a>
                <a href="delete_account.php"
                   onclick="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.');"
                   class="btn-danger">Delete Account</a>
            </div>
        </section>

    </main>
</div><!-- /customer-layout -->


<footer>
</footer>

<script>
// Live avatar preview
document.getElementById('profile_picture').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        const preview = document.getElementById('avatarPreview');
        preview.innerHTML = `<img src="${ev.target.result}" alt="Profile preview" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
    };
    reader.readAsDataURL(file);
});
</script>

</body>
</html>