<?php
require_once 'config.php';

$sql = "SHOW TABLES";           // simple query that returns all table names
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Test</title>
</head>
<body>
<h2>DB connection works! Tables found:</h2>
<ul>
<?php while ($row = mysqli_fetch_row($result)) : ?>
    <li><?php echo $row[0]; ?></li>
<?php endwhile; ?>
</ul>
</body>
</html>
