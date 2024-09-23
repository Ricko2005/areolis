<?php
session_start();
require_once('bd.php');

if (isset($_POST['submit'])) {

    if (!empty($_POST['Mail']) && !empty($_POST['Password'])) {
        $Mail = htmlspecialchars($_POST['Mail']);
        $Password = htmlspecialchars($_POST['Password']);
        $hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

        // Vérifier si l'utilisateur existe déjà
        $requete = $bdd->prepare("SELECT * FROM admin WHERE Mail = ?");
        $requete->execute([$Mail]);
        $resultat = $requete->fetch(PDO::FETCH_ASSOC);

        if ($resultat) {
            // L'utilisateur existe, vérifier le mot de passe
            if (password_verify($Password, $resultat['Password'])) {
                $_SESSION['id'] = $resultat['id'];
                $_SESSION['Mail'] = $resultat['Mail'];
                $_SESSION['Password'] = $resultat['Password'];

                if ($resultat['Statut'] === 'admin') {
                    header("Location: dashboard.php?id=" . $_SESSION['id']);
                    exit();
                } else {
                    header("Location: connexion.php");
                    exit();
                }
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            // Si l'utilisateur n'existe pas, créer un nouvel utilisateur
            $requete = $bdd->prepare("INSERT INTO admin(Mail, Password) VALUES (?, ?)");
            $requete->execute([$Mail, $hashedPassword]);

            // Ré-essayer de sélectionner l'utilisateur
            $requete = $bdd->prepare("SELECT * FROM admin WHERE Mail = ?");
            $requete->execute([$Mail]);
            $resultat = $requete->fetch(PDO::FETCH_ASSOC);

            if ($resultat) {
                $_SESSION['id'] = $resultat['id'];
                $_SESSION['Mail'] = $resultat['Mail'];
                $_SESSION['Password'] = $resultat['Password'];

                if ($resultat['Statut'] === 'admin') {
                    header("Location: dashboard.php?id=" . $_SESSION['id']);
                    exit();
                } else {
                    header("Location: connexion.php");
                    exit();
                }
            } else {
                $message = "Erreur lors de la création du compte.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs!";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="Connexion.css">
    <style>
        .error {
            border: 2px solid red;
        }
    </style>
</head>
<body>
<div id="sideright">
       
           
        </div>
    <section id="connect">
       
        <div id="champ">
            <?php if (!empty($errorMessage)) {
                echo "<p style='color:red;'>$errorMessage</p>";
            } ?>
            <form action="" method="post">
                <h3>Connexion</h3>
                <p style="color:red;" id="error">
                    <?php
                    if (isset($message)) {
                        echo $message;
                    }
                    ?>
                </p>
                <div class="caseInput">
                <input type="email" name="Mail" id="mail" placeholder="Adresse mail" class="<?php echo !empty($errorMessage) ? 'error' : ''; ?>"><br><br>
                <input type="password" name="Password" id="Password" placeholder="Mot de passe" class="<?php echo !empty($errorMessage) ? 'error' : ''; ?>"><br><br>
                </div>
                <p><a href="jeton.php">Mot de passe oublié?</a></p>
                <button type="submit" name="submit">Se connecter</button>
                <button type="button" onclick="location.href='inscription.php';">Créer un compte</button>
            </form>
        </div>
    </section>
    <script>
        var phrases = [
            "Bienvenue sur AFTER WORK Saison 3"
        ];
        var chartIndex = 0;
        var phrasesIndex = 0;
        var pause = 2000;
        var interval = 100;
        function displaytext() {
            if (chartIndex < phrases[phrasesIndex].length) {
                document.getElementById("text").innerHTML += phrases[phrasesIndex].charAt(chartIndex);
                chartIndex++;
                setTimeout(displaytext, interval);
            } else {
                setTimeout(() => {
                    document.getElementById("text").innerHTML = "";
                    chartIndex = 0;
                    phrasesIndex = (phrasesIndex + 1) % phrases.length;
                    displaytext();
                }, pause);
            }
        }
        displaytext();
    </script>
</body>
</html>
