<?php
// add_category.php
include 'db.php';

$category = "";
$description = "";
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get data from form
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // 2. Simple validation
    if ($category === '') {
        $errors[] = "Category name is required.";
    }

    // 3. If no errors, insert into database
    if (empty($errors)) {
        $sql = "INSERT INTO categories (category, description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $category, $description);

        if ($stmt->execute()) {
            $success = "Category added successfully!";
            // clear form
            $category = "";
            $description = "";
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
    <title>Add Category</title>
</head>
<body>
    <h1>Add New Category</h1>

    <!-- Show success message -->
    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <!-- Show errors -->
    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" action="">
        <label>
            Category name:<br>
            <input type="text" name="category" value="<?= htmlspecialchars($category) ?>" required>
        </label>
        <br><br>

        <label>
            Description (optional):<br>
            <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
        </label>
        <br><br>

        <button type="submit">Add Category</button>
    </form>

    <p><a href="products.php">Back to products</a></p>
</body>
</html>
