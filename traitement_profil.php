<?php
session_start();
require_once 'config.php';

// Sécurité : On s'assure qu'on arrive bien depuis le formulaire et qu'on est connecté
if (!isset($_SESSION['id_utilisateur']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['id_utilisateur'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$mail = $_POST['mail'];
$date_nais = $_POST['datedenaissance'];
$nouveau_mdp = $_POST['mdp'];   

try {
    // Si l'utilisateur a tapé un nouveau mot de passe
    if (!empty($nouveau_mdp)) {
        $mdp_hache = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        
        $sql = "UPDATE Utilisateur 
                SET Nom = :nom, Prenom = :prenom, Email = :mail, datedenaissance = :date_nais, mdp = :mdp 
                WHERE id_utilisateur = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':date_nais' => $date_nais,
            ':mdp' => $mdp_hache,
            ':id' => $id_user
        ]);
        
    } else {
        // S'il a laissé le mot de passe vide, on ne met à jour que les autres infos
        $sql = "UPDATE Utilisateur 
                SET Nom = :nom, Prenom = :prenom, Email = :mail, datedenaissance = :date_nais 
                WHERE id_utilisateur = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':date_nais' => $date_nais,
            ':id' => $id_user
        ]);
    }

    // On met à jour la session en direct pour que la barre de navigation affiche le bon prénom
    $_SESSION['prenom'] = $prenom;

    // Redirection vers le profil avec un message de succès
    header('Location: profil.php?succes=1');
    exit();

} catch (\PDOException $e) {
    die("Erreur lors de la modification : " . $e->getMessage());
}
?>