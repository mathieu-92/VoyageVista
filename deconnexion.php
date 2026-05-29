<?php
session_start(); // On démarre la session pour pouvoir y accéder

// 1. On détruit toutes les variables de session
$_SESSION = array();

// 2. Si on utilise des cookies de session, on les supprime
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. On détruit la session elle-même
session_destroy();

// 4. On redirige l'utilisateur vers l'accueil ou la page de connexion
header('Location: index.php');
exit();
?>