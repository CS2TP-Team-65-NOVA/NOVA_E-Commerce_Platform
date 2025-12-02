<?php

include 'db.php';

// Check if a specific product is requested
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id > 0) {
    // 1) Get single product + category
    $sql = "SELECT p.*, c.category 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $productResult = $stmt->get_result();
    $product = $productResult->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }

    // 2) Get product versions (sizes)
    $sql = "SELECT * 
            FROM product_versions
            WHERE product_id = ?
            ORDER BY size_ml ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $versionsResult = $stmt->get_result();

    // 3) Get reviews for this product
    $sql = "SELECT r.*, u.full_name
            FROM reviews r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $reviewsResult = $stmt->get_result();

} else {
    // No ID given → list all products with category
    $sql = "SELECT p.product_id, p.name, p.price, c.category
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            ORDER BY p.product_id DESC";
    $productsResult = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products</title>
</head>
<body>

<?php if ($product_id > 0): ?>

    <!-- SINGLE PRODUCT VIEW -->
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <p><strong>Category:</strong> <?= htmlspecialchars($product['category'] ?? 'Uncategorised') ?></p>
    <p><strong>Price:</strong> £<?= htmlspecialchars($product['price']) ?></p>
    <?php if (!empty($product['image'])): ?>
        <p><img src="<?= htmlspecialchars($product['image']) ?>" alt="Product image" style="max-width:200px;"></p>
    <?php endif; ?>
    <p><strong>Description:</strong><br>
        <?= nl2br(htmlspecialchars($product['description'])) ?>
    </p>

    <h2>Available sizes / versions</h2>
    <?php if ($versionsResult && $versionsResult->num_rows > 0): ?>
        <ul>
            <?php while ($v = $versionsResult->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($v['size_ml']) ?> ml –
                    £<?= htmlspecialchars($v['price']) ?> |
                    Stock: <?= htmlspecialchars($v['stock_qty']) ?> |
                    SKU: <?= htmlspecialchars($v['sku']) ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No versions found for this product.</p>
    <?php endif; ?>

    <h2>Reviews</h2>
    <p><a href="addReview.php?product_id=<?= $product_id ?>">Add a review</a></p>

    <?php if ($reviewsResult && $reviewsResult->num_rows > 0): ?>
        <ul>
            <?php while ($r = $reviewsResult->fetch_assoc()): ?>
                <li style="margin-bottom:15px;">
                    <strong><?= htmlspecialchars($r['full_name']) ?></strong>
                    – Rating: <?= htmlspecialchars($r['rating']) ?>/5<br>
                    <?= nl2br(htmlspecialchars($r['comment'])) ?><br>
                    <small>Created: <?= htmlspecialchars($r['created_at']) ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>

    <p><a href="getProduct.php">Back to all products</a></p>

<?php else: ?>

    <!-- PRODUCT LIST VIEW -->
    <h1>All Products</h1>
    <p><a href="addProduct.php">Add new product</a></p>

    <?php if ($productsResult && $productsResult->num_rows > 0): ?>
        <ul>
            <?php while ($row = $productsResult->fetch_assoc()): ?>
                <li>
                    #<?= $row['product_id'] ?>:
                    <strong><?= htmlspecialchars($row['name']) ?></strong>
                    – £<?= htmlspecialchars($row['price']) ?>
                    (<?= htmlspecialchars($row['category'] ?? 'Uncategorised') ?>)
                    [<a href="getProduct.php?id=<?= $row['product_id'] ?>">view</a>]
                    [<a href="updateProduct.php?id=<?= $row['product_id'] ?>">edit</a>]
                    [<a href="deleteProduct.php?id=<?= $row['product_id'] ?>"
                        onclick="return confirm('Delete this product?');">delete</a>]
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>
