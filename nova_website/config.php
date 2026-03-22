<?php

/*
$servername = "localhost"; 
$username = "cs2team65";                     
$password = "XRCsv6P4min3JM88F9xZ8LVGM";     
$dbname = "cs2team65_db";                  

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

*/

$servername = "127.0.0.1";  // localhost can sometimes fail on Mac
$username = "root";          // default XAMPP/MAMP username
$password = "";              // default password (empty)
$dbname = "cs2team65_db";    // your imported local DB

//$conn = new mysqli($servername, $username, $password, $dbname);

$conn = new mysqli(null, "root", "", "cs2team65_db", null, "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}