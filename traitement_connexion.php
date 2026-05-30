<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $mdp = $_POST['mdp'];

    // Modification ici : on remplace 'mail' par 'Email'
    $sql = "SELECT * FROM Utilisateur WHERE Email = :mail"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mail' => $mail]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mdp'])) {
        // On enregistre les infos, ET SURTOUT LE RÔLE
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['Nom'];
        $_SESSION['prenom'] = $user['Prenom'];
        $_SESSION['role'] = $user['role']; // <-- La ligne magique

        // Le tri directionnel
        if ($_SESSION['role'] === 'Prestataire') {
            header('Location: dashboard_prestataire.php');
        } else {
            header('Location: index.php'); // Client normal
        }
        exit();
    }else {
        die("Email ou mot de passe incorrect.");
    }
}