<?php
// getReviews.php
include 'db.php';

// Check if we are filtering by product
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id > 0) {
    // Reviews for a specific product
    $sql = "SELECT r.*, p.name AS product_name, u.full_name
            FROM reviews r
            JOIN products p ON r.product_id = p.product_id
            JOIN users u ON r.user_id = u.user_id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // All reviews
    $sql = "SELECT r.*, p.name AS product_name, u.full_name
            FROM reviews r
            JOIN products p ON r.product_id = p.product_id
            JOIN users u ON r.user_id = u.user_id
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reviews</title>
</head>
<body>

<?php if ($product_id > 0): ?>
    <h1>Reviews for Product #<?= htmlspecialchars($product_id) ?></h1>
    <p><a href="getProduct.php?id=<?= htmlspecialchars($product_id) ?>">Back to product</a></p>
<?php else: ?>
    <h1>All Reviews</h1>
<?php endif; ?>

<?php if ($result && $result->num_rows > 0): ?>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li style="margin-bottom: 15px;">
                <strong>Product:</strong> 
                <?= htmlspecialchars($row['product_name']) ?> 
                (ID: <?= $row['product_id'] ?>)<br>

                <strong>User:</strong> 
                <?= htmlspecialchars($row['full_name']) ?> 
                (User ID: <?= $row['user_id'] ?>)<br>

                <strong>Rating:</strong> 
                <?= htmlspecialchars($row['rating']) ?>/5<br>

                <strong>Comment:</strong><br>
                <?= nl2br(htmlspecialchars($row['comment'])) ?><br>

                <small>
                    Created: <?= htmlspecialchars($row['created_at']) ?> 
                    | Updated: <?= htmlspecialchars($row['updated_at']) ?>
                </small>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No reviews found.</p>
<?php endif; ?>

</body>
</html>
