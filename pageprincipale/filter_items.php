<?php
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nos Produits</title>
    <link rel="stylesheet" href="Ecommarce.css">
</head>
<body>';

include 'database.php';

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '';



$query = "SELECT * FROM item WHERE 1";

if (!empty($search)) {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $query .= " AND (LOWER(nom) LIKE '%$safeSearch%' OR LOWER(description) LIKE '%$safeSearch%')";
}

if (!empty($category)) {
    $safeCategory = mysqli_real_escape_string($conn, $category);
    $query .= " AND categorie = '$safeCategory'";
}

if (!empty($price)) {
    if ($price === 'low') {
        $query .= " AND prix < 3000";
    } elseif ($price === 'medium') {
        $query .= " AND prix BETWEEN 3000 AND 10000";
    } elseif ($price === 'high') {
        $query .= " AND prix > 10000";
    }
}

$result = mysqli_query($conn, $query);
echo "<div class='main'>";

if (mysqli_num_rows($result) > 0) {
    while ($item = mysqli_fetch_assoc($result)) {
        echo "<div class='itm'>";
        echo "<div class='haut'><img src='{$item['imageSrc']}' class='im'></div>";
        echo "<div class='bas'>";
        echo "<p class='description'>{$item['nom']}</p>";
        echo "<p class='pargh' style='display:none;'>{$item['description']}</p>";
        echo "<h5><strong>{$item['prix']} DA</strong></h5>";
        echo "<a href='details.php?id={$item['item_id']}'><button class='btnselect'>Select Option</button></a>";
        echo "</div></div>";
        
    }

} else {
    echo '<p>Aucun article ne correspond à vos critères de recherche.</p>';
}
echo "</div>";
?>
