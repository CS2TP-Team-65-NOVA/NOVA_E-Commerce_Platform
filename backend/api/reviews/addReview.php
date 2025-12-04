<?php
// addReview.php
include 'db.php';

// 1. User must be logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to add a review.");
}

$user_id = (int) $_SESSION['user_id'];

// 2. Get product_id (from URL or POST)
$product_id = 0;
if (isset($_GET['product_id'])) {
    $product_id = (int) $_GET['product_id'];
} elseif (isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];
}

if ($product_id <= 0) {
    die("No product selected.");
}

$rating = "";
$comment = "";
$errors = [];
$success = "";

// 3. Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    // Simple validation
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5.";
    }
    if ($comment === '') {
        $errors[] = "Comment is required.";
    }

    // Check if user already reviewed this product
    if (empty($errors)) {
        $checkSql = "SELECT 1 FROM reviews WHERE product_id = ? AND user_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $product_id, $user_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $errors[] = "You have already reviewed this product.";
        }
    }

    // Insert into DB
    if (empty($errors)) {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);

        if ($stmt->execute()) {
            $success = "Review added successfully!";
            // Optional: redirect back to product page
            // header("Location: product.php?id=" . $product_id);
            // exit;
            $rating = "";
            $comment = "";
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Review</title>
</head>
<body>
    <h1>Add Review for Product #<?= htmlspecialchars($product_id) ?></h1>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">

        <label>Rating (1–5):<br>
            <input type="number" name="rating" min="1" max="5"
                   value="<?= htmlspecialchars($rating) ?>" required>
        </label>
        <br><br>

        <label>Comment:<br>
            <textarea name="comment" rows="4" cols="40" required><?= htmlspecialchars($comment) ?></textarea>
        </label>
        <br><br>

        <button type="submit">Submit Review</button>
    </form>

    <p><a href="product.php?id=<?= htmlspecialchars($product_id) ?>">Back to product</a></p>
</body>
</html>
