<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['mail'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $date_nais = $_POST['datedenaissance'];

    try {
        // La requête inclut maintenant la date de naissance
        $sql = "INSERT INTO Utilisateur (nom, prenom, Email, mdp, datedenaissance) 
                VALUES (:nom, :prenom, :mail, :mdp, :date_nais)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':mdp' => $mdp,
            ':date_nais' => $date_nais
        ]);

        header('Location: connexion.php?succes=1');
        exit();
    } catch (\PDOException $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
    }
}
?>