<?php
session_start();
require_once 'config.php';

// Démarrage de la requête pour récupérer les activités et leurs destinations
try {
    $sql = "SELECT a.*, d.ville, d.pays 
            FROM activite a 
            JOIN destination d ON a.id_destination = d.id_destination 
            ORDER BY d.ville ASC";
            
    $stmt = $pdo->query($sql);
    $activites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("Erreur lors de la récupération des activités : " . $e->getMessage());
}

// Pour le compteur de notifications
$notif_count = 0;
if(isset($_SESSION['id_utilisateur'])) {
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM notification WHERE id_utilisateur = ? AND lue = 0");
    $stmt_count->execute([$_SESSION['id_utilisateur']]);
    $notif_count = $stmt_count->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activités & Excursions - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS spécifique à la page Activités */
        .bg-light { background-color: #f8f9fa; }
        .text-center { text-align: center; }
        .content-layout { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-header { margin-bottom: 40px; }
        .page-header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .page-header p { color: #666; font-size: 1.1em; }

        .grid-results {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .activity-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .activity-card:hover { transform: translateY(-5px); }
        .activity-img { height: 200px; background-color: #ddd; overflow: hidden; }
        .activity-img img { width: 100%; height: 100%; object-fit: cover; }
        .activity-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .activity-tag { background: #eef6ff; color: #007BFF; padding: 5px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; align-self: flex-start; margin-bottom: 10px; }
        .activity-location { color: #666; font-size: 0.9em; margin-bottom: 15px; }
        .activity-location i { color: #dc3545; }
        .activity-desc { color: #555; font-size: 0.95em; margin-bottom: 20px; flex-grow: 1; line-height: 1.4; }
        .activity-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 15px; }
        .activity-price { font-size: 1.3em; font-weight: bold; color: #28a745; }
        .activity-duration { color: #888; font-size: 0.85em; margin-top: 3px; }
        .btn-sm { padding: 8px 15px !important; font-size: 0.9em !important; }
        
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 10px; }
    </style>
</head>
<body class="bg-light">

    <header class="top-nav">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <div class="notif-bell" style="margin-right: 20px;">
                    <a href="notifications.php" style="position: relative; color: #333; text-decoration: none;">
                        <i class="fa-regular fa-bell" style="font-size: 1.2em;"></i>
                        <?php if($notif_count > 0): ?>
                            <span style="position: absolute; top: -5px; right: -8px; background: #dc3545; color: white; border-radius: 50%; padding: 1px 5px; font-size: 0.6em; font-weight: bold;"><?= $notif_count ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="user-profile-menu">
                    <div class="profile-trigger" style="color: #333;"><i class="fa-solid fa-user-circle"></i> <span><?= htmlspecialchars($_SESSION['prenom']) ?></span></div>
                </div>
                <a href="panier.php" class="btn-primary panier-icon"><i class="fa-solid fa-shopping-cart"></i></a>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content-layout">
        <div class="page-header text-center">
            <h1>Expériences et Activités</h1>
            <p>Découvrez des activités inoubliables pour enrichir votre séjour.</p>
        </div>

        <div class="grid-results">
            <?php if(!empty($activites)): ?>
                <?php foreach($activites as $act): ?>
                    <div class="activity-card">
                        <div class="activity-img">
                            <?php if(!empty($act['image_illustration'])): ?>
                                <img src="image/<?= htmlspecialchars($act['image_illustration']) ?>" alt="<?= htmlspecialchars($act['nom'] ?? 'Activité') ?>">
                            <?php else: ?><i class="fa-solid fa-camera"></i><?php endif; ?>
                        </div>
                        <div class="activity-content">
                            <?php if(!empty($act['type'])): ?><span class="activity-tag"><?= htmlspecialchars($act['type']) ?></span><?php endif; ?>
                            <h3><?= htmlspecialchars($act['nom'] ?? 'Activité sans nom') ?></h3>
                            <div class="activity-location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($act['ville'] ?? 'Ville inconnue') ?>, <?= htmlspecialchars($act['pays'] ?? '') ?></div>
                            <p class="activity-desc"><?= !empty($act['description']) ? htmlspecialchars(substr($act['description'], 0, 100)) . '...' : 'Aucune description.' ?></p>
                            <div class="activity-footer">
                                <div>
                                    <div class="activity-price"><?= isset($act['prix']) ? htmlspecialchars($act['prix']) . ' €' : 'Gratuit' ?></div>
                                    <div class="activity-duration"><i class="fa-regular fa-clock"></i> <?= !empty($act['duree']) ? htmlspecialchars($act['duree']) : 'Non spécifiée' ?></div>
                                </div>
                                <a href="details_activite.php?id=<?= isset($act['id_activite']) ? $act['id_activite'] : '' ?>" class="btn-primary btn-sm">Réserver</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state"><h3>Aucune activité disponible.</h3></div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>