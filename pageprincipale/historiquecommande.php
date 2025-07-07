<?php
session_start();
include 'database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "CALL afficherHistoriqueClient(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<div class="histoirecommande">';
if ($result->num_rows > 0) {
    
    while ($row = $result->fetch_assoc()) {
        echo '<div class="detailscommande" style="white-space: pre-line; padding: 15px; border: 1px solid #ccc; border-radius: 10px; margin-top: 20px;">';
        echo 'Commande N°' . htmlspecialchars($row['commande_id']) .
             ' | Date : ' . htmlspecialchars($row['date_commande']);
             
       
        echo '<br>';
        echo '</div>';
    }
   
    

} else {
    echo 'Aucune commande trouvée.';
}
echo '</div>';

$stmt->close();
$conn->next_result(); 
?>
