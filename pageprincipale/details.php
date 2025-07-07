<?php
session_start();
include 'database.php';

if (!isset($_GET['id'])) {
    echo "ID du item manquant.";
    exit;
}

$id = intval($_GET['id']); 
$sql = "SELECT * FROM item WHERE item_id = $id";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if (!$item) {
    echo "Produit non trouvé.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=shopping_cart" />
    <link rel="stylesheet" href="Ecommarce.css">
</head>
<body>
  <div class="det">
    <div class="details">
        <div class="both">
        <button class="retour" onclick="window.location.href = 'index.php'">Home</button>
        <span class="material-symbols-outlined" onclick="panier()">
            shopping_cart
        </span>
        </div>
        <div class="deleft">
          <img src="<?php echo $item['imageSrc']; ?>"  id="itemImage" alt="Image du produit">
        </div>
        <div class="deright">
          <p  id="itemDescription" ><?php echo $item['nom']; ?></p>
          <p id="prg" ><?php echo $item['description']; ?></p>
          <h5 d="itemPrice" ><?php echo $item['prix']; ?> DA</h5>
          <form id="addPanierForm" method="POST" action="ajouter_panier.php" style="display:none;">
          <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
          <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>"> <!-- suppose que tu stockes l'utilisateur connecté -->
          <input type="hidden" name="quantite" value="1">
          </form>

         <div class="panier">
           <div class="qtyplusmoins">
             <button class="qty-btn minus"  onclick="decrement()" >-</button>
             <input type="number" id="quantityInput" value="0" min="0" max="99" class="qty-input">
             <button class="qty-btn plus" onclick="increment()" >+</button>
           </div>
           <div>
             <button class="add-to-cart-btn" onclick="addToCart()">add to panier</button>
           </div>
         </div>
        </div>
     </div>
       
<script>
 
function panier(){
  window.location.href = "consulterpanier.php"
}
function increment() {
  const input = document.getElementById("quantityInput");
  let current = parseInt(input.value);
  if (current < 99) {
    input.value = current + 1;
  }
}

function decrement() {
  const input = document.getElementById("quantityInput");
  let current = parseInt(input.value);
  if (current > 0) {
    input.value = current - 1;
  }
}
function addToCart() {
    const quantity = document.getElementById("quantityInput").value;
    const form = document.getElementById("addPanierForm");

    form.querySelector('input[name="quantite"]').value = quantity;
    form.submit(); 
}

</script>
</body>
</html>