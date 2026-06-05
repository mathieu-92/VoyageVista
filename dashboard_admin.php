<?php
session_start();
require_once 'config.php';

// SÉCURITÉ : Vérification que seul un Administrateur peut accéder à cette page
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Récupération de tous les utilisateurs depuis la base de données
try {
    $stmt = $pdo->query("SELECT id_utilisateur, nom, prenom, email, role FROM utilisateur ORDER BY role ASC, nom ASC");
    $utilisateurs = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; }
        .admin-container { max-width: 1000px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header-title { color: #333; border-bottom: 2px solid #007BFF; padding-bottom: 10px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; }
        tr:hover { background-color: #f1f1f1; }
        
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 0.85em; font-weight: bold; color: white; display: inline-block; text-align: center; min-width: 70px; }
        .badge-admin { background-color: #dc3545; }
        .badge-pro { background-color: #28a745; }
        .badge-client { background-color: #007BFF; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    
    <header class="top-nav" style="background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <a href="index.php" style="text-decoration: none; color: #007BFF; font-weight: bold;">
            <i class="fa-solid fa-arrow-left"></i> Retour à l'accueil
        </a>
    </header>

    <main class="admin-container">
        <h2 class="header-title"><i class="fa-solid fa-users-gear"></i> Gestion des Utilisateurs</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'droits_retires'): ?>
            <div class="alert-success">
                <i class="fa-solid fa-check-circle"></i> Les permissions spéciales ont été retirées avec succès. L'utilisateur est redevenu Client.
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions de sécurité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id_utilisateur']) ?></td>
                        <td><strong><?= htmlspecialchars($user['nom']) ?> <?= htmlspecialchars($user['prenom']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php if ($user['role'] === 'Admin'): ?>
                                <span class="badge badge-admin">Admin</span>
                            <?php elseif ($user['role'] === 'Prestataire'): ?>
                                <span class="badge badge-pro">Pro</span>
                            <?php else: ?>
                                <span class="badge badge-client">Client</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($user['role'] === 'Admin' || $user['role'] === 'Prestataire') && $user['id_utilisateur'] != $_SESSION['id_utilisateur']): ?>
                                
                                <form action="retrait_permission.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="id_cible" value="<?= $user['id_utilisateur'] ?>">
                                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir retirer les droits de cet utilisateur ?');" style="background-color: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 0.9em; transition: 0.2s;">
                                        <i class="fa-solid fa-user-shield"></i> Rétrograder
                                    </button>
                                </form>

                            <?php elseif ($user['id_utilisateur'] == $_SESSION['id_utilisateur']): ?>
                                <span style="color: #888; font-style: italic;"><i class="fa-solid fa-lock"></i> Votre compte</span>
                            <?php else: ?>
                                <span style="color: #aaa;">Aucune action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    
</body>
</html>