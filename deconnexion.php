<?php
session_start();

// S'assurer que la session est bien démarrée avant de la détruire
if (session_id()) {
    // Réinitialiser toutes les variables de session
    $_SESSION = array();

    // Si un cookie de session existe, on le supprime
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Détruire la session
    session_destroy();
}

// Redirection après la déconnexion
header("Location: connexion.php");  // Redirige l'utilisateur vers la page de connexion après déconnexion
exit;
?>
