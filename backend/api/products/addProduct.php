<?php
// addProduct.php
include 'db.php';

$name = "";
$description = "";
$price = "";
$image = "";
$category_id = "";
$brand_id = "";
$errors = [];
$success = "";

// Get categories for dropdown
$categories = [];
$catSql = "SELECT category_id, category FROM categories ORDER BY category ASC";
$catResult = $conn->query($catSql);
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $image       = trim($_POST['image'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $brand_id    = $_POST['brand_id'] ?? '';

    // Basic validation
    if ($name === '') {
        $errors[] = "Product name is required.";
    }

    if ($price === '' || !is_numeric($price)) {
        $errors[] = "A valid price is required.";
    }

    if ($category_id === '') {
        $errors[] = "Please select a category.";
    }

    // Convert types
    $price_val = (float) $price;
    $category_id_val = (int) $category_id;

    // brand_id is optional – if empty, set to NULL
    $brand_id_val = null;
    if ($brand_id !== '') {
        $brand_id_val = (int) $brand_id;
    }

    if (empty($errors)) {
        $sql = "INSERT INTO products (name, description, price, image, category_id, brand_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        // ssdsii -> name(s), description(s), price(d), image(s), category_id(i), brand_id(i)
        $stmt->bind_param(
            "ssdsii",
            $name,
            $description,
            $price_val,
            $image,
            $category_id_val,
            $brand_id_val
        );

        if ($stmt->execute()) {
            $success = "Product added successfully!";
            // Clear the form
            $name = $description = $price = $image = "";
            $category_id = $brand_id = "";
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
    <title>Add Product</title>
</head>
<body>

<h1>Add Product</h1>

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
    <label>
        Product name:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
    </label>
    <br><br>

    <label>
        Description:<br>
        <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
    </label>
    <br><br>

    <label>
        Price:<br>
        <input type="text" name="price" value="<?= htmlspecialchars($price) ?>" required>
    </label>
    <br><br>

    <label>
        Image URL (optional):<br>
        <input type="text" name="image" value="<?= htmlspecialchars($image) ?>">
    </label>
    <br><br>

    <label>
        Category:<br>
        <select name="category_id" required>
            <option value="">-- Select category --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= ($cat['category_id'] == $category_id ? 'selected' : '') ?>>
                    <?= htmlspecialchars($cat['category']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>

    <label>
        Brand ID (optional):<br>
        <input type="number" name="brand_id" value="<?= htmlspecialchars($brand_id) ?>">
    </label>
    <br><br>

    <button type="submit">Add Product</button>
</form>

<p><a href="getProduct.php">View all products</a></p>

</body>
</html>
