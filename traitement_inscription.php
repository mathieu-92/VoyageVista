<?php
// On appelle le fichier de connexion à la base de données qu'on a fait tout à l'heure
require_once 'config.php';

// On vérifie que le formulaire a bien été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. On récupère les données envoyées par le formulaire
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $date_naissance = $_POST['datedenaissance'];
    $role = $_POST['role'];
    $consentement = isset($_POST['consentement']) ? 1 : 0; // 1 si coché, 0 sinon

    // 2. SÉCURITÉ : On hache le mot de passe (ne jamais stocker un mot de passe en clair !)
    $mdp_hache = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    // 3. On prépare la requête SQL d'insertion
    $sql = "INSERT INTO utilisateur (Nom, Prenom, Email, mdp, datedenaissance, role, consentement) 
            VALUES (:nom, :prenom, :email, :mdp, :datedenaissance, :role, :consentement)";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        // 4. On exécute la requête avec les données
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':mdp' => $mdp_hache,
            ':datedenaissance' => $date_naissance,
            ':role' => $role,
            ':consentement' => $consentement
        ]);

        echo "<h2>Succès ! Ton compte a bien été créé bossito.</h2>";
        echo "<a href='connexion.php'>Clique ici pour te connecter</a>";
        
    } catch (\PDOException $e) {
        // En cas d'erreur (par exemple si l'email existe déjà)
        echo "Erreur lors de l'inscription : " . $e->getMessage();
    }
} else {
    // Si on arrive sur cette page sans passer par le formulaire, on redirige
    header('Location: inscription.php');
    exit();
}
?>