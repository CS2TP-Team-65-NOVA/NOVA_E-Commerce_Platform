<?php
// updateProduct.php
include 'db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no ID provided
if ($product_id <= 0) {
    die("No product selected.");
}

// Fetch all categories for dropdown
$categories = [];
$catSql = "SELECT category_id, category FROM categories ORDER BY category ASC";
$catResult = $conn->query($catSql);
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Load existing product
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$errors = [];
$success = "";

// On form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $image       = trim($_POST['image'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $brand_id    = $_POST['brand_id'] !== '' ? (int)$_POST['brand_id'] : null;

    // Validation
    if ($name === '') $errors[] = "Product name is required.";
    if ($price === '' || !is_numeric($price)) $errors[] = "Valid price is required.";
    if ($category_id <= 0) $errors[] = "Please select a category.";

    if (empty($errors)) {
        $sql = "UPDATE products 
                SET name = ?, description = ?, price = ?, image = ?, category_id = ?, brand_id = ?
                WHERE product_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssdsiii",
            $name,
            $description,
            $price,
            $image,
            $category_id,
            $brand_id,
            $product_id
        );

        if ($stmt->execute()) {
            $success = "Product updated successfully!";

            // Reload updated product data
            $sql = "SELECT * FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
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
    <title>Update Product</title>
</head>
<body>

<h1>Update Product: <?= htmlspecialchars($product['name']) ?></h1>

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

<form method="post">

    <label>Name:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
    </label><br><br>

    <label>Description:<br>
        <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
    </label><br><br>

    <label>Price:<br>
        <input type="text" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
    </label><br><br>

    <label>Image URL:<br>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>">
    </label><br><br>

    <label>Category:<br>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" 
                    <?= $cat['category_id'] == $product['category_id'] ? "selected" : "" ?>>
                    <?= htmlspecialchars($cat['category']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Brand ID (optional):<br>
        <input type="number" name="brand_id" value="<?= htmlspecialchars($product['brand_id']) ?>">
    </label><br><br>

    <button type="submit">Update Product</button>

</form>

<p><a href="getProduct.php">Back to product list</a></p>

</body>
</html>
