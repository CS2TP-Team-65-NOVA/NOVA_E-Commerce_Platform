<?php
// db.php
// Shared database connection for the entire backend team

$host    = 'localhost';
$db      = 'nova_malaika';   // <-- your actual database name
$user    = 'root';           // <-- your MySQL username
$pass    = '';               // <-- your MySQL password (often empty on XAMPP/MAMP)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
