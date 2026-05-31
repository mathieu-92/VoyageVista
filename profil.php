<?php
session_start();
require_once 'config.php';

// 1. SÉCURITÉ : Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_user = $_SESSION['id_utilisateur'];

try {
    $sql = "SELECT * FROM Utilisateur WHERE id_utilisateur = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_user]);
    $user = $stmt->fetch();
} catch (\PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: left;
            margin-top: 20px;
        }
        .profile-card p {
            font-size: 1.1em;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .profile-card i {
            color: #007BFF;
            width: 25px;
        }
        /* Style pour le formulaire caché */
        .edit-form-container {
            display: <?= isset($_GET['succes']) ? 'block' : 'none' ?>; 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            margin-top: 20px; 
            border: 1px solid #ddd;
        }
    </style>
</head>
<body class="auth-page"> 
    <div class="auth-container" style="max-width: 600px;">
        <div class="auth-header">
            <h2><i class="fa-solid fa-circle-user"></i> Espace Membre</h2>
            <p>Bienvenue sur votre profil, <?= htmlspecialchars($user['Prenom']) ?> !</p>
        </div>

        <?php if(isset($_GET['succes']) && $_GET['succes'] == 1): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <i class="fa-solid fa-check"></i> Profil mis à jour avec succès !
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <p><i class="fa-solid fa-id-card"></i> <strong>Nom :</strong> <?= htmlspecialchars($user['Nom']) ?></p>
            <p><i class="fa-solid fa-user"></i> <strong>Prénom :</strong> <?= htmlspecialchars($user['Prenom']) ?></p>
            <p><i class="fa-solid fa-envelope"></i> <strong>Email :</strong> <?= htmlspecialchars($user['Email']) ?></p>
            
            <?php if(!empty($user['datedenaissance'])): ?>
                <p><i class="fa-solid fa-calendar"></i> <strong>Date de naissance :</strong> <?= htmlspecialchars($user['datedenaissance']) ?></p>
            <?php endif; ?>

            <?php if(!empty($user['role'])): ?>
                <p><i class="fa-solid fa-briefcase"></i> <strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
            <?php endif; ?>
        </div>

        <button type="button" onclick="document.getElementById('edit-form').style.display='block'" class="btn-primary full-width" style="margin-top: 15px; background-color: #28a745; border: none;">
            <i class="fa-solid fa-pen"></i> Modifier mes informations
        </button>

        <div id="edit-form" class="edit-form-container">
            <h3 style="margin-bottom: 15px; text-align: left;">Mettre à jour mon profil</h3>
            <form action="traitement_profil.php" method="POST" style="text-align: left;">
                
                <div class="input-group" style="margin-bottom: 10px;">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['Nom']) ?>" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="input-group" style="margin-bottom: 10px;">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['Prenom']) ?>" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="input-group" style="margin-bottom: 10px;">
                    <label>Date de naissance</label>
                    <input type="date" name="datedenaissance" value="<?= htmlspecialchars($user['datedenaissance']) ?>" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="input-group" style="margin-bottom: 10px;">
                    <label>Email</label>
                    <input type="email" name="mail" value="<?= htmlspecialchars($user['Email']) ?>" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="input-group" style="margin-bottom: 15px;">
                    <label>Nouveau mot de passe (optionnel)</label>
                    <input type="password" name="mdp" placeholder="Laissez vide pour conserver l'actuel" style="width: 100%; padding: 8px;">
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Enregistrer</button>
            </form>
        </div>

        <div style="margin-top: 25px; display: flex; justify-content: space-between;">
            <a href="index.php" class="btn-primary" style="text-decoration: none; text-align: center; flex: 1; margin-right: 10px; background-color: #6c757d;">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
            <a href="deconnexion.php" class="btn-primary" style="background-color: #dc3545; text-decoration: none; text-align: center; flex: 1; margin-left: 10px;">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>
</body>
</html>