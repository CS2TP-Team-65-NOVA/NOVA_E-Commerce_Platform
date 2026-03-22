<?php
session_start(); 

$host = "localhost";
$user = "root";
$pass = "";                 
$dbname = "cs2team65_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
