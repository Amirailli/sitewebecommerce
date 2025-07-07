<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $image = mysqli_real_escape_string($conn, $_POST['imageSrc']);
    $description = mysqli_real_escape_string($conn, $_POST['nom']);
    $details = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['prix']);
    $stock = floatval($_POST['Quantitie']);
    $category = mysqli_real_escape_string($conn, $_POST['categorie']);

    $sql = "INSERT INTO item (imageSrc, nom, description, prix, stock, categorie) 
            VALUES ('$image', '$description', '$details', '$price', '$stock','$category')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true,
            "message" => "Item ajouté avec succès",
            "category" => $category
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Erreur : " . mysqli_error($conn)]);
    }
}
?>
