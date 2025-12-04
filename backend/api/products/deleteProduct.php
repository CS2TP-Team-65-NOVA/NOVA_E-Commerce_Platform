<?php
// deleteProduct.php
include 'db.php';

// 1. Get product_id from URL: deleteProduct.php?id=3
if (!isset($_GET['id'])) {
    die("No product ID provided.");
}

$product_id = (int) $_GET['id'];

if ($product_id <= 0) {
    die("Invalid product ID.");
}

// 2. Delete the product
$sql = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    // 3. Redirect back to product list
    header("Location: getProduct.php?msg=deleted");
    exit;
} else {
    echo "Error deleting product: " . $conn->error;
}
?>
