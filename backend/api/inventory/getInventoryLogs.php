
<?php

require_once __DIR__ . '/../..config/db.php';


$sql ="Select * From inventory";
$result = $conn -> query ($sql);

$inventory = [];

if ($result -> num_rows >0){
    while ($row = $result -> fetch_assoc()){
        $inventory[] = $row;
    }
}

//converts the PHP array into JSONN do frontend testsers can reat it


header('Content-Type: application/json');
echo json_encode($inventory);

?>