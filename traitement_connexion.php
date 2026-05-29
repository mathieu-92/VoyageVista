<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $mdp = $_POST['mdp'];

    $sql = "SELECT * FROM Utilisateur WHERE mail = :mail";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mail' => $mail]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mdp'])) {
        // Succès : on enregistre les infos dans la SESSION
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        header('Location: index.php');
        exit();
    } else {
        die("Email ou mot de passe incorrect.");
    }
}