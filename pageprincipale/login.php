<?php
session_start();
include 'database.php';

header('Content-Type: application/json'); 

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    $query = "SELECT * FROM user WHERE nom = '$username' AND mot_de_passe = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["rolee"] = $user["rolee"];
        $_SESSION["nom"] = $user["nom"];

        $response['success'] = true;
        $response['role'] = $user["rolee"];
        
    } else {
        $response['success'] = false;
        $response['message'] = "Utilisateur non trouvé. Redirection vers l'inscription...";
        $response['redirect'] = 'register.php';
    }

    echo json_encode($response);
    exit();
}
?>
