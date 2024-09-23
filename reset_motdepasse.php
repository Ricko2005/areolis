<?php
session_start();
require_once('bd.php');

if (isset($_POST['submit'])) {
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $newPassword = htmlspecialchars($_POST['new_password']);
        $confirmPassword = htmlspecialchars($_POST['confirm_password']);

        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe et supprimer le jeton
            $update = $bdd->prepare("UPDATE admin SET Password = ?, reset_token = NULL WHERE Mail = ?");
            $update->execute([$hashedPassword, $_SESSION['reset_mail']]);

            echo "Votre mot de passe a été réinitialisé avec succès.";
            // Rediriger vers la page de connexion ou tableau de bord
            header("Location: connexion.php");
            exit();
        } else {
            echo "Les mots de passe ne correspondent pas.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
</head>
<body>
    <h3>Réinitialiser le mot de passe</h3>
    <form action="" method="post">
        <label for="new_password">Nouveau mot de passe :</label><br>
        <input type="password" name="new_password" required><br><br>
        <label for="confirm_password">Confirmez le mot de passe :</label><br>
        <input type="password" name="confirm_password" required><br><br>
        <button type="submit" name="submit">Réinitialiser</button>
    </form>
</body>
</html>
