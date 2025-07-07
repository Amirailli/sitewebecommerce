<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Veuillez vous connecter pour consulter le panier.'); window.location.href = 'index.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT panier.item_id, item.imageSrc, item.nom, item.description, item.prix, panier.quantite
        FROM panier 
        JOIN item  ON panier.item_id = item.item_id
        WHERE panier.user_id = '$user_id'";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="Ecommarce.css">
</head>
<body>
<div class="addition" style="text-align: center; display: flex; flex-direction: row;">
<div class="retour" onclick="window.location.href = 'historiquecommande.php'">
    your command
</div>
<div class="retour" onclick="window.location.href = 'detailcommande.php'">
    detail command
</div>
</div>
    <div id="panierContainer">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert-message">
                <?= $_SESSION['message'] ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (mysqli_num_rows($result) === 0): ?>
            <p class="aucun">Votre panier est vide.</p>
        <?php else: ?>
            <button class="commander" style="display: flex; align-items: center; padding: 5px 10px; border-radius: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; margin-bottom: 20px;" onclick="passerCommande()" >Commander</button>
            <?php while ($item = mysqli_fetch_assoc($result)): ?>
                <div class="list">
                    <div class="groupe">
                        <img class="gmi" src="<?= $item['imageSrc'] ?>" width="100" />
                        <div class="team">
                            <p><?= htmlspecialchars($item['nom']) ?></p>
                            <p class="quantite">quantité : <span class="qunt"><?= $item['quantite'] ?></span></p>
                            <p class="price"><?= $item['prix'] ?> DA</p>
                            <button onclick="window.location.href = 'details.php?id=<?= $item['item_id'] ?>'">Voir détails</button>
                        </div>
                    </div>
                    <button class="supp" onclick="supprimerItem(<?= $item['item_id'] ?>)" >delete</button>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
 <script>
    function supprimerItem(itemId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet article du panier ?")) {
       
        fetch('supprimer_panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'item_id=' + itemId
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
              
                window.location.reload();
            } else {
                alert("Erreur lors de la suppression : " + data);
            }
        })
        .catch(error => {
            alert("Erreur : " + error);
        });
    }
}
function passerCommande() {
    if (confirm("Confirmez-vous cette commande ?")) {
        fetch('passer_commande.php', {
            method: 'POST'
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .then(data => {
            if (data) alert(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
 </script>
</body>
</html>


