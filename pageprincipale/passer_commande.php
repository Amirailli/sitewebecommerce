<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Créer la commande principale
$sql_commande = "INSERT INTO commande (user_id, date_commande, etat) 
                 VALUES (?, NOW(), 'en attente')";
$stmt = $conn->prepare($sql_commande);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$commande_id = $stmt->insert_id;
$stmt->close();

$sql_historique = "INSERT INTO historique_commandes (commande_id, user_id, date_commande, date_annulation, raison) 
                   VALUES (?, ?, NOW(), NULL, NULL)";
$stmt_historique = $conn->prepare($sql_historique);
$stmt_historique->bind_param("ii", $commande_id, $user_id);
$stmt_historique->execute();
$stmt_historique->close();

// 2. Récupérer les articles du panier
$sql_panier = "SELECT p.item_id, p.quantite, i.prix 
               FROM panier p
               JOIN item i ON p.item_id = i.item_id
               WHERE p.user_id = ?";
$stmt = $conn->prepare($sql_panier);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {
    $sql_detail = "INSERT INTO detailcommandes (commande_id, item_id, quantite, prix)
                   VALUES (?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bind_param("iiid", $commande_id, $row['item_id'], $row['quantite'], $row['prix']);
    $stmt_detail->execute();
    $stmt_detail->close();
}

$_SESSION['message'] = "Votre commande #$commande_id a été passée avec succès!";
header("Location: consulterpanier.php");
exit;
?>