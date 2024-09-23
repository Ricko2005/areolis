<?php



require_once("bd.php");
session_start();

try {
    // Préparation et exécution de la requête pour récupérer les données
    $requete = $bdd->query("SELECT Date, Edition, Lieu FROM event ORDER BY id DESC LIMIT 1");
    $data = $requete->fetch(PDO::FETCH_ASSOC);

    // Vérification des données récupérées
    if ($data) {
        $Date = $data['Date'];
        $Edition = $data['Edition'];
        $Lieu = $data['Lieu'];
    } else {
        $Date = 'Non spécifié';
        $Edition = 'Non spécifié';
        $Lieu = 'Non spécifié';
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        #scrollTopBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #B0A536;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 20px;
            font-size: 18px;
            cursor: pointer;
            z-index: 1000;
            font-weight: bold;
        }
        #scrollTopBtn:hover {
            background-color: #555;
        }





    </style>
</head>
<body>

<header>

<!-- =================== Navbar =================== -->
<div id="menu">
    <ul id="nav">
        <li><a href="#">Accueil</a></li>
        <li><a href="#description">Concept</a></li>
        <li><a href="#Partenaire">Nos partenaires</a></li>
    </ul>

    <div id="ticket">
        <span><a href="ticket.php">Espace Ticket<ion-icon name="ticket-outline"></ion-icon></a></span>
    </div>
</div>

<!-- =================== Après Navbar =========================== -->

<section id="info">

    <div class="fashion">
        <h1>FASHION <br> AFTER WORK</h1>
    </div>

    <div class="event">
        <h3>Date de l'événement : <span style="color: #fff;"><?= htmlspecialchars($Date) ?></span></h3><br>
        <span>Lieu: <span style="color:#fff;"><?= htmlspecialchars($Lieu) ?></span></span><br><br>
        <span>Episode: <span style="color:#fff;"><?= htmlspecialchars($Edition) ?></span></span>
    </div>

</section>

</header>
  <!-- ======================== Après Header ========================= -->

<main>

<section id="mode">

    <div class="image">
        <img src="img/afters.jpg" alt="">
    </div>
    <div id="description" class="description">
        <span>Fashion Afterwork est un événement dynamique qui rassemble les principaux acteurs de la mode africaine. Il offre une plateforme unique pour les créateurs, stylistes, et amateurs de mode pour échanger des idées et découvrir les dernières tendances. L'atmosphère est à la fois festive et professionnelle, propice aux réseautages et collaborations futures. Des défilés de mode, des ateliers interactifs et des discussions inspirantes sont au programme, mettant en avant la richesse et la diversité de la mode africaine. Cet événement est une véritable célébration de la créativité et de de l'innovation, renforçant les liens au sein de la communauté de la mode africaine.</span>
    </div>

</section>

<section id="Partenaire">
    <h1>Partenaires officiels</h1>
    <img src="img/areolis.png" alt="" width="200px">
    <img src="img/logond.png" alt="" width="200px">
    <img src="img/kwold.jpg" alt="" width="200px">
</section>
</main>

<!-- Bouton Retour en haut -->
<button id="scrollTopBtn" title="">&#8593</button>

<?php
require_once("footer.php");
?>

<script>
    var scrollTopBtn = document.getElementById("scrollTopBtn");

    window.onscroll = function() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollTopBtn.style.display = "block";
        } else {
            scrollTopBtn.style.display = "none";
        }
    };

    scrollTopBtn.onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
</script>

</body>
</html>

