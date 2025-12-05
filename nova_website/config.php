
<?php
$servername = "localhost"; 
$username = "cs2team65";                     
$password = "XRCsv6P4min3JM88F9xZ8LVGM";     
$dbname = "cs2team65_db";                  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>






