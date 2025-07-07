<?php
include 'database.php';
header('Content-Type: application/json');

$sql = "SELECT  * FROM item";

$result = mysqli_query($conn, $sql);

$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode($items);
?>

