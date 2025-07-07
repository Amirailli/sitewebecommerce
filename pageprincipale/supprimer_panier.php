<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    die('non_connecte');
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];


$check_sql = "SELECT * FROM panier WHERE user_id = '$user_id' AND item_id = '$item_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
  
    $delete_sql = "DELETE FROM panier WHERE user_id = '$user_id' AND item_id = '$item_id'";
    if (mysqli_query($conn, $delete_sql)) {
        echo 'success';
    } else {
        echo 'erreur_suppression';
    }
} else {
    echo 'article_non_trouve';
}

mysqli_close($conn);
?>