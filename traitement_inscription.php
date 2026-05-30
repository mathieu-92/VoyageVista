<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['mail'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $date_nais = $_POST['datedenaissance'];
    
    // NOUVEAU : On récupère le rôle choisi par l'utilisateur
    // Si pour une raison quelconque ça n'a pas été envoyé, on met "Client" par défaut
    $role = isset($_POST['role']) ? $_POST['role'] : 'Client';

    try {
        // La requête inclut maintenant la date de naissance ET le rôle
        $sql = "INSERT INTO Utilisateur (nom, prenom, Email, mdp, datedenaissance, role) 
                VALUES (:nom, :prenom, :mail, :mdp, :date_nais, :role)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':mdp' => $mdp,
            ':date_nais' => $date_nais,
            ':role' => $role // Ajout de la variable rôle ici
        ]);

        header('Location: connexion.php?succes=1');
        exit();
    } catch (\PDOException $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
    }
}
?>