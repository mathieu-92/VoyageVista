<?php
session_start();
require_once 'config.php';

// 1. Récupération des hébergements avec leur destination correspondante
try {
    $sql = "SELECT h.*, d.ville, d.pays 
            FROM hebergement h 
            JOIN destination d ON h.id_destination = d.id_destination 
            ORDER BY d.ville ASC";
            
    $stmt = $pdo->query($sql);
    $hebergements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("Erreur lors de la récupération des hébergements : " . $e->getMessage());
}

// 2. Gestion du badge de notifications pour la barre de navigation
$notif_count = 0;
if (isset($_SESSION['id_utilisateur'])) {
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
    <title>Hébergements & Hôtels - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS POUR LE MENU DÉROULANT */
        .user-profile-menu { position: relative; display: inline-block; margin-right: 20px;}
        .profile-trigger { display: flex; align-items: center; gap: 8px; cursor: pointer; color: #333; padding: 5px 10px; border-radius: 20px; transition: 0.3s; }
        .profile-trigger:hover { background-color: #f1f1f1; }
        .dropdown-content { display: none; position: absolute; right: 0; top: 100%; background-color: white; min-width: 180px; box-shadow: 0 8px 16px rgba(0,0,0,0.15); border-radius: 8px; padding: 10px 0; z-index: 1000; }
        .user-profile-menu:hover .dropdown-content { display: block; }
        .dropdown-content a { color: #333; padding: 10px 20px; text-decoration: none; display: block; font-size: 0.95em; }
        .dropdown-content a:hover { background-color: #f8f9fa; }

        /* CSS DES CARTES HÔTELS */
        .hotel-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .hotel-img {
            height: 220px;
            background-color: #e9ecef;
            position: relative;
        }
        .hotel-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hotel-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .hotel-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .hotel-title {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.3em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .hotel-stars {
            color: #ffc107;
            font-size: 0.8em;
        }
        .hotel-location {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .hotel-desc {
            color: #555;
            font-size: 0.95em;
            line-height: 1.5;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .hotel-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .hotel-price-block {
            display: flex;
            flex-direction: column;
        }
        .hotel-price {
            font-size: 1.5em;
            font-weight: bold;
            color: #007BFF;
        }
        .hotel-price span {
            font-size: 0.6em;
            color: #888;
            font-weight: normal;
        }
    </style>
</head>
<body style="background-color: #f4f7f6; margin: 0; padding: 0;">

    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; padding: 10px 30px;">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        
        <div class="user-actions" style="display: flex; align-items: center;">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                
                <div class="notif-bell" style="margin-right: 20px;">
                    <a href="notifications.php" style="position: relative; color: #333; text-decoration: none;">
                        <i class="fa-regular fa-bell" style="font-size: 1.2em;"></i>
                        <?php if($notif_count > 0): ?>
                            <span style="position: absolute; top: -5px; right: -8px; background: #dc3545; color: white; border-radius: 50%; padding: 1px 5px; font-size: 0.6em; font-weight: bold;">
                                <?= $notif_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <!-- MENU DÉROULANT DYNAMIQUE -->
                <div class="user-profile-menu">
                    <div class="profile-trigger">
                        <i class="fa-solid fa-user-circle" style="font-size: 1.2em; color: #007BFF;"></i>
                        <span style="font-weight: bold;"><?= htmlspecialchars($_SESSION['prenom']) ?></span>
                        <i class="fa-solid fa-chevron-down arrow" style="font-size: 0.8em; color: #888;"></i>
                    </div>
                    <div class="dropdown-content">
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                            <a href="dashboard_admin.php" style="color: #dc3545; font-weight: bold;"><i class="fa-solid fa-hammer"></i> Dashboard Admin</a>
                        <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'Prestataire'): ?>
                            <a href="dashboard_prestataire.php" style="color: #28a745; font-weight: bold;"><i class="fa-solid fa-toolbox"></i> Espace Pro</a>
                        <?php endif; ?>
                        <a href="profil.php">Mon Profil</a>
                        <a href="gestion_reservations.php">Mes Réservations</a>
                        <div style="height: 1px; background: #eee; margin: 5px 0;"></div>
                        <a href="deconnexion.php" style="color: #dc3545;">Déconnexion</a>
                    </div>
                </div>
                
                <a href="panier.php" class="btn-primary" style="background: #007BFF; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;"><i class="fa-solid fa-shopping-cart"></i></a>
            
            <?php else: ?>
                <a href="connexion.php" class="btn-outline" style="color: #007BFF; border: 1px solid #007BFF; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content-layout" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="color: #333; font-size: 2.5em; margin-bottom: 10px;">Où souhaitez-vous dormir ?</h1>
            <p style="color: #666; font-size: 1.1em;">Trouvez l'hébergement parfait pour vos prochaines vacances.</p>
        </div>

        <div class="grid-results" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            
            <?php if(count($hebergements) > 0): ?>
                <?php foreach($hebergements as $heb): ?>
                    <div class="hotel-card">
                        
                        <div class="hotel-img">
                            <?php if(!empty($heb['type'])): ?>
                                <span class="hotel-type-badge"><i class="fa-solid fa-bed"></i> <?= htmlspecialchars($heb['type']) ?></span>
                            <?php endif; ?>
                            
                            <!-- GESTION DE L'IMAGE DYNAMIQUE -->
                            <?php 
                                // On vérifie comment s'appelle la colonne dans ta BDD (image_illustration ou juste image)
                                $image = !empty($heb['image_illustration']) ? $heb['image_illustration'] : (!empty($heb['image']) ? $heb['image'] : 'default.jpg');
                            ?>
                            <img src="image/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($heb['nom'] ?? 'Hébergement') ?>" onerror="this.src='image/default.jpg'">
                        </div>

                        <div class="hotel-content">
    

<h3 class="hotel-title">
    <?= htmlspecialchars($heb['nom_hebergement'] ?? 'Hébergement non défini') ?>
    <div class="hotel-stars">
        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
    </div>
</h3>
                            
                            <!-- LA LOCALISATION -->
                            <div class="hotel-location">
                                <i class="fa-solid fa-map-pin" style="color: #007BFF;"></i> 
                                <?= htmlspecialchars($heb['ville'] ?? '') ?>, <?= htmlspecialchars($heb['pays'] ?? '') ?>
                            </div>
                            
                            <!-- LA DESCRIPTION -->
                            <p class="hotel-desc">
                                <?php 
                                    // On vérifie comment s'appelle la colonne description dans ta BDD
                                    $desc = !empty($heb['description_courte']) ? $heb['description_courte'] : (!empty($heb['description']) ? $heb['description'] : 'Découvrez cet établissement idéalement situé pour profiter pleinement de votre séjour.');
                                    echo htmlspecialchars(substr($desc, 0, 120)) . '...';
                                ?>
                            </p>
                            
                            <div class="hotel-footer">
                                <div class="hotel-price-block">
                                  <div class="hotel-price">
    <?php 
        echo isset($heb['prix_nuit']) ? htmlspecialchars($heb['prix_nuit']) . ' €' : 'NC'; 
    ?> 
    <span>/ nuit</span>
</div>
                                </div>
                                
                                <a href="details_hebergement.php?id=<?= isset($heb['id_hebergement']) ? $heb['id_hebergement'] : '' ?>" class="btn-primary" style="background: #007BFF; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Voir les dispos</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-door-closed" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #555;">Aucun hébergement disponible.</h3>
                    <p style="color: #777;">Notre catalogue est en cours de mise à jour. Revenez très vite !</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>