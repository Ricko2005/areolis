<?php
session_start();
require_once('bd.php');

if (isset($_POST['submit'])) {
    $Mail = htmlspecialchars($_POST['Mail']);
    if (!empty($Mail)) {
        // Vérifier si l'e-mail existe dans la base de données
        $requete = $bdd->prepare("SELECT * FROM admin WHERE Mail = ?");
        $requete->execute([$Mail]);
        $resultat = $requete->fetch(PDO::FETCH_ASSOC);

        if ($resultat) {
            // Générer un jeton unique
            $token = bin2hex(random_bytes(50));
            $url = "http://votre_site.com/reset_password.php?token=" . $token;

            // Stocker le jeton dans la base de données
            $update = $bdd->prepare("UPDATE admin SET reset_token = ? WHERE Mail = ?");
            $update->execute([$token, $Mail]);

            // Envoyer l'e-mail avec le lien de réinitialisation
            $to = $Mail;
            $subject = "Réinitialisation de votre mot de passe";
            $message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : " . $url;
            $headers = "From: no-reply@votre_site.com";

            if (mail($to, $subject, $message, $headers)) {
                echo "Un e-mail de réinitialisation a été envoyé à votre adresse.";
            } else {
                echo "Une erreur est survenue lors de l'envoi de l'e-mail.";
            }
        } else {
            $error = "Aucun compte n'est associé à cet e-mail.";
        }
    } else {
        echo "Veuillez saisir votre adresse e-mail.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Réinitialisation de Mot de Passe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f9;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 100vh;
        }

        .request-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 12px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        .request-container p {
            color: #666;
            font-size: 14px;
        }

        #error {
            color: red;
            margin: 20px;
        }

        .request-container a {
            color: #5cb85c;
            text-decoration: none;
        }

        .request-container a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .request-container {
                padding: 15px;
            }

            input[type="email"],
            input[type="submit"] {
                padding: 10px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="request-container">
        <h2>Réinitialisation de Mot de Passe</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="Mail">Adresse e-mail</label>
                <input type="email" name="Mail" id="Mail" placeholder="Entrez votre adresse e-mail" required>
            </div>
            <input type="submit" name="submit" value="Envoyer un lien de réinitialisation">
        </form>
        <p>Retour à la <a href="connexion.php">connexion</a>.</p>
        <p id="error">
            <?php if (isset($error)) echo $error; ?>
        </p>
    </div>
</body>

</html>
