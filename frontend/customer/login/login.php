<?php
session_start();

$errorMessage = '';

if (isset($_POST['submitted'])) {

if (!isset($_POST['email'], $_POST['password'])) {
$errorMessage = 'Please fill in both fields.';
} else {

require_once("connect_novadb.php");

try {
$stat = $db->prepare('SELECT * FROM Users WHERE email = ?');
$stat->execute([$_POST['email']]);

if ($stat->rowCount() > 0) {
$row = $stat->fetch();

if (password_verify($_POST['password'], $row['password'])) {

$_SESSION['user_id'] = $row['user_id'];
$_SESSION['username'] = $row['username'];
$_SESSION['role'] = $row['role'];

header("Location: index.php");
exit();

} else {
$errorMessage = 'Password is incorrect.';
}

} else {
$errorMessage = 'Email not found.';
}

} catch (PDOException $ex) {
$errorMessage = "A database error occurred.";
}

}

}
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

<title>Log in</title>

<!-- NOVA favicon -->
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

<a href="register.php" class="nav-link">Register</a>
<a href="login.php" class="nav-link active">Log in</a>

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

<div class="login-container">
<form class="login-form" action="login.php" method="post">

<div class="login-header">
<h1 class="login-title">Log in</h1>
<p class="login-subtitle">Access your NOVA account.</p>
</div>

<?php if ($errorMessage): ?>
<p class="login-error"><?php echo $errorMessage; ?></p>
<?php endif; ?>

<label for="email">Email:</label>
<input type="email" id="email" name="email" placeholder="Email address" required>

<label for="password">Password:</label>
<input type="password" id="password" name="password" placeholder="Password" required>

<input type="hidden" name="submitted" value="TRUE" />

<button type="submit" class="login-btn">Log in</button>

<p class="login-already-user">
Not registered yet?
<a href="register.php">Register here</a>
</p>

</form>
</div>


</main>

</body>
</html>

