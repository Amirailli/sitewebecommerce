<?php
 session_start();

 $image = $nom = $description = $prix = $categorie = $stock = '';
 if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $image = $_POST['imageSrc'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $stock = $_POST['Qunatitie'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    
   
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    body {
            font-family: Arial, sans-serif;
            background-color: black;
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: center;
            align-items: center;
            margin: 0;
            position: relative;
        }

        h1 {
            color: #d7ad08;
            margin-bottom: 20px;
        }

        #addItemForm {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 100%;
            max-width: 350px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            text-align: left;
            color: #333;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 2px solid #ffcc00;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-sizing: border-box;
            transition: border 0.3s;
        }

        input:focus, textarea:focus {
            border: 2px solid #ffcc00;
            outline: none;
        }

        textarea {
            resize: none;
            height: 80px;
        }

        button {
            background-color: #d7ad08;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #ffcc00;
        }
        .retoure{
             border :none;
             background-color: transparent;
             color:#c0b484 ;
             padding:5px 10px;
             font-size: 17px;
             position: absolute;
             right: 3%;
             top: 3%;
        }
    </style>
</head>
<body>
   <button class="retour" onclick="window.location.href='index.php'">Home</button>
    <div>

<form id="addItemForm"  method="POST" action="add_item.php ">
    <label for="itemImage">Image :</label>
    <input type="text" id="itemImage" name="imageSrc" required>

    <label for="itemDescription">Nom :</label>
    <input type="text" id="itemDescription" name="nom" required>

    <label for="itemPargh">Description :</label>
    <textarea id="itemPargh" name="description" required></textarea>

    <label for="itemPrice">Prix :</label>
    <input type="number" step="0.01" id="itemPrice" name="prix" required>

    <label for="itemQuantitie">Quantitie :</label>
    <input type="number" id="itemQuantitie" name="Quantitie" required>

    <label for="itemCategory">Catégorie :</label>
    <select id="itemCategory" name="categorie" required>
        <option value="premium headphones">premium headphones</option>
        <option value="pc models">pc models</option>
        <option value="keyboard & mouse set">keyboard & mouse set</option>
    </select>

    <button type="submit">Ajouter l'item</button>
</form>
    </div>

</body>
</html>

