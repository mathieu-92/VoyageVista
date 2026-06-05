<?php
session_start();
require_once 'config.php';

// 1. Récupération des filtres
$ville_recherchee = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$ville_depart = isset($_GET['origine']) ? trim($_GET['origine']) : ''; 
$type_recherche = isset($_GET['type_recherche']) ? $_GET['type_recherche'] : 'vols';
$depart = isset($_GET['depart']) ? $_GET['depart'] : '';
$retour = isset($_GET['retour']) ? $_GET['retour'] : '';
$voyageurs = isset($_GET['voyageurs']) ? (int)$_GET['voyageurs'] : 1;

// NOUVEAU : Récupération du filtre de budget
$budget_max = (isset($_GET['budget_max']) && $_GET['budget_max'] !== '') ? (float)$_GET['budget_max'] : null;

$aujourdhui = date('Y-m-d');

// POINT GRILLE : Gestion des dates de trajet (Incohérences refusées)
if (!empty($depart) && $depart < $aujourdhui) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:#dc3545;'>Erreur : La date de départ ne peut pas être dans le passé.</h2>
            <a href='index.php' style='padding:10px 20px; background:#007BFF; color:white; text-decoration:none; border-radius:5px;'>Retour à l'accueil</a>
         </div>");
}
if (!empty($depart) && !empty($retour) && $retour <= $depart) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:#dc3545;'>Erreur : La date de retour doit être après la date de départ.</h2>
            <a href='index.php' style='padding:10px 20px; background:#007BFF; color:white; text-decoration:none; border-radius:5px;'>Retour à l'accueil</a>
         </div>");
}

$resultats = [];

try {
    // 2. AIGUILLAGE DU MOTEUR DE RECHERCHE
    if ($type_recherche === 'vols') {
        $sql = "SELECT * FROM vol WHERE aeroport_arrivee LIKE :recherche AND places_disponible >= :voyageurs";
        $params = [':recherche' => '%' . $ville_recherchee . '%', ':voyageurs' => $voyageurs];
        
        if (!empty($ville_depart)) { 
            $sql .= " AND aeroport_depart LIKE :origine"; 
            $params[':origine'] = '%' . $ville_depart . '%';
        }
        
        if (!empty($depart)) { 
            $sql .= " AND DATE(datedepart) >= :date_depart"; 
            $params[':date_depart'] = $depart;
        }
        
        // FILTRE BUDGET
        if ($budget_max) {
            $sql .= " AND prix <= :budget";
            $params[':budget'] = $budget_max;
        }
        
        $sql .= " ORDER BY datedepart ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

    } elseif ($type_recherche === 'hotels') {
        $sql = "SELECT h.*, d.ville, d.pays FROM hebergement h 
                JOIN destination d ON h.id_destination = d.id_destination 
                WHERE (d.ville LIKE :ville OR d.pays LIKE :pays)";
        $params = [':ville' => '%' . $ville_recherchee . '%', ':pays' => '%' . $ville_recherchee . '%'];
        
        // FILTRE BUDGET
        if ($budget_max) {
            $sql .= " AND h.prix_par_nuit <= :budget";
            $params[':budget'] = $budget_max;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

    } elseif ($type_recherche === 'activites') {
        $sql = "SELECT a.*, d.ville, d.pays FROM activite a 
                JOIN destination d ON a.id_destination = d.id_destination 
                WHERE (d.ville LIKE :ville OR d.pays LIKE :pays)";
        $params = [':ville' => '%' . $ville_recherchee . '%', ':pays' => '%' . $ville_recherchee . '%'];
        
        // FILTRE BUDGET
        if ($budget_max) {
            $sql .= " AND a.prix <= :budget";
            $params[':budget'] = $budget_max;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

    } elseif ($type_recherche === 'vehicules') {
        $sql = "SELECT * FROM vehicule WHERE disponibilite = 1 AND (marque LIKE :recherche1 OR type_vehicule LIKE :recherche2)";
        $params = [
            ':recherche1' => '%' . $ville_recherchee . '%',
            ':recherche2' => '%' . $ville_recherchee . '%'
        ];
        
        // FILTRE BUDGET
        if ($budget_max) {
            $sql .= " AND prix_journalier <= :budget";
            $params[':budget'] = $budget_max;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

    } else {
        $sql = "SELECT * FROM destination WHERE ville LIKE :ville OR pays LIKE :pays";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ville' => '%' . $ville_recherchee . '%', ':pays' => '%' . $ville_recherchee . '%']);
        $resultats = $stmt->fetchAll();
    }
} catch (\PDOException $e) {
    die("Erreur lors de la recherche : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .search-recap { display: inline-flex; flex-wrap: wrap; justify-content: center; gap: 20px; background: white; padding: 15px 25px; border-radius: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; color: #555; }
        .recap-item i { color: #007BFF; margin-right: 5px; }
        
        .result-card { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.3s; }
        .result-card:hover { transform: translateY(-5px); }
        .card-img { height: 200px; background: #eee; }
        .card-img img { width: 100%; height: 100%; object-fit: cover; }
        .card-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .card-title { margin: 0 0 10px 0; font-size: 1.2em; color: #333; }
        .card-location { color: #666; font-size: 0.9em; margin-bottom: 15px; }
        .card-price { font-size: 1.3em; font-weight: bold; color: #28a745; margin-top: auto; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        
        .flight-ticket { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; padding: 20px; margin-bottom: 20px; border-left: 5px solid #007BFF; }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    
    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 10px 30px; display: flex; justify-content: space-between; align-items: center;">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        <div class="user-actions" style="display: flex; gap: 15px; align-items: center;">
            <a href="index.php" class="btn-outline" style="text-decoration: none; color: #007BFF; border: 1px solid #007BFF; padding: 10px 20px; border-radius: 5px;">Nouvelle recherche</a>
            <a href="panier.php" class="btn-primary panier-icon" style="text-decoration: none; color: white; background: #007BFF; padding: 10px 20px; border-radius: 5px;"><i class="fa-solid fa-shopping-cart"></i></a>
        </div>
    </header>

    <main class="content-layout" style="margin-top: 40px; max-width: 1000px; margin-left: auto; margin-right: auto; padding: 0 20px;">
        <h2 style="text-align: center; margin-bottom: 10px; color: #333;">Résultats pour : "<?= htmlspecialchars($ville_recherchee) ?>"</h2>
        
        <div style="text-align: center;">
            <div class="search-recap">
                <span class="recap-item"><i class="fa-solid fa-tag"></i> <strong><?= ucfirst(htmlspecialchars($type_recherche)) ?></strong></span>
                <?php if(!empty($depart)): ?><span class="recap-item"><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y', strtotime($depart)) ?></span><?php endif; ?>
                <?php if($type_recherche !== 'vehicules'): ?><span class="recap-item"><i class="fa-solid fa-user-group"></i> <?= htmlspecialchars($voyageurs) ?> pers.</span><?php endif; ?>
                <?php if($budget_max): ?><span class="recap-item"><i class="fa-solid fa-wallet"></i> Max <?= htmlspecialchars($budget_max) ?> €</span><?php endif; ?>
            </div>
        </div>
        
        <div>
            <?php if(count($resultats) > 0): ?>
                
                <?php if($type_recherche === 'vols'): ?>
                    <?php foreach($resultats as $vol): ?>
                        <div class="flight-ticket">
                            <div style="font-weight: bold; color: #555;"><i class="fa-solid fa-plane-departure" style="font-size: 1.5em; color: #007BFF;"></i><br><?= htmlspecialchars($vol['compagnie_aerienne']) ?></div>
                            <div style="text-align: center;"><h3><?= date('H:i', strtotime($vol['datedepart'])) ?></h3><?= htmlspecialchars($vol['aeroport_depart']) ?></div>
                            <div style="color: #ccc; font-size: 1.5em;"><i class="fa-solid fa-arrow-right-long"></i></div>
                            <div style="text-align: center;"><h3><?= date('H:i', strtotime($vol['datearrivee'])) ?></h3><?= htmlspecialchars($vol['aeroport_arrivee']) ?></div>
                            <div style="text-align: right;">
                                <h3 style="margin: 0; color: #28a745;"><?= htmlspecialchars($vol['prix']) ?> €</h3>
                                <p style="margin: 5px 0 10px; font-size: 0.8em; color: #888;">Dispo: <?= htmlspecialchars($vol['places_disponible']) ?></p>
                                
                                <form action="panier.php" method="POST">
                                    <input type="hidden" name="action" value="ajouter">
                                    <input type="hidden" name="id_destination" value="VOL-<?= $vol['id_vol'] ?>">
                                    <input type="hidden" name="nom_ville" value="Vol vers <?= htmlspecialchars($vol['aeroport_arrivee']) ?>">
                                    <input type="hidden" name="date_debut" value="<?= date('Y-m-d', strtotime($vol['datedepart'])) ?>">
                                    <input type="hidden" name="date_fin" value="<?= date('Y-m-d', strtotime($vol['datearrivee'])) ?>">
                                    <input type="hidden" name="voyageurs" value="<?= htmlspecialchars($voyageurs) ?>">
                                    <input type="hidden" name="prix_estime" value="<?= htmlspecialchars($vol['prix']) ?>">
                                    <button type="submit" class="btn-primary" style="background-color: #28a745; border: none; padding: 10px 20px; cursor: pointer; color: white; border-radius: 5px;">Sélectionner</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                
                <?php elseif($type_recherche === 'vehicules'): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach($resultats as $v): ?>
                        <div class="result-card">
                            <div class="card-img" style="background: #333; display:flex; justify-content:center; align-items:center;">
                                <i class="fa-solid fa-car-side" style="font-size: 5em; color: white;"></i>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title"><?= htmlspecialchars($v['marque']) ?> - <?= htmlspecialchars($v['type_vehicule']) ?></h3>
                                <p class="card-location"><i class="fa-solid fa-gas-pump"></i> <?= htmlspecialchars($v['motorisation']) ?> | <i class="fa-solid fa-users"></i> <?= $v['nombres_places'] ?> places</p>
                                
                                <div class="card-price" style="flex-direction: column; align-items: stretch; border-top: 1px solid #eee; margin-top: auto;">
                                    <div style="display:flex; justify-content:space-between; margin: 15px 0;">
                                        <span style="color: #28a745;"><?= htmlspecialchars($v['prix_journalier']) ?> € / jour</span>
                                    </div>
                                    
                                    <form action="panier.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="action" value="ajouter">
                                        <input type="hidden" name="id_destination" value="VEH-<?= $v['id_vehicule'] ?>">
                                        <input type="hidden" name="nom_ville" value="<?= htmlspecialchars($v['marque'] . ' ' . $v['type_vehicule']) ?>">
                                        <input type="hidden" name="date_debut" value="<?= htmlspecialchars($depart) ?>">
                                        <input type="hidden" name="date_fin" value="<?= htmlspecialchars($retour) ?>">
                                        <input type="hidden" name="voyageurs" value="1">
                                        <input type="hidden" name="prix_estime" value="<?= htmlspecialchars($v['prix_journalier']) ?>">
                                        <button type="submit" class="btn-primary" style="width: 100%; text-align: center; border: none; cursor:pointer; background-color:#007BFF; padding:10px; color:white; border-radius:5px;">
                                            Sélectionner
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach($resultats as $item): 
                        $titre = $item['nom'] ?? $item['nom_hebergement'] ?? $item['ville'] ?? 'Sans nom';
                        $prix = $item['prix'] ?? $item['prix_par_nuit'] ?? null;
                    ?>
                        <div class="result-card">
                            <div class="card-img">
                                <img src="image/<?= htmlspecialchars($item['image_illustration'] ?? 'default.jpg') ?>" onerror="this.src='image/default.jpg'">
                            </div>
                            <div class="card-body">
                                <h3 class="card-title"><?= htmlspecialchars($titre) ?></h3>
                                <div class="card-location"><i class="fa-solid fa-map-pin"></i> <?= htmlspecialchars($item['ville'] ?? '') ?>, <?= htmlspecialchars($item['pays'] ?? '') ?></div>
                                <p style="font-size: 0.9em; color: #555;"><?= htmlspecialchars(substr($item['description_courte'] ?? $item['description'] ?? '', 0, 80)) ?>...</p>
                                
                                <div class="card-price">
                                    <span><?= $prix ? $prix . ' €' : 'Prix sur demande' ?></span>
                                    
                                    <?php
                                        if ($type_recherche === 'hotels') {
                                            $lien = "details_hebergement.php?id=" . ($item['id_hebergement'] ?? '');
                                        } elseif ($type_recherche === 'activites') {
                                            $lien = "details_activite.php?id=" . ($item['id_activite'] ?? '');
                                        } else {
                                            $lien = "details_offre.php?id=" . ($item['id_destination'] ?? '');
                                        }
                                    ?>
                                    <a href="<?= $lien ?>" class="btn-primary" style="padding: 5px 15px; text-decoration:none; color:white; background:#007BFF; border-radius:5px;">Voir plus</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="text-align: center; padding: 50px; background: white; border-radius: 10px;">
                    <i class="fa-solid <?= $type_recherche === 'vols' ? 'fa-plane-slash' : ($type_recherche === 'vehicules' ? 'fa-car-burst' : 'fa-magnifying-glass-location') ?>" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #333;">Oups, aucun résultat ne correspond à vos critères !</h3>
                    <p style="color: #666;">Essayez d'augmenter votre budget ou de changer de destination.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>