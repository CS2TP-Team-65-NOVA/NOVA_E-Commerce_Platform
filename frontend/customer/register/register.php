<?php
session_start();

// Handle form submission
if (isset($_POST['submitted'])) {

    require_once('connect_deliciodb.php');

    $username = isset($_POST['username']) ? trim($_POST['username']) : false;
    $email    = isset($_POST['email']) ? trim($_POST['email']) : false;
    $password = isset($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : false;

    if (!$username || !$email || !$password) {
        $error_message = "All fields are required!";
    } else {
        try {
            $check = $db->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);

            if ($check->fetch()) {
                $error_message = "Username or email is already taken.";
            } else {
                $stmt = $db->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password]);

                header("Location: login.php");
                exit();
            }
        } catch (PDOException $ex) {
            $error_message = "A database error occurred!";
        }
    }
}
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

<title>Register</title>
<link rel="icon" type="image/x-icon" href="nova_favicon.png"/>
</head>

<body>

<header id="main-header">
    <nav id="navbar">

        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="perfumes.php" class="nav-link">Perfumes</a>
        </div>

        <a href="index.php" class="logo-link">
            <img src="nova_logo_black.png" id="logo" alt="NOVA Logo">
        </a>

        <div class="nav-right">
        <?php if (!isset($_SESSION['user_id'])): ?>

            <a href="register.php" class="nav-link active">Register</a>
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

<main>

<div class="register-container">
    <form class="register-form" action="register.php" method="post">

        <div class="register-header">
            <h1 class="register-title-inside">Create your NOVA account</h1>
            <p class="register-subtitle">Register to create your NOVA account.</p>
        </div>

        <?php if (isset($error_message)): ?>
        <p class="register-error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" class="register-input" placeholder="Username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="register-input" placeholder="Email address" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" class="register-input" placeholder="Password" required>

        <input type="hidden" name="submitted" value="true" />

        <button type="submit" class="register-btn">Register</button>

        <p class="register-already-user">
            Already a user?
            <a href="login.php">Log in</a>
        </p>

    </form>
</div>

</main>

</body>
</html>
