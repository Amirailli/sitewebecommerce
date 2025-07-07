<?php
session_start();
include 'database.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $commande_id = $_POST['commande_id'];
    $user_id = $_SESSION['user_id'];

    $conn->begin_transaction();

    try {
        
        $check = $conn->prepare("SELECT * FROM commande WHERE commande_id = ? AND user_id = ? AND etat != 'validée'");
        $check->bind_param("ii", $commande_id, $user_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Commande est deja validée");
        }

        $commande_data = $result->fetch_assoc();

        
        $check_hist = $conn->prepare("SELECT 1 FROM historique_commandes WHERE commande_id = ?");
        $check_hist->bind_param("i", $commande_id);
        $check_hist->execute();
        $exists = $check_hist->get_result()->num_rows > 0;

        if ($exists) {
         
            $update = $conn->prepare("UPDATE historique_commandes 
                                    SET date_annulation = NOW(), 
                                        raison = 'Commande annulée par l\'utilisateur'
                                    WHERE commande_id = ?");
            $update->bind_param("i", $commande_id);
            $update->execute();
        } else {
           
            $insert = $conn->prepare("INSERT INTO historique_commandes 
                                    (commande_id, date_commande, date_annulation, raison)
                                    VALUES (?, ?, NOW(), 'Commande annulée par l\'utilisateur')");
            $insert->bind_param("is", $commande_id, $commande_data['date_commande']);
            $insert->execute();
        }

     
        $update_commande = $conn->prepare("UPDATE commande SET etat = 'Annulée' WHERE commande_id = ?");
        $update_commande->bind_param("i", $commande_id);
        $update_commande->execute();

     
        $conn->commit();
        $_SESSION['success'] = "Commande #$commande_id annulée avec succès";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
    }

    header("Location: detailcommande.php");
    exit;
} else {
    $_SESSION['error'] = "Requête invalide";
    header("Location: detailcommande.php");
    exit;
}
?>