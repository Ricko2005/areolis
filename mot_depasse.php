<?php
session_start();
require_once('bd.php');

if (isset($_POST['submit'])) {
    if (!empty($_POST['token'])) {
        $enteredToken = htmlspecialchars($_POST['token']);

        // Comparer le token entré avec celui stocké dans la base de données
        $requete = $bdd->prepare("SELECT * FROM admin WHERE Mail = ? AND reset_token = ?");
        $requete->execute([$_SESSION['reset_mail'], $enteredToken]);
        $resultat = $requete->fetch(PDO::FETCH_ASSOC);

        if ($resultat) {
            // Si le code correspond, rediriger vers la page de réinitialisation du mot de passe
            header("Location: reset_password.php");
            exit();
        } else {
            echo "Code de réinitialisation incorrect.";
        }
    } else {
        echo "Veuillez entrer votre code de réinitialisation.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrer le code</title>
</head>
<body>
    <h3>Entrer le code de réinitialisation</h3>
    <form action="" method="post">
        <label for="token">Veuillez entrer le code de réinitialisation :</label><br>
        <input type="text" name="token" required><br><br>
        <button type="submit" name="submit">Vérifier</button>
    </form>
</body>
</html>


