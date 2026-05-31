<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// 1. SÉCURITÉ : Réservé aux Prestataires
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Prestataire') {
    header('Location: index.php');
    exit();
}

$message_succes = "";
$message_erreur = "";

// 2. SUPPRESSION D'UNE OFFRE (Validation de la grille)
if (isset($_GET['supprimer'])) {
    $id_a_supprimer = (int)$_GET['supprimer'];
    
    try {
        $stmt_delete = $pdo->prepare("DELETE FROM destination WHERE id_destination = ?");
        $stmt_delete->execute([$id_a_supprimer]);
        $message_succes = "La destination a été supprimée du catalogue avec succès.";
    } catch (\PDOException $e) {
        // Sécurité : Si la destination est liée à un vol ou une activité (clé étrangère), on empêche la suppression brutale
        $message_erreur = "Impossible de supprimer cette destination : des vols ou activités y sont encore liés.";
    }
}

// 3. RÉCUPÉRATION DU CATALOGUE
try {
    $stmt = $pdo->query("SELECT * FROM destination ORDER BY id_destination DESC");
    $destinations = $stmt->fetchAll();
    $total_destinations = count($destinations);
} catch (\PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Prestataire - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-container { max-width: 1100px; margin: 40px auto; padding: 20px; }
        .stats-cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: white; border-radius: 10px; padding: 20px; flex: 1;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #007BFF;
            display: flex; align-items: center; justify-content: space-between;
        }
        .stat-card h3 { margin: 0; color: #666; font-size: 1em; }
        .stat-card .number { font-size: 2em; font-weight: bold; color: #333; margin-top: 5px; }
        .stat-card i { font-size: 2.5em; color: #e9ecef; }
        
        .data-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .data-table th { background-color: #f8f9fa; color: #555; font-weight: bold; text-transform: uppercase; font-size: 0.85em; }
        .data-table tr:hover { background-color: #f8f9fa; }
        .data-img { width: 50px; height: 50px; border-radius: 5px; object-fit: cover; }
        
        .btn-sm { padding: 5px 10px; font-size: 0.85em; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-edit { background: #ffc107; color: #212529; }
        .btn-delete { background: #dc3545; color: white; }
        
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
    </style>
</head>
<body style="background-color: #f4f7f6;">

    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        <div class="user-actions">
            <span style="color: #555; font-weight: bold;">
                <i class="fa-solid fa-user-tie"></i> Espace Prestataire
            </span>
            <a href="index.php" class="btn-outline" style="margin-left: 15px;">Retour au site</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2><i class="fa-solid fa-chart-line"></i> Tableau de bord</h2>
            <a href="ajouter_offre.php" class="btn-primary" style="background-color: #28a745; border: none;">
                <i class="fa-solid fa-plus"></i> Nouvelle destination
            </a>
        </div>

        <?php if (!empty($message_succes)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <?= $message_succes ?></div>
        <?php endif; ?>
        <?php if (!empty($message_erreur)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= $message_erreur ?></div>
        <?php endif; ?>

        <div class="stats-cards">
            <div class="stat-card">
                <div>
                    <h3>Total Destinations</h3>
                    <div class="number"><?= $total_destinations ?></div>
                </div>
                <i class="fa-solid fa-map-location-dot" style="color: #007BFF;"></i>
            </div>
            <div class="stat-card" style="border-left-color: #28a745;">
                <div>
                    <h3>Réservations Actives</h3>
                    <div class="number">--</div>
                </div>
                <i class="fa-solid fa-calendar-check" style="color: #28a745;"></i>
            </div>
            <div class="stat-card" style="border-left-color: #ffc107;">
                <div>
                    <h3>Chiffre d'affaires</h3>
                    <div class="number">-- €</div>
                </div>
                <i class="fa-solid fa-wallet" style="color: #ffc107;"></i>
            </div>
        </div>

        <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="padding: 20px; border-bottom: 1px solid #eee; background: #fff;">
                <h3 style="margin: 0; color: #333;">Gestion du catalogue</h3>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_destinations > 0): ?>
                        <?php foreach ($destinations as $dest): ?>
                            <tr>
                                <td style="color: #888;">#<?= $dest['id_destination'] ?></td>
                                <td>
                                    <img src="image/<?= htmlspecialchars($dest['image_illustration']) ?>" alt="img" class="data-img" onerror="this.src='image/default.jpg'">
                                </td>
                                <td><strong><?= htmlspecialchars($dest['ville']) ?></strong></td>
                                <td><?= htmlspecialchars($dest['pays']) ?></td>
                                <td style="text-align: right;">
                                    <a href="#" class="btn-sm btn-edit" onclick="alert('Fonctionnalité de modification à venir !'); return false;"><i class="fa-solid fa-pen"></i> Éditer</a>
                                    
                                    <a href="dashboard_prestataire.php?supprimer=<?= $dest['id_destination'] ?>" class="btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?= htmlspecialchars($dest['ville']) ?> ?');">
                                        <i class="fa-solid fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #888;">Aucune destination dans le catalogue.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>