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
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        header('Location: index.php');
        exit();
    } else {
        die("Email ou mot de passe incorrect.");
    }
}