<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=afterwork', 'root', '');
   
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>


