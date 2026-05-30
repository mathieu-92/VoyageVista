<?php
session_start();
require_once 'config.php';

// 1. SÉCURITÉ : Vérifier si l'utilisateur est bien connecté
// Si l'ID n'est pas dans la session, on le renvoie direct à la connexion
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// 2. RÉCUPÉRATION DES DONNÉES
// Même si on a le nom/prénom dans la session, c'est mieux d'aller chercher
// toutes les infos fraîches dans la base de données (comme la date de naissance)
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
        /* Un petit style supplémentaire exclusif pour la carte profil si besoin */
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
    </style>
</head>
<body class="auth-page"> <div class="auth-container" style="max-width: 600px;">
        <div class="auth-header">
            <h2><i class="fa-solid fa-circle-user"></i> Espace Membre</h2>
            <p>Bienvenue sur votre profil, <?= htmlspecialchars($user['Prenom']) ?> !</p>
        </div>

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

        <div style="margin-top: 25px; display: flex; justify-content: space-between;">
            <a href="index.php" class="btn-primary" style="text-decoration: none; text-align: center; flex: 1; margin-right: 10px;">
                <i class="fa-solid fa-house"></i> Retour à l'accueil
            </a>
            <a href="deconnexion.php" class="btn-primary" style="background-color: #dc3545; text-decoration: none; text-align: center; flex: 1; margin-left: 10px;">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>

</body>
</html>