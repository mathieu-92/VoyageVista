<?php
session_start();
require_once 'config.php';

// 1. On récupère TOUTES les valeurs saisies dans le nouveau formulaire
$ville_recherchee = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$type_recherche = isset($_GET['type_recherche']) ? $_GET['type_recherche'] : 'vols';
$depart = isset($_GET['depart']) ? $_GET['depart'] : '';
$retour = isset($_GET['retour']) ? $_GET['retour'] : '';
$voyageurs = isset($_GET['voyageurs']) ? $_GET['voyageurs'] : 1;

$resultats = [];

try {
    // 2. Requête préparée pour chercher la ville (LIKE permet de trouver des résultats partiels)
    // Amélioration : on cherche maintenant dans "ville" MAIS AUSSI dans "pays"
    if (!empty($ville_recherchee)) {
        $sql = "SELECT * FROM destination WHERE ville LIKE :recherche OR pays LIKE :recherche";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':recherche' => '%' . $ville_recherchee . '%']);
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
    <title>Résultats de recherche - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .search-recap {
            display: inline-flex;
            gap: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            font-size: 0.9em;
            color: #555;
        }
        .recap-item i { color: #007BFF; margin-right: 5px; }
    </style>
</head>
<body class="auth-page">
    
    <header class="top-nav">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 70px;"></a>
        </div>
        
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <div class="user-profile-menu">
                    <div class="profile-trigger">
                        <i class="fa-solid fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['prenom']) ?></span>
                    </div>
                </div>
                <a href="panier.php" class="btn-primary panier-icon">
                    <i class="fa-solid fa-shopping-cart"></i>
                </a>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
                <a href="inscription.php" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content-layout" style="margin-top: 40px;">
        <h2 style="text-align: center; margin-bottom: 10px;">Résultats pour : "<?= htmlspecialchars($ville_recherchee) ?>"</h2>
        
        <div style="text-align: center;">
            <div class="search-recap">
                <span class="recap-item">
                    <i class="fa-solid fa-tag"></i> 
                    Catégorie : <strong><?= ucfirst(htmlspecialchars($type_recherche)) ?></strong>
                </span>
                <?php if(!empty($depart)): ?>
                    <span class="recap-item">
                        <i class="fa-regular fa-calendar"></i> Départ : <?= date('d/m/Y', strtotime($depart)) ?>
                    </span>
                <?php endif; ?>
                <?php if($type_recherche !== 'vols'): ?>
                    <span class="recap-item">
                        <i class="fa-solid fa-user-group"></i> <?= htmlspecialchars($voyageurs) ?> voyageur(s)
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="grid-results">
            <?php if(count($resultats) > 0): ?>
                <?php foreach($resultats as $dest): ?>
                    <div class="card">
                        <img src="image/<?= htmlspecialchars($dest['image_illustration']) ?>" alt="<?= htmlspecialchars($dest['ville']) ?>">
                        <div class="card-content">
                            <h3><?= htmlspecialchars($dest['ville']) ?></h3>
                            <p class="country"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($dest['pays']) ?></p>
                            <p><?= htmlspecialchars($dest['description_courte']) ?></p>
                            
                            <div class="card-footer">
                                <span class="price">Dès <strong>299€</strong></span>
                                <a href="details_offre.php?id=<?= $dest['id_destination'] ?>" class="btn-primary">Découvrir</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; width: 100%; grid-column: 1 / -1; padding: 40px;">
                    <i class="fa-solid fa-plane-slash" style="font-size: 3em; color: #ccc; margin-bottom: 15px;"></i>
                    <p>Aucune destination trouvée pour votre recherche.</p>
                    <a href="index.php" class="btn-primary" style="text-decoration: none; display: inline-block; margin-top: 15px;">Retour à l'accueil</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>