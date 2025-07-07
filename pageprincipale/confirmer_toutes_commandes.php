<?php
session_start();
include 'database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "CALL finaliserToutesCommandes(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "Toutes vos commandes ont été confirmées avec succès.";
} else {
    echo "Erreur lors de la confirmation des commandes.";
}

$stmt->close();
$conn->next_result();
?>
<br>

