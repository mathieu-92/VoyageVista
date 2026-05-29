<?php
// DÉMARRAGE DE LA SESSION : Doit toujours être la TOUTE PREMIÈRE ligne avant le HTML !
session_start(); 

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    // 1. On cherche l'utilisateur dans la base de données grâce à son email
    $sql = "SELECT * FROM utilisateur WHERE Email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // 2. On vérifie si l'utilisateur existe ET si le mot de passe saisi correspond au mot de passe haché
    if ($user && password_verify($mdp, $user['mdp'])) {
        
        // SUCCÈS : On sauvegarde les infos importantes de l'utilisateur dans la variable globale $_SESSION
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['prenom'] = $user['Prenom'];
        $_SESSION['nom'] = $user['Nom'];
        $_SESSION['role'] = $user['role'];

        // 3. Redirection intelligente selon le rôle de l'utilisateur
        if ($user['role'] === 'Prestataire') {
            header('Location: dashboard_prestataire.php'); // Redirection vers le back-office
        } else {
            header('Location: index.php'); // Redirection vers la page d'accueil classique
        }
        exit(); // On arrête le script après une redirection
        
    } else {
        // ÉCHEC : L'email n'existe pas ou le mot de passe est faux
        echo "<h2>Erreur : Email ou mot de passe incorrect.</h2>";
        echo "<a href='connexion.php'>Réessayer</a>";
    }
} else {
    // Si on accède à la page sans valider le formulaire, on redirige
    header('Location: connexion.php');
    exit();
}
?>