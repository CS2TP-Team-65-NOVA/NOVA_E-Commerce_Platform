<?php
// getCategories.php
include 'db.php';

$sql = "SELECT category_id, category, description FROM categories ORDER BY category ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Categories List</title>
</head>
<body>

<h1>All Categories</h1>

<?php if ($result && $result->num_rows > 0): ?>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($row['category']) ?></strong><br>
                <?= htmlspecialchars($row['description']) ?>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No categories found.</p>
<?php endif; ?>

</body>
</html>
