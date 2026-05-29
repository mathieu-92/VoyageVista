<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['mail'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT); // Cryptage du mot de passe

    try {
        $sql = "INSERT INTO Utilisateur (nom, prenom, mail, mdp) VALUES (:nom, :prenom, :mail, :mdp)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':mdp' => $mdp
        ]);

        header('Location: connexion.php?succes=1');
        exit();
    } catch (\PDOException $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
    }
}