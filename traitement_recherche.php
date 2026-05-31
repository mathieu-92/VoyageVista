<?php
session_start();
require_once 'config.php';

// 1. Récupération des filtres
$ville_recherchee = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$type_recherche = isset($_GET['type_recherche']) ? $_GET['type_recherche'] : 'vols';
$depart = isset($_GET['depart']) ? $_GET['depart'] : '';
$voyageurs = isset($_GET['voyageurs']) ? (int)$_GET['voyageurs'] : 1;

$resultats = [];

try {
    // 2. Aiguillage selon le type de recherche
    if ($type_recherche === 'vols') {
        // RECHERCHE DE VOLS (Adapté à ta table 'vol')
        $sql = "SELECT * FROM vol WHERE aeroport_arrivee LIKE :recherche AND places_disponible >= :voyageurs";
        
        // Si une date de départ est saisie, on filtre aussi par date 
        // (On utilise DATE() car ta colonne datedepart contient aussi l'heure)
        if (!empty($depart)) {
            $sql .= " AND DATE(datedepart) = :date_depart";
        }
        
        $stmt = $pdo->prepare($sql);
        $params = [
            ':recherche' => '%' . $ville_recherchee . '%',
            ':voyageurs' => $voyageurs
        ];
        if (!empty($depart)) {
            $params[':date_depart'] = $depart;
        }
        
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

    } else {
        // RECHERCHE DE DESTINATIONS CLASSIQUE
        if (!empty($ville_recherchee)) {
            $sql = "SELECT * FROM destination WHERE ville LIKE :recherche OR pays LIKE :recherche";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':recherche' => '%' . $ville_recherchee . '%']);
            $resultats = $stmt->fetchAll();
        }
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
        .search-recap { display: inline-flex; gap: 20px; background: white; padding: 15px 25px; border-radius: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; color: #555; }
        .recap-item i { color: #007BFF; margin-right: 5px; }
        
        /* Design des billets d'avion */
        .flight-ticket { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; padding: 20px; margin-bottom: 20px; border-left: 5px solid #007BFF; }
        .flight-time { font-size: 1.5em; font-weight: bold; color: #333; }
        .flight-city { color: #666; font-size: 0.9em; }
        .flight-airline { display: flex; align-items: center; gap: 10px; color: #555; font-weight: bold; }
        .flight-price { text-align: right; }
        .flight-price h3 { margin: 0; color: #28a745; font-size: 1.8em; }
        .flight-price p { margin: 5px 0 10px 0; color: #888; font-size: 0.85em; }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    
    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <div class="user-profile-menu">
                    <div class="profile-trigger" style="color: #333;">
                        <i class="fa-solid fa-user-circle"></i> <span><?= htmlspecialchars($_SESSION['prenom']) ?></span>
                    </div>
                </div>
                <a href="panier.php" class="btn-primary panier-icon"><i class="fa-solid fa-shopping-cart"></i></a>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline" style="color: #007BFF; border-color: #007BFF;">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content-layout" style="margin-top: 40px; max-width: 1000px; margin-left: auto; margin-right: auto;">
        <h2 style="text-align: center; margin-bottom: 10px; color: #333;">Résultats pour : "<?= htmlspecialchars($ville_recherchee) ?>"</h2>
        
        <div style="text-align: center;">
            <div class="search-recap">
                <span class="recap-item"><i class="fa-solid fa-tag"></i> <strong><?= ucfirst(htmlspecialchars($type_recherche)) ?></strong></span>
                <?php if(!empty($depart)): ?><span class="recap-item"><i class="fa-regular fa-calendar"></i> Départ : <?= date('d/m/Y', strtotime($depart)) ?></span><?php endif; ?>
                <span class="recap-item"><i class="fa-solid fa-user-group"></i> <?= htmlspecialchars($voyageurs) ?> passager(s)</span>
            </div>
        </div>
        
        <div>
            <?php if(count($resultats) > 0): ?>
                
                <?php if($type_recherche === 'vols'): ?>
                    <?php foreach($resultats as $vol): ?>
                        <div class="flight-ticket">
                            <div class="flight-airline">
                                <i class="fa-solid fa-plane-departure" style="font-size: 2em; color: #007BFF;"></i>
                                <span><?= htmlspecialchars($vol['compagnie_aerienne']) ?></span>
                            </div>
                            
                            <div style="text-align: center;">
                                <div class="flight-time"><?= date('H:i', strtotime($vol['datedepart'])) ?></div>
                                <div class="flight-city"><?= htmlspecialchars($vol['aeroport_depart']) ?></div>
                            </div>
                            
                            <div style="color: #ccc; font-size: 1.5em;"><i class="fa-solid fa-arrow-right-long"></i></div>
                            
                            <div style="text-align: center;">
                                <div class="flight-time"><?= date('H:i', strtotime($vol['datearrivee'])) ?></div>
                                <div class="flight-city"><?= htmlspecialchars($vol['aeroport_arrivee']) ?></div>
                            </div>
                            
                            <div class="flight-price">
                                <h3><?= htmlspecialchars($vol['prix']) ?> €</h3>
                                <p><i class="fa-solid fa-couch"></i> <?= htmlspecialchars($vol['places_disponible']) ?> places restantes</p>
                                
                                <form action="panier.php" method="POST">
                                    <input type="hidden" name="action" value="ajouter">
                                    <input type="hidden" name="id_destination" value="VOL-<?= $vol['id_vol'] ?>">
                                    <input type="hidden" name="nom_ville" value="Vol pour <?= htmlspecialchars($vol['aeroport_arrivee']) ?>">
                                    <input type="hidden" name="date_debut" value="<?= date('Y-m-d', strtotime($vol['datedepart'])) ?>">
                                    <input type="hidden" name="date_fin" value="<?= date('Y-m-d', strtotime($vol['datearrivee'])) ?>">
                                    <input type="hidden" name="voyageurs" value="<?= htmlspecialchars($voyageurs) ?>">
                                    <input type="hidden" name="prix_estime" value="<?= htmlspecialchars($vol['prix']) ?>">
                                    <button type="submit" class="btn-primary" style="background-color: #28a745; border: none; padding: 10px 20px;">Sélectionner</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                
                <?php else: ?>
                    <div class="grid-results">
                    <?php foreach($resultats as $dest): ?>
                        <div class="card">
                            <img src="image/<?= htmlspecialchars($dest['image_illustration']) ?>" alt="<?= htmlspecialchars($dest['ville']) ?>">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($dest['ville']) ?></h3>
                                <p class="country"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($dest['pays']) ?></p>
                                <div class="card-footer">
                                    <span class="price">Dès <strong>299€</strong></span>
                                    <a href="details_offre.php?id=<?= $dest['id_destination'] ?>" class="btn-primary">Découvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="text-align: center; padding: 50px; background: white; border-radius: 10px;">
                    <i class="fa-solid <?= $type_recherche === 'vols' ? 'fa-plane-slash' : 'fa-magnifying-glass-location' ?>" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #333;">Aucun résultat trouvé</h3>
                    <p style="color: #666;">Modifiez vos dates ou la destination.</p>
                    <a href="index.php" class="btn-primary" style="text-decoration: none; margin-top: 15px; display: inline-block;">Retour à l'accueil</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>