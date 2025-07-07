<?php
session_start();
include 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Utilisateur non connecté"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = intval($_POST['item_id']);
    $quantite = intval($_POST['quantite']);
    $user_id = $_SESSION['user_id'];

    if ($item_id <= 0 || $quantite <= 0) {
        echo json_encode(["success" => false, "message" => "Données invalides"]);
        exit;
    }

    $sql = "INSERT INTO panier (user_id, item_id, quantite)
            VALUES ('$user_id', '$item_id', '$quantite')
            ON DUPLICATE KEY UPDATE quantite = quantite + $quantite";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["success" => true, "message" => "Item ajouté au panier"]);
} else {
    echo json_encode(["success" => false, "message" => "Item non ajouté au panier"]);
}
}
?>
