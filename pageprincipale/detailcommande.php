<?php 
session_start();
include 'database.php';

if (isset($_SESSION['success'])) {
    echo '<div style="color: green; margin: 10px 0; padding: 10px; border: 1px solid green; border-radius: 5px;">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div style="color: red; margin: 10px 0; padding: 10px; border: 1px solid red; border-radius: 5px;">'.$_SESSION['error'].'</div>';
    unset($_SESSION['error']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sqlCommandes = "SELECT DISTINCT commande_id, etat FROM commande WHERE user_id = ? AND etat != 'Annulée'"; // Ne pas afficher les annulées
$stmt = $conn->prepare($sqlCommandes);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultCommandes = $stmt->get_result();

if ($resultCommandes->num_rows > 0) {
    while ($rowCommande = $resultCommandes->fetch_assoc()) {
        $commande_id = $rowCommande['commande_id'];
        $etat = $rowCommande['etat'];

        $sqlDetails = "CALL afficherDetailCommande(?)";
        $stmtDetails = $conn->prepare($sqlDetails);
        $stmtDetails->bind_param("i", $commande_id);
        $stmtDetails->execute();
        $resultDetails = $stmtDetails->get_result();

        echo '<div class="detailscommande" style="position: relative; padding: 15px; border: 1px solid #ccc; border-radius: 10px; margin-top: 20px; margin-bottom: 30px;">';
        
        echo '<h3 style="margin-top: 0;">Commande N°' . htmlspecialchars($commande_id) . ' : ';
        echo '<span style="color: ' . ($etat == 'Annulée' ? 'red' : 'green') . '">(' . htmlspecialchars($etat) . ')</span></h3>';
        
        if ($resultDetails->num_rows > 0) {
            $rowDetail = $resultDetails->fetch_assoc();
            echo '<div style="margin-bottom: 40px; font-weight: normal;">' . nl2br(htmlspecialchars($rowDetail['message'])) . '</div>';
        } else {
            echo '<div style="font-weight: normal;">Aucun détail trouvé pour cette commande.</div>';
        }
        
        
        if ($etat != 'Annulée') {
            echo '<div style="text-align: right; margin-top: 10px;">';
            echo '<form action="annuler_commande.php" method="post" onsubmit="return confirm(\'Êtes-vous sûr de vouloir annuler cette commande ?\')">';
            echo '<input type="hidden" name="commande_id" value="' . $commande_id . '">';
            echo '<input type="submit" value="Annuler la commande" style="padding: 8px 15px; background-color: #ff4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">';
            echo '</form>';
            echo '</div>';
        }
        
        echo '</div>';

        $stmtDetails->close();
        $conn->next_result();
    }
} else {
    echo "<p style='font-weight: normal;'>Aucune commande trouvée.</p>";
}

echo '<div style="margin-top: 30px; text-align: center;">'; 
echo '<form action="confirmer_toutes_commandes.php" method="post">';
echo '<input type="submit" value="Confirmer Commandes" style="padding: 8px 15px; background-color:rgb(18, 206, 109); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">';
echo '</form>';
echo '</div>';

$stmt->close();
?>