<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $nom = mysqli_real_escape_string($conn, $_POST['nom'] ?? '');
    $mot_de_passe = mysqli_real_escape_string($conn, $_POST['mot_de_passe'] ?? '');
    $rolee = mysqli_real_escape_string($conn, $_POST['rolee'] ?? 'user'); // Valeur par défaut 'user'

  
    if (empty($nom) || empty($mot_de_passe)) {
        echo "<script>alert('Tous les champs sont obligatoires.'); window.location.href='register.php';</script>";
        exit();
    }

   
    if (!in_array($rolee, ['user', 'admin'])) {
        $rolee = 'user'; 
    }

    $check = mysqli_query($conn, "SELECT * FROM user WHERE nom = '$nom'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Ce nom d\\'utilisateur existe déjà.'); window.location.href='index.php';</script>";
        exit();
    }


    $query = "INSERT INTO user (nom, mot_de_passe, rolee) VALUES ('$nom', '$mot_de_passe', '$rolee')";
    if (mysqli_query($conn, $query)) {
        if ($rolee === 'admin') {
            echo "<script>alert('Inscription réussie !'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('Inscription réussie !'); window.location.href='index.php';</script>";
        }
    } else {
        $error = mysqli_error($conn);
        echo "<script>alert('Erreur lors de l\\'inscription: $error'); window.location.href='register.php';</script>";
    }
    
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="Ecommarce.css">
</head>
<body>
<div id="loginBox">
        <h2>Créer un compte</h2>
        <form id="loginForm" method="POST">
            <label for="nom">Nom d'utilisateur :</label>
            <input type="text" name="nom" class="ipt" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" name="mot_de_passe" class="ipt" required>

            <label for="rolee">Rôle :</label>
            <select name="rolee" class="ipt" required>
                <option value="">-- Sélectionnez un rôle --</option>
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>

            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>
